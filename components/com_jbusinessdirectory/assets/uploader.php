<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view');
?>
<script type="text/javascript">
var view = '<?php echo $view; ?>';
var maxAttachments = '<?php echo isset($this->item->package)?$this->item->package->max_attachments :$this->appSettings->max_attachments ?>';
var maxPictures;
var setIsBack = false;
var picturesUploaded = 0;

var picturesFolder = '<?php echo JURI::root().PICTURES_PATH ?>';
var checkedIcon = '<?php echo JURI::root() ."administrator/components/".JBusinessUtil::getComponentName()."/assets/img/checked.gif"?>';
var uncheckedIcon = '<?php echo JURI::root()."administrator/components/".JBusinessUtil::getComponentName() ?>/assets/img/unchecked.gif';
var deleteIcon = '<?php echo JURI::root() ."administrator/components/".JBusinessUtil::getComponentName()."/assets/img/del_options.gif"?>';
var upIcon = '<?php echo JURI::root()."administrator/components/".JBusinessUtil::getComponentName() ?>/assets/img/up-icon.png';
var downIcon = '<?php echo JURI::root()."administrator/components/".JBusinessUtil::getComponentName() ?>/assets/img/down-icon.png';


function setIsBackEnd(){
    picturesUploaded = jQuery('input[name*="picture_path[]"]').length;
    setIsBack = true;
	checkNumberOfPictures();
}

function setMaxPictures(maxAllowedNumber){
    picturesUploaded = jQuery('input[name*="picture_path[]"]').length;
    maxPictures = maxAllowedNumber;
    checkNumberOfPictures();
}

function getMaxAllowedNumber(){
    return maxPictures;
}

function imageUploader(folderID, folderIDPath, type, picId) {
	if(type === undefined || type === null)
		type= '';
	if(picId === undefined || picId === null)
	    picId= '';
	jQuery("#"+type+"imageUploader"+picId).change(function()  {
		jQuery("#remove-image-loading").remove();
		jQuery("#"+type+"picture-preview"+picId).append('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span> Loading...</p>');
		jQuery("#item-form").validationEngine('detach');
		var fisRe = /^.+\.(jpg|bmp|gif|png|jpeg|PNG|JPG|GIF|JPEG)$/i;
		var path = jQuery(this).val();
		if (path.search(fisRe) == -1) {
			jQuery("#remove-image-loading").remove();
			alert('JPG, JPEG, BMP, GIF, PNG only!');
			return false;
		}

		jQuery(this).upload(folderIDPath, function(responce)  {
			if( responce == '' ) {
				jQuery("#remove-image-loading").remove();
				alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
				jQuery(this).val('');
			}
			else {
				var xml = responce;
                if(jbdUtils.enable_resolution_check) {
                    var warning = jQuery(xml).find("warning").attr("value");
                    if (typeof warning !== 'undefined') {
                        jQuery("#remove-image-loading").remove();
                        var wHeight = jQuery(xml).find("warning").attr("height");
                        var wWidth = jQuery(xml).find("warning").attr("width");
                        alert("<?php echo JText::_("LNG_IMAGE_SIZE_WARNING",true) ?> (Width:" + wWidth + ", Height:" + wHeight + ")");
                        return false;
                    }
                }

				jQuery(xml).find("picture").each(function() {
					if(jQuery(this).attr("error") == 0 ) {
						setUpImage(
							folderID + jQuery(this).attr("path"),
							jQuery(this).attr("name"),
							type,
                            picId
						);
						jQuery("#remove-image-loading").remove();

						if(jbdUtils.enable_crop) {
                            showCropper(picturesFolder + folderID + jQuery(this).attr("path"), type, picId);
                        }
					}
					else if( jQuery(this).attr("error") == 1 )
						alert("<?php echo JText::_("LNG_FILE_ALLREADY_ADDED",true)?>");
					else if( jQuery(this).attr("error") == 2 )
						alert("<?php echo JText::_("LNG_ERROR_ADDING_FILE",true)?>");
					else if( jQuery(this).attr("error") == 3 )
						alert("<?php echo JText::_("LNG_ERROR_GD_LIBRARY",true)?>");
					else if( jQuery(this).attr("error") == 4 )
						alert("<?php echo JText::_("LNG_ERROR_RESIZING_FILE",true)?>");
				});
			}
		});
		jQuery("#item-form").validationEngine('attach');
	});
}

