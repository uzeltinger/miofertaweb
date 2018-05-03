<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
?>

<style>
    div.ui-datepicker{
        font-size:12px;
    }

    ul li{
        list-style: none;
        list-style-type: none;
        list-style-position: initial;
        list-style-image: initial;
    }
</style>

<div id="wizard">
    <h3><i class="dir-icon-cog"></i> <?php echo JText::_('LNG_SELECT_SERVICE'); ?></h3>
    <section>
        <?php foreach($this->services as $service) { ?>
            <div class="row-fluid">
                <div class="span6">
                    <b><a href="javascript:void(0)" class="service-link" onclick="selectService(<?php echo $service->id ?>)"><?php echo $this->escape($service->name) ?></a></b>
                </div>
                <div class="span3">
                    <?php if($service->show_duration) { ?>
                        <p><?php echo JBusinessUtil::formatTimePeriod($service->duration, 1) ?></p>
                    <?php } ?>
                </div>
                <div class="span3">
                    <p><?php echo JBusinessUtil::getPriceFormat($service->price, $service->currency_id); ?></p>
                </div>
            </div>
            <div class="clear"></div>
            <hr/>
        <?php } ?>
    </section>
    <h3><i class="dir-icon-user"></i> <?php echo JText::_('LNG_SELECT_PROVIDER'); ?></h3>
    <section>
        <div id="providers-content">
        </div>

        <form style="display:none;" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'); ?>" method="post" name="serviceForm" id="service-form">
            <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
            <input type="hidden" name="task" id="task" value="serviceguestdetails.createBookingForm" />
            <input type="hidden" name="companyId" id="companyId" value="<?php echo $this->company->id ?>" />
            <input type="hidden" name="serviceId" id="serviceId" value="" />
            <input type="hidden" name="providerId" id="providerId" value="" />
            <input type="hidden" name="date" id="date" value="" />
            <input type="hidden" name="hour" id="hour" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </section>
    <h3><i class="dir-icon-calendar"></i> <?php echo JText::_('LNG_DATE_AND_TIME'); ?></h3>
    <section>
        <div class="row-fluid">
            <div class="span5" id="datepicker"></div>
            <div class="span7 available-hours" id="available-hours">
                <div class="row-fluid">
                    <div class="span3">
                        <b><?php echo JText::_('LNG_MORNING'); ?></b>
                        <ul id="morning" class="list-unstyled">

                        </ul>
                    </div>
                    <div class="span3">
                        <b><?php echo JText::_('LNG_AFTERNOON'); ?></b>
                        <ul id="afternoon" class="list-unstyled">

                        </ul>
                    </div>
                    <div class="span3">
                        <b><?php echo JText::_('LNG_EVENING'); ?></b>
                        <ul id="evening" class="list-unstyled">

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    var todayDate = new Date();
    var maxDate = new Date();
    maxDate.setMonth(todayDate.getMonth() + 6);

    var serviceId = 0;
    var providerId = 0;
    var selectedHour;
    var selectedDate;

    var wizard = jQuery("#wizard").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "slide",
        titleTemplate: "#title#",
        enablePagination: false,
        onStepChanging: function (event, currentIndex, newIndex) {
            if (serviceId == 0) {
                alert("<?php echo JText::_('LNG_PLEASE_SELECT_SERVICE'); ?>");
                return false;
            }

            if (newIndex == 1)
                renderProviders();
            else if (newIndex == 2) {
                if (providerId == 0) {
                    alert("<?php echo JText::_('LNG_PLEASE_SELECT_SERVICE'); ?>");
                    return false;
                }
                else
                    getVacationDates();
            }

            return true;
        },
        onFinishing: function (event, currentIndex) {
            jQuery('#serviceId').val(serviceId);
            jQuery('#providerId').val(providerId);
            jQuery('#date').val(selectedDate);
            jQuery('#hour').val(selectedHour);

            document.serviceForm.submit();
        }
    });
</script>