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

<div class="catalog-letters">

     <?php
    $letters = range('A', 'Z');
    $language = JFactory::getLanguage();
    $language_tag = $language->getTag();

	if($language_tag=="el-GR") {
		$letters=array('Α','Β','Γ','Δ','Ε','Ζ','Η','Θ','Ι','Κ','Λ','Μ','Ν','Ξ','Ο','Π','Ρ','Σ','Τ','Υ','Φ','Χ','Ψ','Ω');
	} else if($language_tag=="es-ES") { 
		$letters=array('A','B','C','Ch','D','E','F','G','H','I','J','K','L','LL','M','N','Ñ','O','P','Q','R',' S','T','U','V','W','X','Y','Z');
	} else if($language_tag=="ru-RU") { 
		$letters=array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','ъ','Ы','ь','Э','Ю','Я');
	} ?> 
	
    <a href="javascript:chooseLetter('[x]')">
        <span class="<?php echo $this->letter=='[x]'? 'letter-selected':'' ?>">#</span>
    </a>

    <a href="javascript:chooseLetter('[0-9]')">
        <span class="<?php echo strtoupper($this->letter)=='[0-9]'? 'letter-selected':'' ?>">0-9</span>
    </a>

    <?php foreach($letters as $i) { ?>
        <a href="javascript:chooseLetter('<?php echo $i ?>')">
            <?php
            $class="no-class";
            if(strtoupper($this->letter) == $i) {
                $class='letter-selected ';
            }

            if(isset($this->letters[$i])){
                $class.=" used-letter";
            } ?>

            <span class="<?php echo $class ?>"><?php echo $i ?> </span>
        </a>
    <?php } ?>

    <a href="javascript:chooseLetter('')">
        <span class="<?php echo empty($this->letter)?'letter-selected':'' ?>"> <?php echo JText::_('LNG_ALL')?></span>
    </a>
</div>

<script>

     function chooseLetter(letter){
         jQuery("#adminForm  #letter").val(letter);
         jQuery("#adminForm").submit();
     }       
</script>