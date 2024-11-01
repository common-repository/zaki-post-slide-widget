<?php
/**
 * Plugin Name: Zaki Post Slide Widget
 * Plugin URI:  http://www.zaki.it
 * Description: Widget that allows you to create a slider of posts using the jQuery library <a href="http://bxslider.com">bxSlider v4</a>. You can customize many options, and choose from categories or custom post-type. Css customizable as you like.
 * Version:     1.3.3
 * Author:      Zaki Design
 * Author URI:  http://www.zaki.it
 */

	/**
	 *  Include JS
	 */
	function zakiPostSlideJs() {
		wp_enqueue_script('jquery.bxslider',plugins_url( '/bxSlider/jquery.bxslider.min.js', __FILE__ ),array('jquery'),false,false);
	}
	add_action('wp_enqueue_scripts','zakiPostSlideJs');

	/**
	 * zakiPostSlideWidget Class
	 */
	class zakiPostSlideWidget extends WP_Widget {
		
		// Print subcategories according to the main category and its indentation
		private function getSubCategoriesList($catId,$fieldname,$indent,$arrayValues) {
			$catList = get_categories(array(
				'hide_empty' => false,
				'parent' => $catId
			));
			foreach($catList as $cat) : 
				for($i=1;$i<=$indent;$i++) { echo '&mdash;'; }
				?>
				&nbsp;<input id="<?php echo $this->get_field_id($fieldname); ?>" name="<?php echo $this->get_field_name($fieldname); ?>[]" type="checkbox" value="<?=$cat->term_id?>" <?php if(in_array($cat->term_id,$arrayValues)) echo 'checked="checked"' ?> />&nbsp;<?=$cat->name?>
				<br />
				<?php 
				$subCatList = get_categories(array(
					'hide_empty' => false,
					'parent' => $cat->term_id
				));
				if($subCatList) :
					$newIndent = $indent + 1;
					$this->getSubCategoriesList($cat->term_id, $fieldname, $newIndent, $arrayValues);
				endif;
			endforeach;
		}
	
		// Init and registration
		function zakiPostSlideWidget() {
			$widget_ops = array(
				'classname' => 'zakiPostSlideWidget',
				'description' => 'Widget that create a slider of posts using the jQuery library bxSlider v4'
			);
			$this->WP_Widget('zakiPostSlideWidget', 'Zaki Post Slide Widget', $widget_ops);			
		}
		
		// Settings
		function form( $instance ) {
			$instance = wp_parse_args( 
				(array) $instance,
				array(
					'title' => '',
					'customclass' => '',
					'number' => 1,
					'block' => 1,
					'orderby' => 'post_date',
					'order' => 'DESC',
					'showdate' => true,
					'isposttype' => 0,
					'categories' => array(),
					'posttype' => '',
					'showimage' => false,
					'imagetype' => '',
					'imagelinked' => false,
					'showarchive' => false,
					'titlelink' => false,
					'textlength' => 20,
					'pausetime' => 6000,
					'scrolltype' => 'horizontal'
				)
			);
			$title = $instance['title'];
			$titlelink = $instance['titlelink'];
			$customclass = $instance['customclass'];
			$number = ($instance['number']=='' or $instance['number']==0 or !is_numeric($instance['number'])) ? 1 : $instance['number'];
			$block = ($instance['block']=='' or $instance['block']==0 or !is_numeric($instance['block'])) ? 1 : $instance['block'];
			$orderby = $instance['orderby'];
			$order = $instance['order'];
			$isposttype = $instance['isposttype'];
			$categories = (is_array($instance['categories'])) ? $instance['categories'] : array();
			$posttype = $instance['posttype'];
			$showimage = $instance['showimage'];
			$imagetype = $instance['imagetype'];
			$imagelinked = $instance['imagelinked'];
			$showdate = $instance['showdate'];
			$showarchive = $instance['showarchive'];
			$textlength = ($instance['textlength']=='' or !is_numeric($instance['textlength'])) ? 20 : $instance['textlength'];
			$pausetime = $instance['pausetime'];
			$scrolltype = $instance['scrolltype'];			
			?>			
				
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">
					<strong><?=__('Title','zaki')?>:</strong>&nbsp;
					<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('titlelink'); ?>">
					<strong><?=__('Put archive link on title?','zaki')?>:</strong>&nbsp;
					<input id="<?php echo $this->get_field_id('titlelink'); ?>" name="<?php echo $this->get_field_name('titlelink'); ?>" type="checkbox" value="1" <?php if($titlelink) echo 'checked="checked"' ?> />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('customclass'); ?>">
					<strong><?=__('Additional CSS classes','zaki')?>:</strong>&nbsp;
					<input class="widefat" id="<?php echo $this->get_field_id('customclass'); ?>" name="<?php echo $this->get_field_name('customclass'); ?>" type="text" value="<?php echo esc_attr($customclass); ?>" />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>">
					<strong><?=__('Number of posts to show','zaki')?>:</strong>&nbsp;
					<input size="1" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('block'); ?>">
					<strong><?=__('Number of posts to show for block','zaki')?>:</strong>&nbsp;
					<input size="1" id="<?php echo $this->get_field_id('block'); ?>" name="<?php echo $this->get_field_name('block'); ?>" type="text" value="<?php echo esc_attr($block); ?>" />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('orderby'); ?>">
					<strong><?=__('Sorting','zaki')?>:</strong>&nbsp;
					<select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
						<option value="title" <?php if($orderby=='title') echo 'selected="selected"'; ?>><?=__('Title','zaki')?></option>
						<option value="date" <?php if($orderby=='date') echo 'selected="selected"'; ?>><?=__('Date','zaki')?></option>
						<option value="name" <?php if($orderby=='name') echo 'selected="selected"'; ?>><?=__('Slug','zaki')?></option>
						<option value="ID" <?php if($orderby=='ID') echo 'selected="selected"'; ?>><?=__('ID','zaki')?></option>
						<option value="rand" <?php if($orderby=='rand') echo 'selected="selected"'; ?>><?=__('Random','zaki')?></option>
						<option value="menu_order" <?php if($orderby=='menu_order') echo 'selected="selected"'; ?>><?=__('Page Order','zaki')?></option>
					</select>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('order'); ?>">
					<strong><?=__('Sorting type','zaki')?>:</strong>&nbsp;
					<select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
						<option value="DESC" <?php if($order=='DESC') echo 'selected="selected"'; ?>><?=__('Descending','zaki')?></option>
						<option value="ASC" <?php if($order=='ASC') echo 'selected="selected"'; ?>><?=__('Ascending','zaki')?></option>
					</select>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('isposttype'); ?>">
					<?php
						// JS action
						$isposttype_action = "javascript:
	 						if(jQuery('#".$this->get_field_id('isposttype')."').val()==0) { 
	 							jQuery('#".$this->id."-CAT').show();
								jQuery('#".$this->id."-PT').hide(); 
							} else {
								jQuery('#".$this->id."-PT').show();
								jQuery('#".$this->id."-CAT').hide();
							};";
					?>
					<strong><?=__('Type of post to show','zaki')?>:</strong><br />
					<select onChange="<?=$isposttype_action?>" id="<?php echo $this->get_field_id('isposttype'); ?>" name="<?php echo $this->get_field_name('isposttype'); ?>">
						<option value="0" <?php if($isposttype==0) echo 'selected="selected"'; ?>><?=__('Posts','zaki')?></option>
						<option value="1" <?php if($isposttype==1) echo 'selected="selected"'; ?>><?=__('Custom Post Type','zaki')?></option>
					</select>
				</label>
				<br />
				<div id="<?=$this->id?>-CAT">
					<?php
						$mainCatList = get_categories(array(
							'hide_empty' => false,
							'parent' => 0
						)); 
					?>
					<label for="<?php echo $this->get_field_id('categories'); ?>">
						<div style="height:150px; overflow:auto; background:#fff; border:1px solid #eeeeee; padding:5px;">
						<?php foreach($mainCatList as $cat) : ?>
							<input id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>[]" type="checkbox" value="<?=$cat->term_id?>" <?php if(in_array($cat->term_id,$categories)) echo 'checked="checked"' ?> />&nbsp;<?=$cat->name?>
							<br />
							<?php $this->getSubCategoriesList($cat->term_id, 'categories', 1, $categories); ?>
						<?php endforeach; ?>
						</div>
					</label>
				</div>
				<div id="<?=$this->id?>-PT">
					<?php
						$mainPostType = get_post_types(array(
						  'public'   => true,
						  '_builtin' => false
						),'objects');
					?>
					<label for="<?php echo $this->get_field_id('posttype'); ?>">
						<select id="<?php echo $this->get_field_id('posttype'); ?>" name="<?php echo $this->get_field_name('posttype'); ?>">
							<?php foreach($mainPostType as $kpt => $pt) : ?>
								<option value="<?=$kpt?>" <?php if($posttype==$kpt) echo 'selected="selected"' ?>><?=$pt->labels->name?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('showimage'); ?>">
					<strong><?=__('Show thumbnail?','zaki')?>:</strong>&nbsp;
					<input id="<?php echo $this->get_field_id('showimage'); ?>" name="<?php echo $this->get_field_name('showimage'); ?>" type="checkbox" value="1" <?php if($showimage) echo 'checked="checked"' ?> />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('imagetype'); ?>">
					<?php $image_sizes = get_intermediate_image_sizes(); ?>
					<strong><?=__('Thumbnail size','zaki')?>:</strong><br />
					<select id="<?php echo $this->get_field_id('imagetype'); ?>" name="<?php echo $this->get_field_name('imagetype'); ?>">
						<?php foreach ($image_sizes as $size_name): ?>
						<option value="<?php echo $size_name ?>" <?php if($imagetype==$size_name) echo 'selected="selected"'; ?>><?php echo $size_name ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('imagelinked'); ?>">
					<strong><?=__('Put link on thumbnail?','zaki')?>:</strong>&nbsp;
					<input id="<?php echo $this->get_field_id('imagelinked'); ?>" name="<?php echo $this->get_field_name('imagelinked'); ?>" type="checkbox" value="1" <?php if($imagelinked) echo 'checked="checked"' ?> />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('showdate'); ?>">
					<strong><?=__('Show post date?','zaki')?>:</strong>&nbsp;
					<input id="<?php echo $this->get_field_id('showdate'); ?>" name="<?php echo $this->get_field_name('showdate'); ?>" type="checkbox" value="1" <?php if($showdate) echo 'checked="checked"' ?> />
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('showarchive'); ?>">
					<strong><?=__('Show archive link?','zaki')?>:</strong>&nbsp;
					<input id="<?php echo $this->get_field_id('showarchive'); ?>" name="<?php echo $this->get_field_name('showarchive'); ?>" type="checkbox" value="1" <?php if($showarchive) echo 'checked="checked"' ?> /><br />
					<em>(<?=__('In case of multiple categories will be used only the first','zaki')?>)</em>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('textlength'); ?>">
					<strong><?=__('Length of the text','zaki')?>:</strong>&nbsp;
					<input size="2" id="<?php echo $this->get_field_id('textlength'); ?>" name="<?php echo $this->get_field_name('textlength'); ?>" type="text" value="<?php echo esc_attr($textlength); ?>" /><br />
					<em>(<?=__('Write the number of words','zaki')?>)</em>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('pausetime'); ?>">
					<strong><?=__('Delay slide','zaki')?>:</strong>&nbsp;
					<input size="4" id="<?php echo $this->get_field_id('pausetime'); ?>" name="<?php echo $this->get_field_name('pausetime'); ?>" type="text" value="<?php echo esc_attr($pausetime); ?>" /><br />
					<em>(<?=__('In ms','zaki')?>)</em>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('scrolltype'); ?>">
					<strong><?=__('Type of animation','zaki')?>:</strong><br />
					<select id="<?php echo $this->get_field_id('scrolltype'); ?>" name="<?php echo $this->get_field_name('scrolltype'); ?>">
						<option value="horizontal" <?php if($scrolltype=='horizontal') echo 'selected="selected"'; ?>><?=__('Horizontal','zaki')?></option>
						<option value="vertical" <?php if($scrolltype=='vertical') echo 'selected="selected"'; ?>><?=__('Vertical','zaki')?></option>
						<option value="fade" <?php if($scrolltype=='fade') echo 'selected="selected"'; ?>><?=__('Fade','zaki')?></option>
					</select>
				</label>
			</p>
					
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('#<?=$this->get_field_id('isposttype')?>').change(function(){
						var selChoose = jQuery(this).val();
						if(selChoose==0) { 
							jQuery('#<?=$this->id?>-CAT').show();
							jQuery('#<?=$this->id?>-PT').hide(); 
						} else {
							jQuery('#<?=$this->id?>-PT').show();
							jQuery('#<?=$this->id?>-CAT').hide();
						}
					}).trigger('change');
				});
			</script>
			
			<?php
		}
		
		// Settings update
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = $new_instance['title'];
			$instance['titlelink'] = $new_instance['titlelink'];
			$instance['customclass'] = $new_instance['customclass'];
			$instance['number'] = $new_instance['number'];
			$instance['block'] = $new_instance['block'];
			$instance['orderby'] = $new_instance['orderby'];
			$instance['order'] = $new_instance['order'];
			$instance['isposttype'] = $new_instance['isposttype'];
			$instance['categories'] = $new_instance['categories'];
			$instance['posttype'] = $new_instance['posttype'];
			$instance['showimage'] = $new_instance['showimage'];
			$instance['imagetype'] = $new_instance['imagetype'];
			$instance['imagelinked'] = $new_instance['imagelinked'];
			$instance['showdate'] = $new_instance['showdate'];
			$instance['showarchive'] = $new_instance['showarchive'];
			$instance['textlength'] = $new_instance['textlength'];
			$instance['pausetime'] = $new_instance['pausetime'];
			$instance['scrolltype'] = $new_instance['scrolltype'];
			return $instance;
		}
		
		// Output widget
		function widget( $args, $instance ) {
			extract($args, EXTR_SKIP);

			echo $before_widget;
			
			// Data
			$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			$titlelink = ($instance['titlelink']) ? true : false;
			$customclass = $instance['customclass'];
			$number = $instance['number'];
			$block = $instance['block'];
			$orderby = $instance['orderby'];
			$order = $instance['order'];
			$isposttype = ($instance['isposttype']) ? true : false;
			$categories = (is_array($instance['categories'])) ? implode(',',$instance['categories']) : '';
			$posttype = $instance['posttype'];
			$showimage = ($instance['showimage']) ? true : false;
			$imagetype = $instance['imagetype'];
			$imagelinked = ($instance['imagelinked']) ? true : false;
			$showdate = ($instance['showdate']) ? true : false;
			$showarchive = ($instance['showarchive']) ? true : false;
			$textlength = $instance['textlength'];
			$pausetime = $instance['pausetime'];
			$scrolltype = $instance['scrolltype'];
			
			$arrayPostsUniqueArgs = array(
				'posts_per_page' => $number,
				'numberposts' => $number,
				'orderby' => $orderby,
				'order' => $order
			);

			if(!$isposttype) :
				$arrayPostsUniqueArgs['category'] = $categories;
				$archivelink = ($showarchive) ? get_category_link(array_shift($instance['categories'])) : '#';
			else :
				$arrayPostsUniqueArgs['post_type'] = $posttype;
				$archivelink = ($showarchive) ? get_post_type_archive_link($posttype) : '#';
			endif;
			$arrayPostsUnique = get_posts($arrayPostsUniqueArgs);

			//Array slice
			$arrayPosts = array_chunk($arrayPostsUnique, $block);
			
			// Output widget
			if(!empty($title)) {
			    echo $before_title;
			    if(!$titlelink) { echo $title; } else { echo '<a href="'.$archivelink.'">'.$title.'</a>'; }
			    echo $after_title;
			}
			
			if($arrayPosts) :
				?>
								
				<script type="text/javascript">
					jQuery(document).ready(function(){
						var lenScroller = jQuery('#<?=$widget_id?>-scroller > div').length;
						if(lenScroller > 1) {
							jQuery('#<?=$widget_id?>-scroller').bxSlider({
								mode: '<?=$scrolltype?>',
								auto: true,
								pager: true,
								controls: false,
								pause: <?=$pausetime?>,
								pagerSelector: jQuery('#<?=$widget_id?>-pager')
							});
						}

						<?php if($customclass!='') : ?>
						jQuery('#<?=$widget_id?>').addClass('<?=$customclass?>');
						<?php endif; ?>
					});
				</script>
				
				<div id="<?=$widget_id?>-scroller" class="zakiPostSlideWidgetScroll">
	            	<?php foreach($arrayPosts as $aposts) : ?>
					<div>
	                    <?php foreach($aposts as $ap) : ?>
	                    	<div>
	                    		<?php if($showdate) : ?><span><em>[<?=mysql2date(get_option('date_format'),$ap->post_date)?>]</em></span><?php endif; ?>
	                    		<h2><a href="<?=get_permalink($ap->ID)?>"><?=$ap->post_title?></a></h2>
	                    		<?php if($showimage and has_post_thumbnail($ap->ID)) : ?>
	                    			<?php if($imagelinked) { ?><a href="<?=get_permalink($ap->ID)?>"><?php } ?>
	                    			    <?=get_the_post_thumbnail($ap->ID,$imagetype)?>
	                    			<?php if($imagelinked) { ?></a><?php } ?>
	                    		<?php endif; ?>
	                    		<p><?=wp_trim_words($ap->post_content,$textlength,'...')?></p>
	                    	</div>
	                    <?php endforeach; ?>            
	                </div>
	                <?php endforeach; ?>
		        </div>
		        <div id="<?=$widget_id?>-pager" class="zakiPostSlideWidgetPager"></div>
		        
		        <?php if($showarchive) : ?>
					<div class="zakiPostSlideWidgetArchive">
						<a href="<?=$archivelink?>" ><?=__('&raquo;&nbsp;Archive','zaki')?></a>
					</div>
				<?php endif; ?>
								
				<?php
			endif;
			
			echo $after_widget;
		}
		
	}
	
	/* Widget registration */
	function zakiPostSlideWidgetRegister() {
		register_widget( 'zakiPostSlideWidget' );
	}
	add_action( 'widgets_init', 'zakiPostSlideWidgetRegister' );
?>