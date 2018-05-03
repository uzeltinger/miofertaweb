<?php 
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

class WireTransfer implements IPaymentProcessor {
	
	var $type;
	var $name;
	
	public function initialize($data){
		$this->type =  $data->type;
		$this->name =  $data->name;
		$this->mode = $data->mode;
		if(isset($data->fields))
			$this->fields = $data->fields;
	}
	
	public function getPaymentGatewayUrl(){
	
	}
	
	public function getPaymentProcessorHtml(){
		$html ="<ul id=\"payment_form_$this->type\" style=\"display:none\" class=\"form-list\">
		<li>
		".JText::_('LNG_WIRE_TRANSFER_INFO',true)."
				</li>
				</ul>";
		
		return $html;
	}
	
	public function getHtmlFields() {
		$html  = '';
		return $html;
	}
	
	public function processTransaction($data, $controller = "payment"){
		$result = new stdClass();
		$result->transaction_id = 0;
		$result->amount = $data->amount;
		$result->payment_date = date("Y-m-d");
		$result->response_code = 0;
		$result->order_id = $data->id;
		$result->currency=  $data->currency;
		$result->processor_type = $this->type;
		$result->status = PAYMENT_WAITING;
		$result->payment_status = PAYMENT_STATUS_WAITING;

		return $result;
	}
	
	public function processResponse($data){
		$result = new stdClass();
			
		return $result;
	}

	public function getPaymentDetails($paymentDetails){
		$result = "";
		
		ob_start();
		?>

		<TABLE>
			<?php
			if(!empty($this->fields))
				foreach($this->fields as $column=>$value){?>
					<TR>
						<TD align=left width=40% nowrap>
							<b><?php echo JText::_('LNG_'.strtoupper($column),true);?> :</b>
						</TD>
						<TD>
							<?php echo $value?>
						</TD>
					</TR>
				<?php } ?>
		</TABLE>

		<?php
		$result = $result.ob_get_contents();
		
		ob_end_clean();
		
		return $result;
	}
}