function setUpImage(path, name, type, picId) {
	jQuery("#"+type+"imageLocation"+picId).val(path);
	var img_new	= document.createElement('img');
	img_new.setAttribute('src', picturesFolder + path );
	img_new.setAttribute('id', 'itemImg');
	img_new.setAttribute('class', 'item-image');
	if (picId != '') img_new.setAttribute("style", "width:100px;");
	if(view == 'speaker' || view =='membership' ) img_new.setAttribute("style", "width:100px;height:100px;");
	jQuery("#"+type+"picture-preview"+picId).empty();
	jQuery("#"+type+"picture-preview"+picId).append(img_new);
}

function markerUploader(folderID, folderIDPath) {
	jQuery("#markerfile").change(function() {
		jQuery("#remove-image-loading").remove();
		jQuery("#marker-preview").append('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span></p>');
		jQuery("#item-form").validationEngine('detach');
		var fisRe 	= /^.+\.(jpg|bmp|gif|png)$/i;
		var path = jQuery(this).val();
		if (path.search(fisRe) == -1) {
			jQuery("#remove-image-loading").remove();
			alert(' JPG, BMP, GIF, PNG only!');
			return false;
		}
		jQuery(this).upload(folderIDPath, function(responce) {
			if( responce == '' ) {
				jQuery("#remove-image-loading").remove();
				alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
				jQuery(this).val('');
			}
			else {
				var xml = responce;
				jQuery(xml).find("picture").each(function() {
					if(jQuery(this).attr("error") == 0 ) {
						setUpMarker(
							folderID + jQuery(this).attr("path"),
							jQuery(this).attr("name")
						);
						jQuery("#remove-image-loading").remove();
					}
					else if( jQuery(this).attr("error") == 1 )
						alert("<?php echo JText::_('LNG_FILE_ALLREADY_ADDED',true)?>");
					else if( jQuery(this).attr("error") == 2 )
						alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
					else if( jQuery(this).attr("error") == 3 )
						alert("<?php echo JText::_('LNG_ERROR_GD_LIBRARY',true)?>");
					else if( jQuery(this).attr("error") == 4 )
						alert("<?php echo JText::_('LNG_ERROR_RESIZING_FILE',true)?>");
				});
			}
		});
		jQuery("#item-form").validationEngine('attach');
	});
}

function setUpMarker(path, name) {
	jQuery("#markerLocation").val(path);
	var img_new	= document.createElement('img');
	img_new.setAttribute('src', picturesFolder + path );
	img_new.setAttribute('id', 'markerImg');
	img_new.setAttribute('class', 'marker-image');
	jQuery("#marker-preview").empty();
	jQuery("#marker-preview").append(img_new);
}

