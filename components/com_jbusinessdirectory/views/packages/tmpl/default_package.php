<div class="featured-product-col-border" id="hss">
					<div class="featured-product-head">
						<div class="head-text" >
							<div class="name">
								<?php echo $package->name ?> 
							</div>
							<div class="price" >
								<div class="item1">
									<span class="price-item"><?php echo $package->price == 0 ? JText::_("LNG_FREE"):JBusinessUtil::getPriceFormat($package->price + $this->appSettings->vat*$package->price/100)?></span>
									<?php echo $package->days > 0 ? " / ":"" ?>	
									<?php if($package->days > 0) {?>
									 	<span>
									 		<?php 
									 			echo $package->time_amount;
									 			echo ' ';
									 			$time_unit = JText::_('LNG_DAYS');
									 			switch($package->time_unit){
									 				case "D":
									 					$time_unit = JText::_('LNG_DAYS');
									 					break;
									 				case "W":
									 					$time_unit = JText::_('LNG_WEEKS');
									 					break;
									 				case "M":
									 					$time_unit = JText::_('LNG_MONTHS');
									 					break;
									 				case "Y":
									 					$time_unit = JText::_('LNG_YEARS');
									 					break;
									 			}
									 			
									 			echo $time_unit;
									 		?>
									 		</span>
									 <?php }?>	
									</div>
								<span class="item2">
									<?php echo $package->description ?>
								</span>
							</div>
						</div>
					</div>
					
					<?php foreach($this->packageFeatures as $key=>$featureName){?>
	
					<div class="featured-product-cell" >
						<?php 
							$class="dir-icon-minus-square not-contained-feature";
							if(isset($package->features) && in_array($key, $package->features)){
								$class="dir-icon-check-square contained-feature";
							}
						?>
						<div>
							<?php
								$featureSpec = '';
								$max = "";
								if($key == 'image_upload') {
									if(!empty($package->max_pictures)){
										$max = $package->max_pictures;
									}
									$class = !empty($package->max_pictures)?$class:"dir-icon-minus-square not-contained-feature";
									
								}
								if($key == 'videos') {
									if(!empty($package->max_videos)){
										$max = $package->max_videos;
									}
									$class = !empty($package->max_videos)?$class:"dir-icon-minus-square not-contained-feature";
									
								}
								if($key == 'company_offers') {
									if(!empty($package->max_offers)){
										$max = $package->max_offers;
									}
								}

								if($key == 'company_events') {
									if(!empty($package->max_events)){
										$max = $package->max_events;
									}
								}
								
								if($key == 'attachments') {
									if(!empty($package->max_attachments)){
										$max = $package->max_attachments;
									}
									$class = !empty($package->max_attachments)?$class:"dir-icon-minus-square not-contained-feature";
									
								}
								if($key == 'multiple_categories') {
									if(!empty($package->max_categories)){
										$max = $package->max_categories;
									}
									$class = !empty($package->max_categories)?$class:"dir-icon-minus-square not-contained-feature";
								}
								
								if($class=="not-contained-feature"){
									$max="";
								}
							?>
							<i class="<?php echo $class?>"></i><span class="max-items"><?php echo $max ?></span> <?php echo $featureName.$featureSpec; ?>
						</div>
					</div>
					<?php } ?>
					
					<?php foreach($this->customAttributes as $customAttribute){
						if($customAttribute->show_in_front==0 || $customAttribute->attribute_type != ATTRIBUTE_TYPE_BUSINESS){
							continue;
						}
						$class="dir-icon-minus-square not-contained-feature";
						if(isset($package->features) && in_array($customAttribute->code,$package->features)){
							$class="dir-icon-check-square contained-feature";
						}
						?>
						<div class="featured-product-cell " >
							<div><i class="<?php echo $class?>"></i><?php echo $customAttribute->name?></div>
						</div>
					<?php }?>
					
				</div>