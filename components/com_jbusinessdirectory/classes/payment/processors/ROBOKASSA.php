<?php

/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

class ROBOKASSA implements IPaymentProcessor
{
    var $type;
    var $name;
    var $mode;

    private $mrh_login;
    private $mrh_pass1;
    private $mrh_pass2;

    var $out_summ;
    var $out_summ_curr;
    var $inv_desc;
    var $inv_id;
    var $crc;
    var $isTest;

    var $paymentURL = "https://merchant.roboxchange.com/Index.aspx";
    var $paymentURLTest = 'https://merchant.roboxchange.com/Index.aspx';

    // return URL
    var $successURL;
    // notify URL
    var $resultURL;
    // fail/cancel URL
    var $failURL;

    public function initialize($data)
    {
        $this->type = $data->type;
        $this->name = $data->name;
        $this->mode = $data->mode;
        $this->mrh_login = $data->merchant_login;
        $this->mrh_pass1 = $data->merchant_pass_1;
        $this->mrh_pass2 = $data->merchant_pass_2;
    }

    public function getPaymentGatewayUrl()
    {
        if($this->mode=="test" || $this->mode=="TEST"){
            $this->isTest = '1';
            return $this->paymentURLTest;
        }else{
            return $this->paymentURL;
        }
    }

    public function getHtmlFields()
    {
        $html  = '';
        $html .= sprintf('<input type="hidden" name="MrchLogin" id="MrchLogin" value="%s" />', $this->mrh_login);
        $html .= sprintf('<input type="hidden" name="OutSum" id="OutSum" value="%s" />', $this->out_summ);
        if($this->out_summ_curr != 'RUB')
            $html .= sprintf('<input type="hidden" name="OutSumCurrency" id="OutSumCurrency" value="%s" />', $this->out_summ_curr);
        $html .= sprintf('<input type="hidden" name="InvId" id="InvId" value="%d" />', $this->inv_id);
        $html .= sprintf('<input type="hidden" name="Desc" id="Desc" value="%s" />', $this->inv_desc);
        $html .= sprintf('<input type="hidden" name="SignatureValue" id="SignatureValue" value="%s" />', $this->crc);
        $html .= sprintf('<input type="hidden" name="isTest" id="isTest" value="%s" />', $this->isTest);

        return $html;
    }

    public function getPaymentProcessorHtml()
    {
        $html ="<ul id=\"payment_form_$this->type\" style=\"display:none\" class=\"form-list\">
		<li>
		    ".JText::_('LNG_PROCESSOR_ROBOKASSA_INFO',true)."
		    </li>
		</ul>";

        return $html;
    }

    public function processTransaction($data, $controller="payment")
    {
        // Note: For ROBOKASSA, success, result and fail URL-s are stored in the robokassa merchant account
        $this->successUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.processResponse&processor=robokassa",false,-1);
        $this->resultUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.procesAutomaticResponse&processor=robokassa",false,-1);
        $this->failUrl = JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.processCancelResponse",false,-1);;

        $this->out_summ = $data->amount;
        $this->inv_desc = $data->service." ".$data->description;
        $this->inv_id = $data->id;
        $this->out_summ_curr = $data->currency;

        $this->crc = md5("$this->mrh_login:$this->out_summ:$this->inv_id:$this->out_summ_curr:$this->mrh_pass1");
        if($this->out_summ_curr == 'RUB')
            $this->crc = md5("$this->mrh_login:$this->out_summ:$this->inv_id:$this->mrh_pass1");

        $result = new stdClass();
        $result->transaction_id = 0;
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
        $result->amount = $data["OutSum"];
        $result->order_id = $data["InvId"];
        $result->currency = isset($data["OutSumCurrency"])?$data["OutSumCurrency"]:'RUB';
        $result->payment_date = date("Y-m-d");
        $result->transaction_id = $result->order_id.strtotime($result->payment_date);
        $result->processor_type = $this->type;
        $result->response_code = '';
        $result->response_message = '';
        $result->processAutomatically = true;

        $checkSum = $data["SignatureValue"];
        $checkSum = strtoupper($checkSum);

        $local_checksum = strtoupper(md5("$result->amount:$result->order_id:$this->mrh_pass2"));

        // check signature
        if($checkSum == $local_checksum)
        {
            echo "OK".$result->order_id;
            $result->status = PAYMENT_SUCCESS;
            $result->payment_status = PAYMENT_STATUS_PAID;
        }
        else
        {
            $result->status = PAYMENT_SUCCESS;
            $result->payment_status = PAYMENT_STATUS_PAID;
        }


        return $result;
    }

    public function getPaymentDetails($paymentDetails)
    {
        return JText::_('LNG_PROCESSOR_ROBOKASSA',true);
    }

}