function multiImageUploader(folder, folderPath) {
	jQuery("#multiImageUploader").change(function() {
		jQuery("#remove-image-loading").remove();
		jQuery("#table_pictures").append('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span>Loading...</p>');
		jQuery("#item-form").validationEngine('detach');
		var fisRe = /^.+\.(jpg|bmp|gif|png|jpeg|PNG|JPG|GIF|JPEG)$/i;
		var path = jQuery(this).val();
		
		if (path.search(fisRe) == -1) {
			jQuery("#remove-image-loading").remove();
			alert(' JPG, JPEG, BMP, GIF, PNG only!');
			return false;
		}	
		jQuery(this).upload(folderPath, function(responce) {
			if( responce =='' ) {
				jQuery("#remove-image-loading").remove();
				alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
				jQuery(this).val('');
			}
			else {
				var xml = responce;
				if(jbdUtils.enable_resolution_check) {
                    var warning = jQuery(xml).find("warning").attr("value");
                    if (typeof warning !== 'undefined') {
                        jQuery("#remove-image-loading").remove();
                        var wHeight = jQuery(xml).find("warning").attr("height");
                        var wWidth = jQuery(xml).find("warning").attr("width");
                        alert("<?php echo JText::_("LNG_IMAGE_SIZE_WARNING",true) ?> (Width:" + wWidth + ", Height:" + wHeight + ")");
                        return false;
                    }
                }
				jQuery(xml).find("picture").each(function() {
					if(jQuery(this).attr("error") == 0 ) {
						addPicture(
							folder + jQuery(this).attr("path"),
							jQuery(this).attr("name")
						);
						jQuery("#remove-image-loading").remove();
					}
					else if( jQuery(this).attr("error") == 1 )
						alert("<?php echo JText::_('LNG_FILE_ALLREADY_ADDED',true)?>");
					else if( jQuery(this).attr("error") == 2 )
						alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
					else if( jQuery(this).attr("error") == 3 )
						alert("<?php echo JText::_('LNG_ERROR_GD_LIBRARY',true)?>");
					else if( jQuery(this).attr("error") == 4 )
						alert("<?php echo JText::_('LNG_ERROR_RESIZING_FILE',true)?>");
				});
				jQuery(this).val('');
			}
		}, 'html');
		jQuery("#item-form").validationEngine('attach');
	});
}

function addPicture(path, name) {
	var tb = document.getElementById('sortable');
	if( tb==null ) {
		alert('Undefined List, contact administrator !');
	}

	var li_new	= document.createElement('li');
	li_new.setAttribute('class', 'ui-state-default');

	var span_new = document.createElement('span');
	span_new.setAttribute('class','ui-icon ui-icon-arrowthick-2-n-s');
	li_new.appendChild(span_new);

	var div_new	= document.createElement('div');
	div_new.setAttribute('class','images-list-container');
	li_new.appendChild(div_new);

	var img_new	= document.createElement('img');
	img_new.setAttribute('src', picturesFolder + path );
	img_new.setAttribute('class', 'uploaded-image');
	jQuery(img_new).css("cssText", "padding-right: 10px !important;");
	div_new.appendChild(img_new);

	var h6_new = document.createElement('h6');
	h6_new.innerHTML = name;
	div_new.appendChild(h6_new);

	if (path.search("reviews") != 1 || setIsBack) {
		var div2_new = document.createElement('div');
		div2_new.setAttribute('class', 'image-checked-container');
		div_new.appendChild(div2_new);

		var img_enable = document.createElement('i');
		img_enable.setAttribute('class', 'dir-icon-check-square-o');
		img_enable.setAttribute('aria-hidden', 'true');
		img_enable.onclick = function () {
			var form = document.adminForm;
			var v_status = null;
			var pos = jQuery(this).closest('li').index();

			if (form.elements['picture_enable[]'].length == null) {
				v_status = form.elements['picture_enable[]'];
			}
			else {
				v_status = form.elements['picture_enable[]'][pos];
			}
			if (v_status.value == '1') {
				jQuery(this).attr('class', 'dir-icon-square-o');
				jQuery(v_status).val('0');
			}
			else {
				jQuery(this).attr('class', 'dir-icon-check-square-o');
				jQuery(v_status).val('1');
			}
		};
		div2_new.appendChild(img_enable);
	}

	var div3_new = document.createElement('div');
	div3_new.setAttribute('class','image-delete-container');
	div_new.appendChild(div3_new);

	var img_del	= document.createElement('i');
	img_del.setAttribute('class', 'dir-icon-trash-o');
	img_del.setAttribute('aria-hidden', 'true');
	img_del.onclick = function() {
		if(!confirm('<?php echo JText::_('LNG_CONFIRM_DELETE_PICTURE',true)?>'))
			return;
		var row = jQuery(this).parents('li:first');
		var row_idx = row.prevAll().length;
		jQuery('#crt_pos').val(row_idx);
		jQuery('#crt_path').val( path );
		jQuery('#btn_removefile').click();
	};

	div3_new.appendChild(img_del);

	var input_new_1 = document.createElement('input');
	input_new_1.setAttribute('type', 'hidden');
	input_new_1.setAttribute('name', 'picture_enable[]');
	input_new_1.setAttribute('id', 'picture_enable[]');
	input_new_1.setAttribute('value', '1');
	div_new.appendChild(input_new_1);
	
	var input_new_2	= document.createElement('input');
	input_new_2.setAttribute('type', 'hidden');
	input_new_2.setAttribute('name', 'picture_path[]');
	input_new_2.setAttribute('id', 'picture_path[]');
	input_new_2.setAttribute('value', path);
	div_new.appendChild(input_new_2);

//	if (path.search("reviews") != 1 || setIsBack) {
//		var input_new_3 = document.createElement('input');
//		input_new_3.setAttribute("name", "picture_info[]");
//		input_new_3.setAttribute("id", "picture_info");
//		input_new_3.setAttribute('value', '');
//		input_new_3.style.width = '25%';
//		div_new.appendChild(input_new_3);
//	}

	if (path.search("reviews") != 1 || setIsBack) {
		var textarea_new = document.createElement('textarea');
		textarea_new.setAttribute("name", "picture_info[]");
		textarea_new.setAttribute("id", "picture_info");
		textarea_new.setAttribute("cols", "50");
		textarea_new.setAttribute("rows", "1");
		jQuery(textarea_new).css("cssText", "width: 40% !important;");
		textarea_new.style.position = 'relative';
		textarea_new.style.resize = 'horizontal';
		div_new.appendChild(textarea_new);
	}


	tb.appendChild(li_new);

    checkNumberOfPictures();
}

