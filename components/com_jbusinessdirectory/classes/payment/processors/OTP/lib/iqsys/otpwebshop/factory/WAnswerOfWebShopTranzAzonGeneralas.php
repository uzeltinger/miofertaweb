<?php

if (!defined('WEBSHOP_LIB_DIR')) define('WEBSHOP_LIB_DIR', dirname(dirname(dirname( __FILE__ ))));

require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/model/WebShopTranzAzon.php';
require_once WEBSHOP_LIB_DIR . '/iqsys/otpwebshop/util/WebShopXmlUtils.php';

/**
* A fizetési tranzakció azonosító generálás szolgáltatás 
* válasz XML-jének feldolgozásása és a megfelelõ value object elõállítása.
* 
* @version 4.0
*/
class WAnswerOfWebShopTranzAzonGeneralas {

    /**
    * A fizetési tranzakció azonosító generálás szolgáltatás 
    * válasz XML-jének feldolgozásása és a megfelelõ value object elõállítása.
    * 
    * @param DomDocument $answer A tranzakciós válasz xml
    * @return WebShopTranzAzon a válasz tartalma, 
    *         vagy NULL üres/hibás válasz esetén
    */
    function load($answer) {
        $webShopTranzAzon = null;

        $record = WebShopXmlUtils::getNodeByXPath($answer, '//answer/resultset/record');
        if (!is_null($record)) {
            $webShopTranzAzon = new WebShopTranzAzon();
            $webShopTranzAzon->setAzonosito(WebShopXmlUtils::getElementText($record, "id"));
            $webShopTranzAzon->setPosId(WebShopXmlUtils::getElementText($record, "posid"));
            $webShopTranzAzon->setTeljesites(WebShopXmlUtils::getElementText($record, "timestamp"));
        }
        
        return $webShopTranzAzon;
    }

}

?>