<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

define('SIMPLESHOP_CONFIGURATION', 'config/haromszereplosshop.conf');
require_once JPATH_COMPONENT_SITE.'/classes/payment/processors/OTP/lib/iqsys/otpwebshop/WebShopService.php';
require_once JPATH_COMPONENT_SITE.'/classes/payment/processors/OTP/lib/iqsys/otpwebshop/RequestUtils.php';
require_once JPATH_COMPONENT_SITE.'/classes/payment/processors/OTP/lib/iqsys/otpwebshop/ConfigUtils.php';


class OTP implements iPaymentProcessor
{
    var $type;
    var $name;
    var $mode;

    var $paymentUrl = 'https://www.otpbankdirekt.hu/webshop/do/webShopVasarlasInditas?posId={0}&azonosito={1}&nyelvkod={2}';

    var $notifyUrl;
    var $returnUrl;
    var $cancelUrl;

    var $shopId;
    var $registrationId;
    var $transactionId;
    var $currencyCode;
    var $amount;

    public function initialize($data)
    {
        $this->type =  $data->type;
        $this->name =  $data->name;
        $this->mode = $data->mode;
        $this->shopId = $data->shop_id;
        if($this->mode == "test")
            $this->shopId = "#02299991";
        $this->registrationId = $data->registration_id;
    }


    public function getPaymentGatewayUrl()
    {
        if($this->mode=="test"){
            $paymentUrl =  $this->paymentUrl;
        }else{
            $paymentUrl = $this->paymentUrl;
        }

        $paymentUrl = ConfigUtils::substConfigValue($paymentUrl,
            array("0" => urlencode($this->shopId),
                "1" => urlencode($this->transactionId)));
    }

    public function getPaymentProcessorHtml()
    {
        $html ="<ul id=\"payment_form_$this->type\" style=\"display:none\" class=\"form-list\">
		<li>
		    ".JText::_('LNG_PROCESSOR_OTP_INFO',true)."
		    </li>
		</ul>";

        return $html;
    }

    public function getHtmlFields()
    {
        return '';
    }

    public function processTransaction($data, $controller = "payment")
    {
        $this->returnUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.processResponse&processor=otp",false,-1);
        $this->notifyUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.procesAutomaticResponse&processor=otp",false,-1);
        $this->cancelUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.processCancelResponse",false,-1);;
        $this->amount = $data->amount;
        $this->currencyCode = $data->currency;

        $service = new WebShopService();
        $tranzAzon = '';
        if (is_null($tranzAzon) || (trim($tranzAzon) == "")) {
            $tranzAzonResponse =  $service->tranzakcioAzonositoGeneralas($this->shopId);
            if ($tranzAzonResponse->hasSuccessfulAnswer) {
                $tranzAzon = $tranzAzonResponse->answerModel->getAzonosito();
            }
        }
        $this->transactionId = $tranzAzon;

        $temp = new stdClass();
        $temp->shopId = $this->shopId;
        $temp->currencyCode = $this->currencyCode;
        $temp->registrationId = $this->registrationId;
        $temp->returnUrl = $this->returnUrl;
        $response = processOtpTransaction($temp);

        dump($response);

        $result = new stdClass();
        $result->amount =  $data->amount;
        $result->payment_date = date("Y-m-d");
        $result->response_code = 0;
        $result->order_id = $data->id;
        $result->currency=  $data->currency;
        $result->processor_type = $this->type;
        $result->status = PAYMENT_REDIRECT;
        $result->payment_status = PAYMENT_STATUS_PENDING;

        return $result;
    }


    public function processResponse($data)
    {
        $result = new stdClass();


        $result->status = PAYMENT_SUCCESS;
        $result->payment_status = PAYMENT_STATUS_PAID;

        return $result;
    }

    public function getPaymentDetails($paymentDetails)
    {
        return JText::_('LNG_PROCESSOR_OTP',true);
    }

    function processOtpTransaction($data)
    {
        $service = new WebShopService();

        // Paraméterek összegyûjtése a kérésbõl
        $posId = $data->shopId;
        $tranzAzon = $data->transactionId;
        $osszeg = $data->amount;
        $devizanem = $data->currencyCode;
        $zsebAzonosito = $data->registrationId;
        $backUrl = $data->returnUrl;

        // BackURL manipláció
        if (!is_null($backUrl) && trim($backUrl) != '') {
                $backUrl =
                    RequestUtils::addUrlQuery($backUrl,
                        array('fizetesValasz' => 'true',
                            'posId' => $posId,
                            'tranzakcioAzonosito' => $tranzAzon));
        }

        syslog(LOG_NOTICE, "Haromszereplos fizetes keres kuldes: " . $posId . " - " . $tranzAzon);

        //global $response;
        // Fizetési tranzakció elindítása
        $response = $service->fizetesiTranzakcio(
            $posId,
            $tranzAzon,
            $osszeg,
            $devizanem,
            $zsebAzonosito,
            $backUrl);

        if ($response) {
            syslog(LOG_NOTICE, "Haromszereplos fizetes keres kuldes: " . $posId . " - " . $tranzAzon . " - " . implode($response->getMessages()));
        }
        else {
            syslog(LOG_ERR, "Haromszereplos fizetes keres kuldes: " . $posId . " - " . $tranzAzon . " - NEM ERTELMEZHETO VALASZ!");
        }

        return $response;
    }

}