function removePicture(pos) {
	var lis=document.querySelectorAll('#sortable li');

	if(lis==null) {
		alert('Undefined List, contact administrator !');
	}

	if(pos >= lis.length)
		pos = lis.length-1;
	lis[pos].parentNode.removeChild(lis[pos]);

    checkNumberOfPictures();
}

function removeAllPicture() {
    var lis=document.querySelectorAll('#sortable li');

    if(lis==null) {
        alert('Undefined List, contact administrator !');
    }

    var maxImages = lis.length;

    for (var i = 0; i < maxImages; i++) {
        var pos = i;

        if(pos >= lis.length)
            pos = lis.length-1;

        lis[pos].parentNode.removeChild(lis[pos]);
    }

    checkNumberOfPictures();
}

function btn_removefile(removePath) {
	jQuery('#btn_removefile').click(function() {
		pos = jQuery('#crt_pos').val();
		path = jQuery('#crt_path').val();
		jQuery( this ).upload(removePath + path + '&_pos='+pos, function(responce) {
			if( responce =='' ) {
				alert("<?php echo JText::_('LNG_ERROR_REMOVING_FILE',true)?>");
				jQuery(this).val('');
			}
			else {
				var xml = responce;
				jQuery(xml).find("picture").each(function() {
					if(jQuery(this).attr("error") == 0 ) {
						removePicture( jQuery(this).attr("pos") );
					}
					else if( jQuery(this).attr("error") == 2 ) {
						removePicture(pos);
					}
					else if( jQuery(this).attr("error") == 3 )
						alert("<?php echo JText::_('LNG_FILE_DOESNT_EXIST',true)?>");
				});
				jQuery('#crt_pos').val('');
				jQuery('#crt_path').val('');
			}
		}, 'html');
		jQuery("#item-form").validationEngine('detach');
	});
}

function multiFileUploader(folderID, folderIDPath) {
	jQuery("#multiFileUploader").change(function() {
		jQuery("#remove-file-loading").remove();
		jQuery("#table_attachments").append('<p id="remove-file-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span> Loading...</p>');
		jQuery("#item-form").validationEngine('detach');
		var path = jQuery(this).val();
		jQuery(this).upload(folderIDPath, function(responce) {
			if( responce =='' ) {
				jQuery("#remove-file-loading").remove();
				alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
				jQuery(this).val('');
			}
			else {
				var xml = responce;
				jQuery("#remove-file-loading").remove();
				jQuery(xml).find("attachment").each(function() {
				    if(jQuery(this).attr("name").length > <?php echo MAX_FILENAME_LENGTH ?>) {
                        alert("<?php echo JText::_('LNG_FILENAME_TOO_LONG',true)?>");
                    }
					else if(jQuery(this).attr("error") == 0 ) {
						if(jQuery("#table_attachments tr").length < maxAttachments) {
							addAttachment(
								folderID + jQuery(this).attr("path"),
								jQuery(this).attr("name")
							);
						jQuery("#multiFileUploader").val("");
						} else {
							alert("<?php echo JText::_('LNG_MAX_ATTACHMENTS_ALLOWED',true)?>"+maxAttachments);
						}
					}
					else if( jQuery(this).attr("info"))
						alert(jQuery(this).attr("info"));
					else {
						alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
					}
				});
			}
		}, 'html');
		jQuery("#item-form").validationEngine('attach');
	});
}

