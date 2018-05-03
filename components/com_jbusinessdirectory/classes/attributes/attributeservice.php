<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
 
defined('_JEXEC') or die('Restricted access');

class AttributeService{

	public static function renderAttributes($attributes, $enablePackages, $packageFeatures) {
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$renderedContent="";
		$db =JFactory::getDBO();
		
		if(count($attributes)>0) {
			foreach ($attributes as $attribute) {
				$class = "";

				if ($attribute->is_mandatory == 1)
					$class = "validate[required]";

				if(!isset($attribute->attributeValue)){
				    $attribute->attributeValue ="";
				}
				$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);
					
				$hideClass= "";
				$app = JFactory::getApplication();
				if (!$app->isAdmin() && $attribute->only_for_admin){
					continue;
				}
				
				if (isset($packageFeatures) && in_array($attribute->code, $packageFeatures) || !$enablePackages)
				{
					$attributeOptions = explode(",", $attribute->options);
					if ($appSettings->enable_multilingual && isset($attribute->options))
					{
						foreach ($attributeOptions as $key => $option)
						{
							$attributeOptions[$key] = JBusinessDirectoryTranslations::getTranslatedItemName($option);
						}
					}
					switch ($attribute->attributeTypeCode)
					{
						case "header":
							$renderedContent .= '<div class="detail_box">';
							$renderedContent .= '<h3 title="' . $attribute->name . '">' . $attribute->name . '</h3>';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';
							break;
						case "input":
						    $inputValue = $attribute->attributeValue;

							if ($attribute->is_mandatory == 1)
								$class = "validate[required] text-input";

							$renderedContent .= '<div class="detail_box '.$hideClass.'">';
							if ($attribute->is_mandatory)
								$renderedContent .= '<div  class="form-detail req"></div>';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . '</label>';
							$renderedContent .= '<input type="text" maxLength="150" size="50" name="attribute_' . $attribute->id . '" id="attribute_' . $attribute->id . '" value="' . $inputValue . '"  class="input_txt ' . $class . '"/>';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';

							break;
						case "link":
							$inputValue = $attribute->attributeValue;

							if ($attribute->is_mandatory == 1)
								$class = "validate[required] text-input";

							$renderedContent .= '<div class="detail_box '.$hideClass.'">';
							if ($attribute->is_mandatory)
								$renderedContent .= '<div  class="form-detail req"></div>';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . '</label>';
							$renderedContent .= '<input type="text" maxLength="150" size="50" name="attribute_' . $attribute->id . '" id="attribute_' . $attribute->id . '" value="' . $inputValue . '"  class="input_txt ' . $class . '"/>';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';

							break;
						case "textarea":
							$inputValue = $attribute->attributeValue;

							if ($attribute->is_mandatory == 1)
								$class = "validate[required] text-input";

							$renderedContent .= '<div class="detail_box '.$hideClass.'">';
							if ($attribute->is_mandatory)
								$renderedContent .= '<div  class="form-detail req"></div>';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . '</label>';
							$renderedContent .= '<textarea cols="10" maxLength="250" name="attribute_' . $attribute->id . '" id="attribute_' . $attribute->id . '" class="input_txt ' . $class . '">' . $inputValue . '</textarea>';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';

							break;
						case "select_box":
							$attributeOptionsIDS = explode(",", $attribute->optionsIDS);

                            if ($attribute->is_mandatory == 1)
								$class = "validate[required] select";

                            $renderedContent .= '<div class="detail_box '.$hideClass.'">';
							if ($attribute->is_mandatory)
								$renderedContent .= '<div  class="form-detail req"></div>';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . '</label>';
							$renderedContent .= '<select name="attribute_' . $attribute->id . '" id="attribute_' . $attribute->id . '" class="input_sel ' . $class . '">';
							$renderedContent .= '<option data-icon="false" value="" selected="selected">' . JText::_("LNG_SELECT") . '</option>';
							foreach ($attributeOptions as $key => $option)
							{

							    if (isset($attribute->attributeValue) && $attributeOptionsIDS[$key] == $attribute->attributeValue) {
                                    $renderedContent .= '<option value="' . $attributeOptionsIDS[$key] . '" selected="selected">' . $option . '</option>';
                                }
								else
									$renderedContent .= '<option value="' . $attributeOptionsIDS[$key] . '">' . $option . '</option>';
							}
							$renderedContent .= '</select>';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';
							break;
						case "checkbox":
							$attributeOptionsIDS = explode(",", $attribute->optionsIDS);
							$attributeValues     = explode(",", $attribute->attributeValue);

							if ($attribute->is_mandatory == 1)
								$class = "validate[minCheckbox[1]] checkbox";

							$renderedContent .= '<div class="detail_box '.$hideClass.'">';
							if ($attribute->is_mandatory)
								$renderedContent .= '<div class="form-detail req"></div>';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . '</label>';
							foreach ($attributeOptions as $key => $option)
							{
								$renderedContent .= "<div class='custom-attr-checkbox'>";
								$option           = "<span class='option'>" . $option . "</span>";
								if (in_array($attributeOptionsIDS[$key], $attributeValues))
									$renderedContent .= '<input type="checkbox" name="attribute_' . $attribute->id . '[]" id="attribute_' . $attribute->id . '" value="' . $attributeOptionsIDS[$key] . '"  class="' . $class . '" checked="true"/>' . $option;
								else
									$renderedContent .= '<input type="checkbox" name="attribute_' . $attribute->id . '[]" id="attribute_' . $attribute->id . '" value="' . $attributeOptionsIDS[$key] . '"  class="' . $class . '"/>' . $option;
								$renderedContent .= "</div>";
							}
							$renderedContent .= '<input type="hidden" name="delete_attribute_' . $attribute->id . '" id="delete_attribute_' . $attribute->id . '" value="1" />';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';
							break;
						case "radio":
							$attributeOptionsIDS = explode(",", $attribute->optionsIDS);

                            if ($attribute->is_mandatory == 1)
								$class = "validate[required] radio";

							$renderedContent .= '<div class="detail_box '.$hideClass.'">';
							if ($attribute->is_mandatory)
								$renderedContent .= '<div  class="form-detail req"></div>';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . '</label>';
							foreach ($attributeOptions as $key => $option)
							{
								$option = "<span class='option'>" . $option . "</span>";
								if ($attributeOptionsIDS[$key] == $attribute->attributeValue)
									$renderedContent .= '&nbsp;<input type="radio" name="attribute_' . $attribute->id . '" id="attribute_' . $attribute->id . '" value="' . $attributeOptionsIDS[$key] . '"  class="' . $class . '" checked="true"/>&nbsp;&nbsp;' . $option;
								else
									$renderedContent .= '&nbsp;<input type="radio" name="attribute_' . $attribute->id . '" id="attribute_' . $attribute->id . '" value="' . $attributeOptionsIDS[$key] . '"  class="' . $class . '"/>&nbsp;&nbsp;' . $option;
							}
							$renderedContent .= '<input type="hidden" name="delete_attribute_' . $attribute->id . '" id="delete_attribute_' . $attribute->id . '" value="1" />';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';
							break;
                        case "multiselect":
                            $attributeOptionsIDS = explode(",", $attribute->optionsIDS);
                            $attributeValues     = explode(",", $attribute->attributeValue);

                            if ($attribute->is_mandatory == 1)
                                $class = "validate[required]";

                            $renderedContent .= '<div class="detail_box">';
                            if ($attribute->is_mandatory)
                                $renderedContent .= '<div  class="form-detail req"></div>';
                            $renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . '</label>';
                            $renderedContent .= '<select multiple="multiple" id="attribute_' . $attribute->id . '" class="inputbox input-medium chosen-select ' . $class . '" name="attribute_' . $attribute->id . '[]">';
                            foreach ($attributeOptions as $key => $option) {
                                if (in_array($attributeOptionsIDS[$key], $attributeValues))
                                    $renderedContent .= '<option value="' . $attributeOptionsIDS[$key] . '" selected>' . $option . '</option>';
                                else
                                    $renderedContent .= '<option value="' . $attributeOptionsIDS[$key] . '">' . $option . '</option>';
                            }
                            $renderedContent .= "</select>";
                            $renderedContent .= '<input type="hidden" name="delete_attribute_' . $attribute->id . '" id="delete_attribute_' . $attribute->id . '" value="1" />';
                            $renderedContent .= '<div class="clear"></div>';
                            $renderedContent .= '</div>';
                            break;
						default:
							echo "";
					}
				}
			}
		}
		return $renderedContent;
	}

	public static function renderAttributesSearch($attributes, $enablePackages, $packageFeatures) {
		//dump($attributes);
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$db =JFactory::getDBO();
		
		$renderedContent="";
		if(!empty($attributes)){
			if($appSettings->enable_multilingual){
				JBusinessDirectoryTranslations::updateAttributesTranslation($attributes);
			}
				
			foreach($attributes as $attribute) {
				$class = "";
				
				if(!isset($attribute->attributeValue)){
				    $attribute->attributeValue ="";
				}
				$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);

				if($attribute->is_mandatory==1 )
					$class = "validate[required]";

				if(isset($packageFeatures) && in_array($attribute->code,$packageFeatures) || !$enablePackages){
					$attributeOptions = explode(",", $attribute->options);
					if ($appSettings->enable_multilingual && isset($attribute->options)) {
						foreach ($attributeOptions as $key => $option) {
							$attributeOptions[$key] = JBusinessDirectoryTranslations::getTranslatedItemName($option);
						}
					}
					switch ($attribute->attributeTypeCode){
						case "header":
							break;
						case "input":
							$inputValue= $attribute->attributeValue;
							$renderedContent.= '<div class="form-field">';
							$renderedContent.= '<input type="text" placeholder="'.$attribute->name.'" size="50" name="attribute_'.$attribute->id.'" id="attribute_'.$attribute->id.'" value="'.$inputValue.'"  class="input_txt '.$class.'"/>';
							$renderedContent.= '</div>';
							break;
						case "textarea":
                            $inputValue= $attribute->attributeValue;
                            $renderedContent.= '<div class="form-field">';
                            $renderedContent.= '<input type="text" placeholder="'.$attribute->name.'" size="50" name="attribute_'.$attribute->id.'" id="attribute_'.$attribute->id.'" value="'.$inputValue.'"  class="input_txt '.$class.'"/>';
                            $renderedContent.= '</div>';
							break;
						case "select_box":
							$attributeOptionsIDS = explode(",",$attribute->optionsIDS);
							$renderedContent.= '<div class="form-field">';
							$renderedContent.= '<select name="attribute_'.$attribute->id.'" id="attribute_'.$attribute->id.'" class="input_sel '.$class.'">';
							$renderedContent.= '<option value="" selected="selected">'.JText::_("LNG_SELECT").' '.$attribute->name.'</option>';
							foreach ($attributeOptions as $key=>$option){
								if($attributeOptionsIDS[$key] == $attribute->attributeValue)
									$renderedContent.='<option value="'.$attributeOptionsIDS[$key].'" selected="selected">'.$option.'</option>';
								else
									$renderedContent.='<option value="'.$attributeOptionsIDS[$key].'">'.$option.'</option>';
							}
							$renderedContent.= '</select>';
							$renderedContent.= '</div>';
							break;
						case "checkbox":
							$attributeOptionsIDS = explode(",",$attribute->optionsIDS);
							$attributeValues = explode(",",$attribute->attributeValue);
							if($attribute->is_mandatory==1 )
								$class = "validate[minCheckbox[1]] checkbox";

							$renderedContent.= '<div class="form-field">';
							$renderedContent.= '<label id="details-lbl" for="attribute_'.$attribute->id.'" class="hasTip" title="'.$attribute->name.'">'.$attribute->name.'</label>';
							foreach ($attributeOptions as $key=>$option){
								$renderedContent.="<div class='custom-div'>";
								$option = "<span class='dir-check-lbl'>".$option."</span>";
								if( in_array($attributeOptionsIDS[$key] , $attributeValues))
									$renderedContent.= '<input type="checkbox" name="attribute_'.$attribute->id.'[]" id="attribute_'.$attribute->id.'" value="'.$attributeOptionsIDS[$key].'"  class="'.$class.'" checked="true"/>'.$option;
								else
									$renderedContent.= '<input type="checkbox" name="attribute_'.$attribute->id.'[]" id="attribute_'.$attribute->id.'" value="'.$attributeOptionsIDS[$key].'"  class="'.$class.'"/>'.$option;
								$renderedContent.="</div>";
							}
							$renderedContent.= '</div>';
							break;
						case "radio":
							$attributeOptionsIDS = explode(",",$attribute->optionsIDS);
							if($attribute->is_mandatory==1 )
								$class = "validate[required] radio";

							$renderedContent.= '<div class="form-field">';
							$renderedContent.= '<label id="details-lbl" for="attribute_'.$attribute->id.'" class="hasTip" title="'.$attribute->name.'">'.$attribute->name.'</label>';
							foreach ($attributeOptions as $key=>$option){

								$option = "<span class='dir-check-lbl'>".$option."</span>";
								if($attributeOptionsIDS[$key] == $attribute->attributeValue)
									$renderedContent.= '&nbsp;<input type="radio" name="attribute_'.$attribute->id.'" id="attribute_'.$attribute->id.'" value="'.$attributeOptionsIDS[$key].'"  class="'.$class.'" checked="true"/>&nbsp;&nbsp;'.$option;
								else
									$renderedContent.= '&nbsp;<input type="radio" name="attribute_'.$attribute->id.'" id="attribute_'.$attribute->id.'" value="'.$attributeOptionsIDS[$key].'"  class="'.$class.'"/>&nbsp;&nbsp;'.$option;
							}
							$renderedContent.= '</div>';
							break;
                        case "multiselect":
                            $attributeOptionsIDS = explode(",",$attribute->optionsIDS);
                            $renderedContent.= '<div class="form-field">';
                            $renderedContent.= '<select name="attribute_'.$attribute->id.'" id="attribute_'.$attribute->id.'" class="input_sel '.$class.'">';
                            $renderedContent.= '<option value="" selected="selected">'.JText::_("LNG_SELECT").' '.$attribute->name.'</option>';
                            foreach ($attributeOptions as $key=>$option){
                                if($attributeOptionsIDS[$key] == $attribute->attributeValue)
                                    $renderedContent.='<option value="'.$attributeOptionsIDS[$key].'" selected="selected">'.$option.'</option>';
                                else
                                    $renderedContent.='<option value="'.$attributeOptionsIDS[$key].'">'.$option.'</option>';
                            }
                            $renderedContent.= '</select>';
                            $renderedContent.= '</div>';
                            break;
						default:
							echo "";
					}
				}
			}
		}
		return $renderedContent;
	}

	public static function renderAttributesFront($attributes, $enablePackages, $packageFeatures) {
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$db =JFactory::getDBO();
		
		$renderedContent="";
		if(!empty($attributes)){
			//update the translations
			if($appSettings->enable_multilingual){
				JBusinessDirectoryTranslations::updateAttributesTranslation($attributes);
			}
			
			foreach($attributes as $attribute) {
				if($attribute->show_in_front != 1){
					continue;
				}
				
				if(!isset($attribute->attributeValue)){
				    $attribute->attributeValue ="";
				}
				$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);
				$attribute->name = htmlspecialchars($attribute->name, ENT_QUOTES);

				if(!isset($attribute->attributeValue)){
				    $attribute->attributeValue ="";
				}
				$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);
				
				if(isset($packageFeatures) && in_array($attribute->code,$packageFeatures) || !$enablePackages){
					$attributeOptions = explode(",", $attribute->options);
					if ($appSettings->enable_multilingual && isset($attribute->options)) {
						foreach ($attributeOptions as $key => $option) {
							$attributeOptions[$key] = JBusinessDirectoryTranslations::getTranslatedItemName($option);
						}
					}
					switch ($attribute->attributeTypeCode){
						case "header":
							$renderedContent.="<h3 class='attribute-header'>".$attribute->name."</h3>";
							break;
						case "input":
							$inputValue= $attribute->attributeValue;
							if(!empty($inputValue)){
								$renderedContent.='<ul class="business-properties attribute-'.$attribute->id.'">';
								$renderedContent.= '<li><div>'.$attribute->name.': </div></li>';
								$renderedContent.= '<li>'.$inputValue.'</li>';
								$renderedContent.= '</ul>';
							}
							break;
						case "link":
							$inputValue= $attribute->attributeValue;
							
							if(!empty($inputValue)){
							    if (!preg_match("~^(?:f|ht)tps?://~i", $inputValue)) {
							        $inputValue = "http://" . $inputValue;
							    }
							    
								$renderedContent.='<ul class="business-properties attribute-'.$attribute->id.'">';
								$renderedContent.= '<li><div>'.$attribute->name.': </div></li>';
								$renderedContent.= '<li> <a target="_blank" href="'.$inputValue.'">'.$inputValue.'</a></li>';
								$renderedContent.= '</ul>';
							}
							break;
						case "textarea":
							$inputValue= $attribute->attributeValue;
							if(!empty($inputValue)){
								$renderedContent.='<ul class="business-properties attribute-'.$attribute->id.'">';
								$renderedContent.= '<li><div>'.$attribute->name.': </div></li>';
								$renderedContent.= '<li>'.$inputValue.'</li>';
								$renderedContent.= '</ul>';
							}
							break;
						case "select_box":
							$attributeOptionsIDS = explode(",",$attribute->optionsIDS);
							$attributeIcons = explode(",",$attribute->optionsIcons);
							$inputValue="";
							foreach ($attributeOptions as $key=>$option){
								if($attributeOptionsIDS[$key] == $attribute->attributeValue){
									$inputValue = $option;
									if(!empty($attributeIcons) && isset($attributeIcons[$key])){
									   $icon = $attributeIcons[$key];
									}
									break;
								}
							}
							if(!empty($inputValue)){
								$renderedContent.='<ul class="business-properties attribute-'.$attribute->id.'">';
								$renderedContent.= '<li><div>'.$attribute->name.': </div></li>';
                                $color = !empty($attribute->color)?'style="color:'.$attribute->color.';"':'';
                                if($attribute->show_icon)
                                    $renderedContent.= '<i class="'.$icon.'" '.$color.'></i>&nbsp;';
								$renderedContent.= '<li>'.$inputValue.'</li>';
								$renderedContent.='</ul>';
							}
							break;
						case "checkbox":
							$attributeOptionsIDS = explode(",",$attribute->optionsIDS);
							$attributeValues = explode(",",$attribute->attributeValue);
                            $attributeIcons = explode(",",$attribute->optionsIcons);
                            if($attributeValues[0]=="")
								break;

                            $color = !empty($attribute->color)?'style="color:'.$attribute->color.';"':'';
                            $renderedContent.='<ul class="business-properties attribute-'.$attribute->id.'">';
							$renderedContent.= '<li><div>'.$attribute->name.': </div></li>';
							foreach ($attributeOptions as $key=>$option){
								if( in_array($attributeOptionsIDS[$key] , $attributeValues)){
								    $renderedContent.= '<li>';
								    if($attribute->show_icon && !empty($attributeIcons) && isset($attributeIcons[$key])){
                                        $renderedContent.= '<i class="'.$attributeIcons[$key].'" '.$color.'></i>&nbsp;';
								    }
									$renderedContent.= $option.',&nbsp;</li>';
								}
							}

							$renderedContent = substr($renderedContent, 0, -12);
							$renderedContent.='</ul>';
							break;
						case "radio":
							$attributeOptionsIDS = explode(",",$attribute->optionsIDS);
                            $attributeIcons = explode(",",$attribute->optionsIcons);
                            $inputValue="";
							foreach ($attributeOptions as $key=>$option){
								if($attributeOptionsIDS[$key] == $attribute->attributeValue){
									$inputValue = $option;
									if(!empty($attributeIcons) && isset($attributeIcons[$key])){
                                        $icon = $attributeIcons[$key];
									}
                                    break;
								}
							}
							if(!empty($inputValue)){
								$renderedContent.='<ul class="business-properties attribute-'.$attribute->id.'">';
								$renderedContent.= '<li><div>'.$attribute->name.': </div></li>';
                                $color = !empty($attribute->color)?'style="color:'.$attribute->color.';"':'';
                                if($attribute->show_icon)
                                    $renderedContent.= '<i class="'.$icon.'" '.$color.'></i>&nbsp;';
								$renderedContent.= '<li>'.$inputValue.'</li>';
								$renderedContent.='</ul>';
							}
								
							break;
                        case "multiselect":
                            $attributeOptionsIDS = explode(",",$attribute->optionsIDS);
                            $attributeValues = explode(",",$attribute->attributeValue);
                            $attributeIcons = explode(",",$attribute->optionsIcons);
                            if($attributeValues[0]=="")
                                break;

                            $color = !empty($attribute->color)?'style="color:'.$attribute->color.';"':'';
                            $renderedContent.='<ul class="business-properties attribute-'.$attribute->id.'">';
                            $renderedContent.= '<li><div>'.$attribute->name.': </div></li>';
                            foreach ($attributeOptions as $key=>$option){
                                if( in_array($attributeOptionsIDS[$key] , $attributeValues)){
                                    if($attribute->show_icon && !empty($attributeIcons) && isset($attributeIcons[$key])){
                                        $renderedContent.= '<i class="'.$attributeIcons[$key].'" '.$color.'></i>&nbsp;';
                                    }
                                    $renderedContent.= '<li>'.$option.',&nbsp;</li>';
                                }
                            }

                            $renderedContent = substr($renderedContent, 0, -12);
                            $renderedContent.= '</li>';
                            $renderedContent.='</ul>';
                            break;
						default:
							echo "";
					}
				}
			}
		}
		return $renderedContent;
	}
	
	/**
	 * Parse attributes and get the values for selected attribute
	 * @param Object $attributes
	 * @param string $name
	 */
	static function getAttributeAsString($attributes, $name){
		foreach($attributes as $attribute) {
			if($attribute->name == $name){
				return self::getAttributeValues($attribute);
			}
		}
		return "";
	}


	/**
	 * Render a attribute
	 * 
	 * @param unknown_type $attribute
	 */
	static function getAttributeValues($attribute){
		$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$db =JFactory::getDBO();
		
		$attributeOptions = explode(",", $attribute->options);
		if ($appSettings->enable_multilingual && isset($attribute->options)) {
			foreach ($attributeOptions as $key => $option) {
				$attributeOptions[$key] = JBusinessDirectoryTranslations::getTranslatedItemName($option);
			}
		}
		
		if(!isset($attribute->attributeValue)){
		    $attribute->attributeValue ="";
		}
		$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);
		
		switch ($attribute->attributeTypeCode){
			case "header":
				return "";
			case "input":
				$inputValue= $attribute->attributeValue;
				if(!empty($inputValue)){
					return $inputValue;
				}else{
					return "";
				}
				break;
			case "link":
				$inputValue= $attribute->attributeValue;
				if(!empty($inputValue)){
					return $inputValue;
				}else{
					return "";
				}
				break;
			case "textarea":
				$inputValue= $attribute->attributeValue;
				if(!empty($inputValue)){
					return $inputValue;
				}else{
					return "";
				}
				break;
			case "select_box":
				$attributeOptionsIDS = explode(",",$attribute->optionsIDS);
				$inputValue="";
				foreach ($attributeOptions as $key=>$option){
					if($attributeOptionsIDS[$key] == $attribute->attributeValue){
						$inputValue = $option;
						break;
					}
				}
				if(!empty($inputValue)){
					return $inputValue;
				}else{
					return "";
				}
				break;
			case "checkbox":
				$attributeOptionsIDS = explode(",",$attribute->optionsIDS);
				$attributeValues = explode(",",$attribute->attributeValue);
				if($attributeValues[0]=="")
					break;
				$renderedContent="";
				foreach ($attributeOptions as $key=>$option){
					if( in_array($attributeOptionsIDS[$key] , $attributeValues)){
						$renderedContent.= $option.',';
					}
				}
				$inputValue= rtrim($renderedContent,',');
				if(!empty($inputValue)){
					return $inputValue;
				}else{
					return "";
				}
				break;
			case "radio":
				$attributeOptionsIDS = explode(",",$attribute->optionsIDS);
				$inputValue="";
				foreach ($attributeOptions as $key=>$option){
					if($attributeOptionsIDS[$key] == $attribute->attributeValue){
						$inputValue = $option;
						break;
					}
				}
				if(!empty($inputValue)){
					return $inputValue;
				}else{
					return "";
				}
				break;
            case "multiselect":
                $attributeOptionsIDS = explode(",",$attribute->optionsIDS);
                $attributeValues = explode(",",$attribute->attributeValue);
                if($attributeValues[0]=="")
                    break;
                $renderedContent="";
                foreach ($attributeOptions as $key=>$option){
                    if( in_array($attributeOptionsIDS[$key] , $attributeValues)){
                        $renderedContent.= $option.',';
                    }
                }
                $inputValue= rtrim($renderedContent,',');
                if(!empty($inputValue)){
                    return $inputValue;
                }else{
                    return "";
                }
                break;
			default:
				echo "";
		}
	}

    /**
     * Assembles all icon classes of a certain attributes into an array. Returns false
     * if show_icon is set to no. Also checks if attribute is contained in the package when
     * the enablePackages is set to true. If not, it returns false.
     *
     * @param $attribute object containing attribute and it's selected values
     * @param $enablePackages boolean true if packages are enabled, false otherwise
     * @param $packageFeatures array array containing package features
     *
     * @return array|bool|string
     *
     * @since 4.9.0
     */
    static function getAttributeIcons($attribute, $enablePackages, $packageFeatures){
        $attributeIcons = explode(",", $attribute->optionsIcons);
        if(!$attribute->show_icon || $attribute->show_in_front != 1)
            return false;

        if (isset($packageFeatures) && in_array($attribute->code, $packageFeatures) || !$enablePackages) {
            switch ($attribute->attributeTypeCode) {
                case "select_box":
                    $attributeOptionsIDS = explode(",", $attribute->optionsIDS);
                    $icons = array();
                    foreach ($attributeIcons as $key => $val) {
                        if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
                            $icons[] = $val;
                            break;
                        }
                    }
                    if (!empty($icons)) {
                        return $icons;
                    } else {
                        return "";
                    }
                    break;
                case "checkbox":
                    $attributeOptionsIDS = explode(",", $attribute->optionsIDS);
                    $attributeValues = explode(",", $attribute->attributeValue);
                    if ($attributeValues[0] == "")
                        break;
                    $icons = array();
                    foreach ($attributeIcons as $key => $val) {
                        if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
                            $icons[] = $val;
                        }
                    }
                    if (!empty($icons)) {
                        return $icons;
                    } else {
                        return "";
                    }
                    break;
                case "radio":
                    $attributeOptionsIDS = explode(",", $attribute->optionsIDS);
                    $icons = array();
                    foreach ($attributeIcons as $key => $val) {
                        if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
                            $icons[] = $val;
                            break;
                        }
                    }
                    if (!empty($icons)) {
                        return $icons;
                    } else {
                        return "";
                    }
                    break;
                case "multiselect":
                    $attributeOptionsIDS = explode(",", $attribute->optionsIDS);
                    $attributeValues = explode(",", $attribute->attributeValue);
                    if ($attributeValues[0] == "")
                        break;
                    $icons = array();
                    foreach ($attributeIcons as $key => $val) {
                        if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
                            $icons[] = $val;
                        }
                    }
                    if (!empty($icons)) {
                        return $icons;
                    } else {
                        return "";
                    }
                    break;
                default:
                    echo "";
            }
        } else {
            return false;
        }
    }
    
     /**
     * Display icons of a specific attribute
     * 
     */
    static public function displayAttributeIcons($attributes,$attributeCode,$enablePackages, $packageFeatures){
        $attribute = null;
        foreach($attributes as $attr) {
            if($attr->code == $attributeCode){
                $attribute = $attr;
                break;
            }
        }
        $icons = self::getAttributeIcons($attribute, $enablePackages, $packageFeatures);
        $color = !empty($attribute->color)?$attribute->color:'';
        if(!empty($icons)) {
            foreach($icons as $icon)
                echo '<i class="'.$icon.' attribute-icon" style="color:'.$color.';"></i>';
        }
        
        return true;
    }
    
}

?>