<?php
/*-----------------------------------------------------------------------------------*/
/* Add meta boxes */
/*-----------------------------------------------------------------------------------*/
add_action ('add_meta_boxes','discy_builder_meta_boxes');
function discy_builder_meta_boxes($post_type) {
	global $post;
	$allow_post_type = apply_filters("discy_allow_post_type",array('post','page','question'));
	if (in_array($post_type,$allow_post_type)) {
		add_meta_box ('discy_meta_tabs',esc_html__('Page settings',"discy"),'discy_meta_tabs',$post_type,'normal','high');
	}
}
/*-----------------------------------------------------------------------------------*/
/* discy_categories_ajax */
/*-----------------------------------------------------------------------------------*/
function discy_categories_ajax () {
	$name = (isset($_POST["name"])?esc_attr($_POST["name"]):"");
	$name_2 = (isset($_POST["name_2"])?esc_attr($_POST["name_2"]):"");
	$tabs = (isset($_POST["tabs"])?esc_attr($_POST["tabs"]):"");
	if ($tabs == "yes") {
		echo '<li><label class="selectit"><input value="on" type="checkbox" name="'.$name.'[show_all_categories]">'.esc_html__('Show All Categories',"discy").'</label></li>';
	}
	echo discy_categories_checklist(array("name" => $name.$name_2,"id" => $name.$name_2));
	die();
}
add_action('wp_ajax_discy_categories_ajax','discy_categories_ajax');
/*-----------------------------------------------------------------------------------*/
/* Page settings */
/*-----------------------------------------------------------------------------------*/
function discy_meta_tabs() {
	global $post;
	wp_nonce_field ('discy_builder_save_meta','discy_save_meta_nonce');?>
	<div id="discy-admin-wrap" class="discy-admin">
		<div class="discy-admin-header">
			<a href="<?php echo discy_theme_url_tf?>" target="_blank"><i class="dashicons-before dashicons-admin-tools"></i><?php echo discy_theme_name?></a>
			<div class="discy_social">
				<ul>
					<li class="discy_social_facebook"><a class="discy_social_f" href="https://www.facebook.com/2code.info" target="_blank"><i class="dashicons dashicons-facebook"></i></a></li>
					<li class="discy_social_twitter"><a class="discy_social_t" href="https://www.twitter.com/2codeThemes" target="_blank"><i class="dashicons dashicons-twitter"></i></a></li>
					<li class="discy_social_site"><a class="discy_social_e" href="https://2code.info/" target="_blank"><i class="dashicons dashicons-email-alt"></i></a></li>
					<li class="discy_social_docs"><a class="discy_social_s" href="https://2code.info/docs/discy/" target="_blank"><i class="dashicons dashicons-sos"></i></a></li>
				</ul>
			</div>
			<div class="clear"></div>
		</div>
		<div class="discy-admin-content">
		    <h2 class="nav-tab-wrapper">
		        <?php echo discy_admin_fields_class::discy_admin_tabs("meta",discy_admin_meta(),$post->ID);?>
		    </h2>
		    <?php settings_errors( 'options-framework' ); ?>
		    <div id="discy-admin-metabox" class="metabox-holder">
			    <div id="discy-admin" class="discy_framework postbox">
			    	<?php discy_admin_fields_class::discy_admin_fields("meta",discy_meta,"meta",$post->ID,discy_admin_meta());?>
				</div> <!-- / #container -->
			</div>
		</div>
		<div class="clear"></div>
	</div> <!-- / .wrap -->
	<?php 
}
/*-----------------------------------------------------------------------------------*/
/* Process builder meta box */
/*-----------------------------------------------------------------------------------*/
add_action ('save_post','discy_builder_meta_save',1,2);
function discy_builder_meta_save ($post_id,$post) {
	if (!isset($_POST)) return $post_id;
	$allow_post_type = apply_filters("discy_allow_post_type",array('post','page','question'));
	if (!in_array($post->post_type,$allow_post_type)) return $post_id;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
	if (!isset($_POST['discy_save_meta_nonce']) || !wp_verify_nonce ($_POST['discy_save_meta_nonce'],'discy_builder_save_meta')) return $post_id;
	if (!current_user_can ('edit_post',$post_id)) return $post_id;
	
	if ($post->post_type == 'question') {
		$data = array();
				
		// Get Question Category
		$data['discy_question_category'] = (isset($_POST['discy_question_category'])?stripslashes( $_POST['discy_question_category'] ):"");
		
		$get_question_user_id = discy_post_meta("user_id","",false);
		
		// category
		if (empty($get_question_user_id) && isset($_POST['discy_question_category'])) {
			$new_term_slug = get_term_by( 'id', (isset($_POST['discy_question_category'])?stripslashes( $_POST['discy_question_category'] ):""), 'question-category')->slug;
			wp_set_object_terms( $post->ID, $new_term_slug, 'question-category' );
		}
		
		$sticky_questions = get_option('sticky_questions');
		$sticky_posts = get_option('sticky_posts');
		if (isset($_POST['sticky_question']) && $_POST['sticky_question'] == "sticky" && isset($_POST['sticky']) && $_POST['sticky'] == "sticky") {
			update_post_meta($post_id,'sticky',1);
			if (is_array($sticky_questions)) {
				if (!in_array($post_id,$sticky_questions)) {
					$array_merge = array_merge($sticky_questions,array($post_id));
					update_option("sticky_questions",$array_merge);
				}
			}else {
				update_option("sticky_questions",array($post_id));
			}
			if (is_array($sticky_posts)) {
				if (!in_array($post_id,$sticky_posts)) {
					$array_merge = array_merge($sticky_posts,array($post_id));
					update_option("sticky_posts",$array_merge);
				}
			}else {
				update_option("sticky_posts",array($post_id));
			}
		}else {
			if (is_array($sticky_questions) && in_array($post_id,$sticky_questions)) {
				$sticky_questions = wpqa_remove_item_by_value($sticky_questions,$post_id);
				update_option('sticky_questions',$sticky_questions);
			}
			if (is_array($sticky_posts) && in_array($post_id,$sticky_posts)) {
				$sticky_posts = wpqa_remove_item_by_value($sticky_posts,$post_id);
				update_option('sticky_posts',$sticky_posts);
			}
			delete_post_meta($post_id,'sticky');
		}
	}
	
	$options = discy_admin_meta();
	foreach ($options as $value) {
		if ($value['type'] != 'heading' && $value['type'] != "heading-2" && $value['type'] != 'info' && $value['type'] != 'content') {
			$val = "";
			
			if (isset($value['std'])) {
				$val = $value['std'];
			}
			
			$field_name = $value['id'];
			
			if (isset($_POST[$field_name])) {
				$val = $_POST[$field_name];
			}
			
			if (!isset($_POST[$field_name]) && $value['type'] == "checkbox") {
				$val = 0;
			}
			
			if ('' === $val || array() === $val) {
				if (isset($value['save']) && $value['save'] == "option") {
					delete_option($field_name);
				}else {
					delete_post_meta($post->ID,$field_name);
				}
			}else if (isset($_POST[$field_name]) || $value['type'] == "checkbox") {
				if ($value['id'] == "question_poll" && $val != "on") {
					update_post_meta($post->ID,'question_poll',2);
				}else {
					if (isset($_POST["private_question"]) && ($_POST["private_question"] == "on" || $_POST["private_question"] == 1)) {
						$anonymously_user = discy_post_meta("anonymously_user","",false);
						update_post_meta($post->ID,'private_question_author',($anonymously_user > 0?$anonymously_user:$post->post_author));
					}
					if (isset($value['save']) && $value['save'] == "option") {
						update_option($field_name,$val);
						if (isset($_POST["tabs_menu"])) {
							update_option("home_page_id",$post->ID);
						}
					}else {
						update_post_meta($post->ID,$field_name,$val);
					}
				}
			}
		}
	}
}?>