function addAttachment(path, name) {
	var tb = document.getElementById('table_attachments');
	if( tb==null ) {
		alert('Undefined table, contact administrator !');
	}

	var td1_new	= document.createElement('td');
	td1_new.style.textAlign = 'left';
	jQuery(td1_new).css("cssText", "padding-top: 15px;");
	var input_new = document.createElement('input');
	input_new.setAttribute("name","attachment_name[]");
	input_new.setAttribute("id","attachment_name");
	jQuery(input_new).css("cssText", "height: 34px;");
	input_new.setAttribute("type","text");
	td1_new.appendChild(input_new);

	var span_new = document.createElement('span');
	span_new.innerHTML = name;
	td1_new.appendChild(span_new);
	
	var input_new_1 = document.createElement('input');
	input_new_1.setAttribute('type', 'hidden');
	input_new_1.setAttribute('name', 'attachment_status[]');
	input_new_1.setAttribute('id', 'attachment_status');
	input_new_1.setAttribute('value', '1');
	td1_new.appendChild(input_new_1);

	var input_new_2 = document.createElement('input');
	input_new_2.setAttribute('type', 'hidden');
	input_new_2.setAttribute('name', 'attachment_path[]');
	input_new_2.setAttribute('id', 'attachment_path');
	input_new_2.setAttribute('value', path);
	td1_new.appendChild(input_new_2);
	
	var td3_new	= document.createElement('td');
	jQuery(td3_new).css("cssText", "padding-bottom: 25px !important;");
	td3_new.style.textAlign = 'center';
	
	var img_del	= document.createElement('img');
	img_del.setAttribute('src', deleteIcon);
	img_del.setAttribute('class', 'btn_attachment_delete');
	img_del.setAttribute('id', 	tb.rows.length);
	img_del.setAttribute('name', 'del_attachment_' + tb.rows.length);
	img_del.onmouseover = function() { this.style.cursor='hand';this.style.cursor='pointer' };
	img_del.onmouseout = function() { this.style.cursor='default' };
	img_del.onclick = function() { 
		if( !confirm('<?php echo JText::_("LNG_CONFIRM_DELETE_ATTACHMENT",true)?>' )) 
			return; 
		var row = jQuery(this).parents('tr:first');
		var row_idx = row.prevAll().length;
		jQuery('#crt_pos_a').val(row_idx);
		jQuery('#crt_path_a').val( path );
		jQuery('#btn_removefile_at').click();
	};

	td3_new.appendChild(img_del);

	var td4_new	= document.createElement('td');
	jQuery(td4_new).css("cssText", "padding-bottom: 25px !important;");
	td4_new.style.textAlign='center';
	var img_enable = document.createElement('img');
	img_enable.setAttribute('src', checkedIcon);
	img_enable.setAttribute('class', 'btn_attachment_status');
	img_enable.setAttribute('id', 	tb.rows.length);
	img_enable.setAttribute('name', 'enable_img_' + tb.rows.length);

	img_enable.onclick = function() { 
		var form = document.adminForm;
		var v_status = null; 
		if( form.elements['attachment_status[]'].length == null ) {
			v_status  = form.elements['attachment_status[]'];
		}
		else {
			var pos = jQuery(this).closest('tr')[0].sectionRowIndex;
			var tb = document.getElementById('table_attachments');
			if( pos >= tb.rows.length )
				pos = tb.rows.length-1;
			v_status  = form.elements['attachment_status[]'][pos];
		}

		if(v_status.value=='1') {
			jQuery(this).attr('src', uncheckedIcon);
			v_status.value ='0';
		}
		else {
			jQuery(this).attr('src', checkedIcon);
			v_status.value ='1';
		}
	};

	td4_new.appendChild(img_enable);
	var td5_new	= document.createElement('td');  
	td5_new.style.textAlign = 'center';
	jQuery(td5_new).css("cssText", "padding-bottom: 25px !important; width: 1px");
			
	td5_new.innerHTML = '<span class=\'span_up\' style=\' margin-bottom: 0 !important; padding-top: 5px  \' onclick=\'var row = jQuery(this).parents("tr:first");  row.insertBefore(row.prev());\'><img src="' + upIcon + '"></span>'+
						'<span class=\'span_down\' onclick=\'var row = jQuery(this).parents("tr:first"); row.insertAfter(row.next());\'><img src="' + downIcon + '"></span>';

	var tr_new = tb.insertRow(tb.rows.length);

	tr_new.appendChild(td1_new);
	tr_new.appendChild(td3_new);
	tr_new.appendChild(td4_new);
	tr_new.appendChild(td5_new);
}

