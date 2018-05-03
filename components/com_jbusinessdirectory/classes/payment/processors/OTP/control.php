<?php

define('SIMPLESHOP_CONFIGURATION', 'config/haromszereplosshop.conf');

require_once JPATH_COMPONENT_SITE.'/classes/payment/processors/OTP/lib/iqsys/otpwebshop/WebShopService.php';
require_once JPATH_COMPONENT_SITE.'/classes/payment/processors/OTP/lib/iqsys/otpwebshop/RequestUtils.php';
require_once JPATH_COMPONENT_SITE.'/classes/payment/processors/OTP/lib/iqsys/otpwebshop/ConfigUtils.php';

function processOtpTransaction($data)
{
    ob_start();

    $config = parse_ini_file('OTP/config/haromszereplosshop.conf');
    $service = new WebShopService();

    // Paraméterek összegyûjtése a kérésbõl
    $posId = $data->shopId;
    $tranzAzon = $data->transactionId;
    $osszeg = $data->amount;
    $devizanem = $data->currencyCode;
    $zsebAzonosito = $data->registrationId;
    $backUrl = $data->returnUrl;

    if (is_null($tranzAzon) || (trim($tranzAzon) == "")) {
        $tranzAzonResponse =  $service->tranzakcioAzonositoGeneralas($posId);
        if ($tranzAzonResponse->hasSuccessfulAnswer) {
            $tranzAzon = $tranzAzonResponse->answerModel->getAzonosito();
        }
    }

    if (is_null($tranzAzon) || (trim($tranzAzon) == "")) {
        processDirectedToBackUrl($data);
        return;
    }

    // Ügyfél átirányítása a vevõ oldali felületre
    $custPageTemplate = $config['webshop_customerpage_url'];
    $custPageTemplate = ConfigUtils::substConfigValue($custPageTemplate,
        array("0" => urlencode($posId),
            "1" => urlencode($tranzAzon)));

    header("Connection: close");
    header("Location: " . $custPageTemplate);
    header("Content-Length: " . ob_get_length());
    ob_end_flush();
    flush();

    // BackURL manipláció
    if (!is_null($backUrl) && trim($backUrl) != '') {
        if (ConfigUtils::getConfigParamBool($config, 'append_trandata_to_backurl', $posId, true)) {
            $backUrl =
                RequestUtils::addUrlQuery($backUrl,
                    array('fizetesValasz' => 'true',
                        'posId' => $posId,
                        'tranzakcioAzonosito' => $tranzAzon));
        }
    }

    syslog(LOG_NOTICE, "Haromszereplos fizetes keres kuldes: " . $posId . " - " . $tranzAzon);

    global $response;
    // Fizetési tranzakció elindítása
    $response = $service->fizetesiTranzakcio(
        $posId,
        $tranzAzon,
        $osszeg,
        $devizanem,
        $zsebAzonosito,
        $backUrl);

    /*********
    Itt a helye a shop-specifikus eredmény feldolgozásnak / tárolásnak
     ********/

    if ($response) {
        syslog(LOG_NOTICE, "Haromszereplos fizetes keres kuldes: " . $posId . " - " . $tranzAzon . " - " . implode($response->getMessages()));
    }
    else {
        syslog(LOG_ERR, "Haromszereplos fizetes keres kuldes: " . $posId . " - " . $tranzAzon . " - NEM ERTELMEZHETO VALASZ!");
    }

    return $response;
}

