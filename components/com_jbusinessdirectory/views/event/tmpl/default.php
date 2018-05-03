<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_SITE.'/classes/attributes/attributeservice.php';
?>

<?php
if($this->appSettings->event_view==1){
    echo $this->loadTemplate('style_1');
}else{
    echo $this->loadTemplate('style_2');
}
?>

<?php
jimport('joomla.application.module.helper');
// this is where you want to load your module position
$modules = JModuleHelper::getModules('dir-event');

if(isset($modules) && count($modules)>0){
    $fullWidth = false; ?>
    <div class="dir-event">
        <?php foreach($modules as $module) {
            echo JModuleHelper::renderModule($module);
        } ?>
    </div>
<?php }
?>

<script>
// starting the script on page load
jQuery(document).ready(function(){
	jQuery("img.image-prv").click(function(e){
		jQuery("#image-preview").attr('src', this.src);	
	});

	jQuery(".chosen-select").chosen({width:"95%"});
});
</script>