function removeAttachment(pos) {
	var tb = document.getElementById('table_attachments');

	if( tb==null ) {
		alert('Undefined table, contact administrator !');
	}

	if(pos >= tb.rows.length)
		pos = tb.rows.length-1;
	tb.deleteRow( pos );
}

function btn_removefile_at(removePath_at) {
	jQuery('#btn_removefile_at').click(function() {
		jQuery("#item-form").validationEngine('detach');
		pos = jQuery('#crt_pos_a').val();
		path = jQuery('#crt_path_a').val();
		jQuery(this).upload(removePath_at + path + '&_pos='+pos, function(responce) {
			if( responce =='' ) {
				alert("<?php echo JText::_('LNG_ERROR_REMOVING_FILE',true)?>");
				jQuery(this).val('');
			}
			else {
				var xml = responce;
				jQuery(xml).find("picture").each(function() {
					if(jQuery(this).attr("error") == 0 ) {
						removeAttachment( jQuery(this).attr("pos") );
					}
					else if( jQuery(this).attr("error") == 2 )
						alert("<?php echo JText::_('LNG_ERROR_REMOVING_FILE',true)?>");
					else if( jQuery(this).attr("error") == 3 )
						alert("<?php echo JText::_('LNG_FILE_DOESNT_EXIST',true)?>");
				});
				jQuery('#crt_pos_a').val('');
				jQuery('#crt_path_a').val('');
			}
		}, 'html');
		jQuery("#item-form").validationEngine('detach');
	});
}

function removeCoverImage() {
	jQuery("#cover-imageLocation").val("");
	jQuery("#cover-picture-preview").html("");
	jQuery("#cover-imageUploader").val("");
}

function removeLogo() {
	jQuery("#imageLocation").val("");
	jQuery("#picture-preview").html("");
	jQuery("#imageUploader").val("");
}

function removeAd() {
    jQuery("#ad-imageLocation").val("");
    jQuery("#ad-picture-preview").html("");
    jQuery("#ad-imageUploader").val("");
}

function removeCompanyLogo() {
    jQuery("#company-imageLocation").val("");
    jQuery("#company-picture-preview").html("");
    jQuery("#company-imageUploader").val("");
}
/* Company & Conference & SessionLocation & Speaker */


function removeMarker() {
	jQuery("#markerLocation").val("");
	jQuery("#marker-preview").html("");
	jQuery("#markerfile").val("");
} 

function removeMapMarker() {
	jQuery("#mapimageLocation").val("");
	jQuery("#mappicture-preview").html("");
	jQuery("#mapimageUploader").val("");
} 
/* Category */


function removeRow(id) {
	jQuery('#'+id).remove();
}

function checkNumberOfPictures() {
    var nrPictures = jQuery('input[name*="picture_path[]"]').length;

    if (maxPictures == nrPictures){
        jQuery("#file-upload").hide();
    }else{
        jQuery("#file-upload").show();

    }
}

</script>