function processDirectedToBackUrl($data, $doRedirect = true) {

    $posId = $data->shopId;
    $tranzAzon = $data->transactionId;

    $config = parse_ini_file(SIMPLESHOP_CONFIGURATION);

    $successAnswerPage =
        ConfigUtils::getConfigParam($config, 'webshop_success_answerpage_url', $posId);
    $cancelledAnswerPage =
        ConfigUtils::getConfigParam($config, 'webshop_cancelled_answerpage_url', $posId);
    $failedAnswerPage =
        ConfigUtils::getConfigParam($config, 'webshop_failed_answerpage_url', $posId);
    $unknownAnswerPage =
        ConfigUtils::getConfigParam($config, 'webshop_unknown_answerpage_url', $posId);

    global $tranzAdatok;

    if (!is_null($tranzAzon) && (trim($tranzAzon) != "")) {
        syslog(LOG_NOTICE, "Fizetes tranzakcio adat lekerdezes: " + $tranzAzon);

        // Lekérdezzük a fizetési tranzakció adatait.
        // A lekérdezett tranzakcióra definiálunk egy idõintervallumot is:
        // [aktuális idõpont - 24 óra ; aktuális idõpont + 24 óra]
        $service = new WebShopService();
        $response = $service->tranzakcioStatuszLekerdezes($posId, $tranzAzon, 1, time() - 60*60*24, time() + 60*60*24);

        if ($response) {

            $answer = $response->getAnswer();
            if ($response->isSuccessful()
                && $response->getAnswer()
                && count($answer->getWebShopFizetesAdatok()) > 0) {

                // Sikerült lekérdezni az adott tranzakció adatát
                $fizetesAdatok = $answer->getWebShopFizetesAdatok();
                $tranzAdatok = current($fizetesAdatok);
                $_REQUEST['tranzAdatok'] = $tranzAdatok;

                syslog(LOG_NOTICE, "Fizetes tranzakcio adat lekerdezes befejezve: " . $posId . " - " . $tranzAzon );

                $responseCode = $tranzAdatok->getPosValaszkod();

                $successPosResponseCodes = array(
                    "000", "00", "001", "002", "003", "004",
                    "005", "006", "007", "008", "009", "010");

                if ($tranzAdatok->isSuccessful()) {
                    // Az ügyfél megfelelõen kitöltötte és elküldte
                    // az adatait, a vásárlás vagy regisztrálás sikeres volt
                    $successAnswerPage = ConfigUtils::substConfigValue($successAnswerPage,
                        array("0" => urlencode($posId),
                            "1" => urlencode($tranzAzon),
                            "2" => urlencode($tranzAdatok->getAuthorizaciosKod())));
                    if ($doRedirect) RequestUtils::includeOrRedirect($successAnswerPage);
                }
                else if ("VISSZAUTASITOTTFIZETES" == $responseCode) {
                    // Az ügyfél elutasította (visszavonta) a vásárlást a vevõ oldali felületen
                    $cancelledAnswerPage = ConfigUtils::substConfigValue($cancelledAnswerPage,
                        array("0" => urlencode($posId),
                            "1" => urlencode($tranzAzon)));
                    if ($doRedirect) RequestUtils::includeOrRedirect($cancelledAnswerPage);
                }
                else {
                    // Az ügyfél kitöltötte és elküldte az adatait,
                    // de a tranzakció sikertelen volt.
                    // Valószínûleg a kártya terhelés nem végezhetõ el
                    $failedAnswerPage = ConfigUtils::substConfigValue($failedAnswerPage,
                        array("0" => urlencode($posId),
                            "1" => urlencode($tranzAzon),
                            "2" => urlencode($responseCode)));
                    if ($doRedirect) RequestUtils::includeOrRedirect($failedAnswerPage);
                }
            }
            else {
                // Ha nem sikerült lekérdezni a választ...
                $unknownAnswerPage = ConfigUtils::substConfigValue($unknownAnswerPage,
                    array("0" => urlencode($posId),
                        "1" => urlencode($tranzAzon)));
                if ($doRedirect) RequestUtils::includeOrRedirect($unknownAnswerPage);
            }
        }
        else {
            // Ha nem sikerült lekérdezni a választ...
            $unknownAnswerPage = ConfigUtils::substConfigValue($unknownAnswerPage,
                array("0" => urlencode($posId),
                    "1" => urlencode($tranzAzon)));
            if ($doRedirect) RequestUtils::includeOrRedirect($failedAnswerPage);
        }
    }
    else {
        $unknownAnswerPage = ConfigUtils::substConfigValue($unknownAnswerPage,
            array("0" => urlencode($posId),
                "1" => urlencode($tranzAzon)));
        if ($doRedirect) RequestUtils::includeOrRedirect($unknownAnswerPage);
    }

    return $tranzAdatok;
}

?>