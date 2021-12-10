<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

ob_start();
/* Categories checklist */
if (!function_exists('wpqa_categories_checklist')) :
	function wpqa_categories_checklist ($args = array()) {
		$defaults = array(
			'selected_cats' => false,
			'taxonomy' => 'category',
			'checkbox' => 'checkbox',
		);
		
		$r = wp_parse_args( $args, $defaults );
		$taxonomy = $r['taxonomy'];
		$args['name'] = $r['name'];
		$args['id'] = $r['id'];
		$args['selected_cats'] = $r['selected_cats'];
		$args['checkbox'] = $r['checkbox'];
		$categories = (array) get_terms( $taxonomy, array( 'get' => 'all' ) );
		$output = '';
		foreach ($categories as $key => $value) {
			$output .= '<li id="'.$args['name'].$taxonomy.'-'.$value->term_id.'">
				<label class="selectit"><span class="wpqa_'.$args['checkbox'].'"><input value="'.$value->term_id.'" '.(is_array($args['selected_cats']) && in_array($value->term_id,$args['selected_cats'])?checked($value->term_id,$value->term_id,false):'').' type="'.$args['checkbox'].'" name="'.$args['name'].'[]" id="'.$args['name'].'in-'.$taxonomy.'-'.$value->term_id.'"></span> '.$value->name.'</label>
			</li>';
		}
		return $output;
	}
endif;
/* Select categories */
if (!function_exists('wpqa_select_categories')) :
	function wpqa_select_categories ($rand,$select,$attr = array(),$post_id = '',$taxonomy = '') {
		$category_single_multi = wpqa_options("category_single_multi");
		if ($category_single_multi == "ajax" && $taxonomy != "category") {
			$attr = array(
				'name'             => 'category',
				'taxonomy'         => $taxonomy,
				'orderby'          => 'name',
				'order'            => 'ASC',
				'required'         => 'yes',
				'show_option_none' => esc_html__("Select a Category","wpqa"),
			);
			$out              = '';
			$rand             = rand(1,1000);
			$taxonomy         = $attr['taxonomy'];
			$show_option_none = $attr['show_option_none'];
			$class            = ' wpqa_'.$attr['name'].'_'.$rand;
			$div_class        = 'wpqa_'.$attr['name'].'_'.$rand;
			
			$terms = array();
			if ($post_id) {
				$terms = wp_get_post_terms($post_id,$taxonomy,array('fields' => 'ids'));
				if (!empty($terms) && is_array($terms)) {
					asort($terms);
				}
				$child_c = get_term(end($terms),$taxonomy);
				if ($child_c->parent > 0) {
					$terms[] = $child_c->parent;
				}
				
				while ($child_c->parent > 0) {
					$child_c = get_term($child_c->parent,$taxonomy);
					if (!is_wp_error($child_c)) {
						if ($child_c->parent > 0) {
							$terms[] = $child_c->parent;
							continue;
						}
					}else {
						break;
					}
				}
			}else {
				$terms = $select;
				if (!empty($terms) && is_array($terms)) {
					asort($terms);
				}
			}
			if (!empty($terms) && is_array($terms)) {
				$terms = array_unique($terms);
			}
			
			$out .= '<span class="category-wrap'.$class.'">';
				if (empty($terms) || (is_array($terms) && !count($terms))) {
					$out .= '<span id="level-0" data-level="0">'.
					wpqa_categories_select(null,$attr,0).'
					</span>';
				}else {
					$level = 0;
					$last_term_id = end($terms);
					foreach( $terms as $term_id) {
						$class = ($last_term_id != $term_id)?'hasChild':'';
						$out .= '<span id="wpqa-level-'.$level.'" data-level="'.$level.'" >'.
							wpqa_categories_select($term_id,$attr,$level).'
						</span>';
						$attr['parent_cat'] = $term_id;
						$level++;
					}
				}
			$out .= '</span>
			<span class="category_loader loader_2"></span>';
			return $out;
		}else if ($category_single_multi == "multi" && $taxonomy != "category") {
			$args = array(
				'selected_cats' => $select,
				'taxonomy'      => $taxonomy,
				'id'            => ($taxonomy == "question-category"?"question-category":"post-category").'-'.$rand,
				'name'          => 'category'
			);
			return '<ul class="row">'.wpqa_categories_checklist($args).'</ul>';
		}else {
			$select = (!empty($select) && is_array($select) && isset($select[0])?$select[0]:$select);
			return '<span class="styled-select">'.wp_dropdown_categories(array("orderby" => "name","echo" => "0","show_option_none" => esc_html__("Select a Category","wpqa"),'taxonomy' => $taxonomy, 'hide_empty' => 0,'depth' => 0,'id' => ($taxonomy == "question-category"?"question-category":"post-category").'-'.$rand,'name' => 'category','hierarchical' => true,'selected' => $select)).'</span>';
		}
	}
endif;
/* Child cats */
if (!function_exists('wpqa_child_cats')) :
	function wpqa_child_cats () {
		$parentCat  = esc_html($_POST['catID']);
		$field_attr = stripslashes($_POST['field_attr']);
		$field_attr = json_decode($field_attr, true);
		$taxonomy   = esc_html($field_attr['taxonomy']);
		$terms = null;
		$result = '';
		
		if ($parentCat < 1) {
			echo ($result);
			die();
		}
		
		$terms = get_terms(array('taxonomy' => $taxonomy,'child_of'=> $parentCat,'hide_empty'=> 0));
		if ($terms) {
			$field_attr['parent_cat'] = $parentCat;
			if ( is_array($terms)) {
				foreach ($terms as $key => $term) {
					$terms[$key] = (array)$term;
				}
			}
			$result .= wpqa_categories_select(null,$field_attr,0);
		}else {
			die();
		}
		
		echo ($result);
		die();
	}
endif;
add_action('wp_ajax_wpqa_child_cats','wpqa_child_cats');
add_action('wp_ajax_nopriv_wpqa_child_cats','wpqa_child_cats');
/* Categories select */
if (!function_exists('wpqa_categories_select')) :
	function wpqa_categories_select ($terms,$attr,$level) {
		$out              = '';
		$selected         = $terms ? $terms : '';
		$required         = sprintf('data-required="%s" data-type="select"',$attr['required']);
		$taxonomy         = $attr['taxonomy'];
		$rand             = rand(1,1000);
		$class            = ' wpqa_'.$attr['name'].'_'.$rand.'_'.$level;
		$multi            = (isset($attr['multi'])?$attr['multi']:'[]');
		$show_option_none = (isset($attr['show_option_none'])?$attr['show_option_none']:esc_html__('Select a Category','wpqa'));
		
		$select = wp_dropdown_categories(array(
			'show_option_none' => $show_option_none,
			'hierarchical'     => 1,
			'hide_empty'       => 0,
			'orderby'          => isset($attr['orderby'])?$attr['orderby']:'name',
			'order'            => isset($attr['order'])?$attr['order']:'ASC',
			'name'             => $attr['name'].$multi,
			'taxonomy'         => $taxonomy,
			'echo'             => 0,
			'title_li'         => '',
			'class'            => 'cat-ajax '.$taxonomy.$class,
			'id'               => 'cat-ajax '.$taxonomy.$class,
			'selected'         => $selected,
			'depth'            => 1,
			'child_of'         => isset($attr['parent_cat'])?$attr['parent_cat']:''
		));
		
		$attr = array(
			'required'     => $attr['required'],
			'name'         => $attr['name'],
			'orderby'      => $attr['orderby'],
			'order'        => $attr['order'],
			'name'         => $attr['name'],
			'taxonomy'     => $attr['taxonomy'],
		);
		
		$out .= '<span class="styled-select">'.str_replace('<select','<select data-taxonomy='.json_encode($attr).' '.$required,$select).'</span>';
		
		return $out;
	}
endif;
/* Ask question */
function wpqa_add_question($type) {
	if (isset($_POST["form_type"]) && $_POST["form_type"] == "add_question" && empty($_POST["user_id"])) :
		$return = wpqa_process_new_questions();
		if (is_wp_error($return)) :
   			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
   		else :
   			$get_post = get_post($return);
   			if ($get_post->post_type == "question") {
   				$get_current_user_id = get_current_user_id();
	   			if (is_user_logged_in()) {
   					$question_publish = wpqa_options("question_publish");
   				}else {
   					$question_publish = wpqa_options("question_publish_unlogged");
   				}
	   			$approved_questions = wpqa_options("approved_questions");
	   			$question_status = "publish";
	   			if ($question_publish == "draft" && !is_super_admin($get_current_user_id)) {
	   				$question_status = "draft";
	   				if ($approved_questions == "on") {
	   					$questions_count = wpqa_count_posts_by_user($get_current_user_id,"question");
	   					if ($questions_count > 0) {
	   						$question_status = "publish";
	   					}
	   				}
	   			}
	   			
	   			if ($question_status == "draft") {
	   				$send_email_draft_questions = wpqa_options("send_email_draft_questions");
	   				if ($send_email_draft_questions == "on") {
	   					$send_text = wpqa_send_email(wpqa_options("email_draft_questions"),"",$return);
	   						$last_message_email = wpqa_email_code($send_text);
	   						$email_title = wpqa_options("title_new_draft_questions");
	   						$email_title = ($email_title != ""?$email_title:esc_html__("New question for review","wpqa"));
	   						wpqa_sendEmail(wpqa_options("email_template"),get_bloginfo('name'),get_bloginfo("admin_email"),get_bloginfo('name'),$email_title,$last_message_email);
	   				}
					wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Your question was successfully added, It's under review.","wpqa").'</p></div>','wpqa_session');
					if ($get_current_user_id > 0) {
						wpqa_notifications_activities($get_current_user_id,"","","","","approved_question","activities","","question");
					}
					wp_redirect(esc_url(home_url('/')));
				}else {
					$the_author = 0;
					if ($get_post->post_author == 0) {
						$the_author = get_post_meta($return,'question_username',true);
					}
					
					$user_id = get_post_meta($return,"user_id",true);
					if ($user_id == "") {
						$anonymously_user = get_post_meta($return,"anonymously_user",true);
						$not_user = ($get_post->post_author > 0?$get_post->post_author:0);
						wpqa_notifications_add_question($return,$the_author,$user_id,$not_user,$anonymously_user,$get_current_user_id);
					}
					update_post_meta($return,'post_approved_before',"yes");
					
					if ($get_current_user_id > 0) {
						wpqa_notifications_activities($get_current_user_id,"","",$return,"","add_question","activities","","question");
					}
					wpqa_post_publish($return,$get_post->post_type,$the_author,$user_id,$not_user,$anonymously_user,$get_current_user_id);
					wp_redirect(get_permalink($return));
				}
			}
			exit;
   		endif;
	endif;
}
add_filter('wpqa_add_question','wpqa_add_question');
/* User question */
function wpqa_user_question($type) {
	if (isset($_POST["form_type"]) && $_POST["form_type"] == "add_question" && isset($_POST["user_id"]) && $_POST["user_id"] != "") :
		$return = wpqa_process_new_questions();
		if (is_wp_error($return)) :
   			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
   		else :
   			$get_post = get_post($return);
   			if ($get_post->post_type == "question") {
   				$user_id = get_current_user_id();
   				if (is_user_logged_in()) {
	   				$question_publish = wpqa_options("question_publish");
	   			}else {
	   				$question_publish = wpqa_options("question_publish_unlogged");
	   			}
   				$approved_questions = wpqa_options("approved_questions");
   				$question_status = "publish";
   				if ($question_publish == "draft" && !is_super_admin($user_id)) {
   					$question_status = "draft";
   					if ($approved_questions == "on") {
   						$questions_count = wpqa_count_posts_by_user($user_id,"question");
   						if ($questions_count > 0) {
   							$question_status = "publish";
   						}
   					}
   				}
   				
   				if ($question_status == "draft") {
					wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Your question was successfully added, It's under review.","wpqa").'</p></div>','wpqa_session');
					if ($user_id > 0) {
						wpqa_notifications_activities($user_id,"","","","","approved_question","activities","","question");
					}
					wp_redirect(esc_url(home_url('/')));
				}else {
					$get_question_user = get_post_meta($get_post->ID,"user_id",true);
					
					update_post_meta($return,'post_approved_before',"yes");
					
					if ($get_post->post_author != $get_question_user && $get_question_user > 0) {
						wpqa_notifications_activities($get_question_user,$get_post->post_author,"",$get_post->ID,"","add_question_user","notifications","","question");
					}
					if ($user_id > 0) {
						wpqa_notifications_activities($user_id,"","",$return,"","add_question","activities","","question");
					}
					wp_redirect(get_permalink($return));
				}
			}
			exit;
   		endif;
   	elseif (isset($_POST["form_type"]) && $_POST["form_type"] == "edit_question") :
		$return = wpqa_process_edit_questions();
		if (is_wp_error($return)) :
			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
		else :
			$question_approved = wpqa_options("question_approved");
			if ($question_approved == "on" || is_super_admin(get_current_user_id())) {
				wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Has been edited successfully.","wpqa").'</p></div>','wpqa_session');
				wp_redirect(get_permalink($return));
			}else {
				wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Has been Edited successfully, The question is under review.","wpqa").'</p></div>','wpqa_session');
				wp_redirect(esc_url(home_url('/')));
			}
			exit;
		endif;
	endif;
}
add_filter('wpqa_user_question','wpqa_user_question');
/* Process new questions */
function wpqa_process_new_questions() {
	global $question_add;
	set_time_limit(0);
	$errors = new WP_Error();
	$question_add = array();
	$form_type = (isset($_POST["form_type"]) && $_POST["form_type"] != ""?$_POST["form_type"]:"");
	if ($form_type == "add_question") {
		$user_id = get_current_user_id();
		$ask_question_no_register = wpqa_options("ask_question_no_register");
		$question_points_active = wpqa_options("question_points_active");
		$question_points = (int)wpqa_options("question_points");
		$points = (int)get_user_meta($user_id,"points",true);
		$active_points = wpqa_options("active_points");
		$poll_image_title = wpqa_options("poll_image_title");
		$poll_image_title_required = wpqa_options("poll_image_title_required");
		
		$fields = array(
			'title','comment','category','question_poll','question_image_poll','remember_answer','question_tags','video_type','video_id','video_description','sticky','ask','private_question','anonymously_question','attachment','attachment_m','featured_image','wpqa_captcha','username','email','terms_active','user_id'
		);
		
		$fields = apply_filters('wpqa_add_question_fields',$fields);
		
		foreach ($fields as $field) :
			if (isset($_POST[$field])) $question_add[$field] = $_POST[$field]; else $question_add[$field] = '';
		endforeach;

		if (!isset($_POST['wpqa_add_question_nonce']) || !wp_verify_nonce($_POST['wpqa_add_question_nonce'],'wpqa_add_question_nonce')) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are an error, Please try again.","wpqa"));
		}
		
		$wpqa_add_question_user = (int)wpqa_add_question_user();
		if ($wpqa_add_question_user > 0) {
			$question_sort = wpqa_options("ask_user_items");
			$comment_question = wpqa_options("content_ask_user");
			$editor_question_details = wpqa_options("editor_ask_user");
		}else {
			$question_sort = wpqa_options("ask_question_items");
			$comment_question = wpqa_options("comment_question");
			$editor_question_details = wpqa_options("editor_question_details");
		}
		
		$payment_group = wpqa_options("payment_group");
		$pay_ask = wpqa_options("pay_ask");
		$custom_permission = wpqa_options("custom_permission");
		$ask_question_no_register = wpqa_options("ask_question_no_register");
		$ask_question = wpqa_options("ask_question");
		if (is_user_logged_in()) {
			$user_is_login = get_userdata($user_id);
			$user_login_group = key($user_is_login->caps);
			$roles = $user_is_login->allcaps;
			$_allow_to_ask = (int)get_user_meta($user_id,$user_id."_allow_to_ask",true);
		}
		
		if (($custom_permission == "on" && is_user_logged_in() && !is_super_admin($user_id) && empty($roles["ask_question"])) || ($custom_permission == "on" && !is_user_logged_in() && $ask_question != "on")) {
			$errors->add('required','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Sorry, you do not have a permission to add a question.","wpqa"));
			if (!is_user_logged_in()) {
				$errors->add('required','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("You must login to ask question.","wpqa"));
			}
		}else if (!is_user_logged_in() && $ask_question_no_register != "on") {
			$errors->add('required','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("You must login to ask question.","wpqa"));
		}else {
			if (!is_user_logged_in() && $pay_ask == "on") {
				$errors->add('required','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("You must login to ask question.","wpqa"));
			}else {
				if (isset($_allow_to_ask) && (int)$_allow_to_ask < 1 && $pay_ask == "on" && $payment_group[$user_login_group] != $user_login_group) {
					$errors->add('required','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("You need to pay first.","wpqa"));
				}
			}
		}
		
		if ($points < $question_points && $question_points_active == "on" && $active_points == "on") $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.sprintf(esc_html__("Sorry do not have the minimum points Please do answer questions, even gaining points (The minimum points = %s).","wpqa"),$question_points));
		
		if (!is_user_logged_in() && $ask_question_no_register == "on" && $user_id == 0) {
			if (empty($question_add['username'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (username).","wpqa"));
			if (empty($question_add['email'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (email).","wpqa"));
			if (!is_email($question_add['email'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Please write correctly email.","wpqa"));
		}
		
		/* Validate Required Fields */
		
		if (empty($question_add['title'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (title).","wpqa"));
		
		if (empty($question_add['user_id']) && isset($question_sort["categories_question"]["value"]) && $question_sort["categories_question"]["value"] == "categories_question" && (empty($question_add['category']) || $question_add['category'] == '-1')) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (category).","wpqa"));
		
		if (isset($question_add['question_poll']) && $question_add['question_poll'] == "on") {
			if (isset($question_add['ask']) && is_array($question_add['ask'])) {
				foreach($question_add['ask'] as $ask) {
					if (count($question_add['ask']) < 2) {
						$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Please enter at least two values in poll.","wpqa"));
					}
					if (((isset($question_add['question_image_poll']) && $question_add['question_image_poll'] == "on" && $poll_image_title == "on" && $poll_image_title_required == "on") || (!isset($question_add['question_image_poll']) && $poll_image_title != "on" && $poll_image_title_required != "on")) && empty($ask['title'])) {
						$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Please enter values in poll.","wpqa"));
					}
				}
			}else {
				$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Please enter at least two values in poll.","wpqa"));
			}
		}
		
		if (isset($question_sort["comment_question"]["value"]) && $question_sort["comment_question"]["value"] == "comment_question" && $comment_question == "on" && empty($question_add['comment'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (content).","wpqa"));
		if (isset($question_sort["video_desc_active"]["value"]) && $question_sort["video_desc_active"]["value"] == "video_desc_active" && $question_add['video_description'] == "on" && empty($question_add['video_id'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Video ID).","wpqa"));
		
		$the_captcha = wpqa_options("the_captcha");
		$captcha_users = wpqa_options("captcha_users");
		$captcha_style = wpqa_options("captcha_style");
		$captcha_question = wpqa_options("captcha_question");
		$captcha_answer = wpqa_options("captcha_answer");
		$show_captcha_answer = wpqa_options("show_captcha_answer");
		if ($the_captcha == "on" && ($captcha_users == "both" || ($captcha_users == "unlogged" && !is_user_logged_in()))) {
			if (empty($question_add["wpqa_captcha"])) {
				$errors->add('required-captcha',esc_html__("There are required fields (captcha).","wpqa"));
			}
			if ($captcha_style == "question_answer") {
				if ($captcha_answer != $question_add["wpqa_captcha"]) {
					$errors->add('required-captcha-error',esc_html__('The captcha is incorrect, Please try again.','wpqa'));
				}
			}else {
				if(!session_id()) session_start();
				if (isset($_SESSION["wpqa_code_captcha_question"]) && $_SESSION["wpqa_code_captcha_question"] != $question_add["wpqa_captcha"]) {
					$errors->add('required-captcha-error',esc_html__('The captcha is incorrect, Please try again.','wpqa'));
				}
			}
		}
		
		if (isset($question_sort["terms_active"]["value"]) && $question_sort["terms_active"]["value"] == "terms_active" && $question_add['terms_active'] != "on") {
			$errors->add('required-terms',esc_html__("There are required fields (Agree of the terms).","wpqa"));
		}
		
		do_action('wpqa_add_question_errors',$errors,$question_add);
		
		if (sizeof($errors->errors)>0) return $errors;
		
		if (is_user_logged_in()) {
			$question_publish = wpqa_options("question_publish");
		}else {
			$question_publish = wpqa_options("question_publish_unlogged");
		}
		$approved_questions = wpqa_options("approved_questions");
		$question_status = "publish";
		if ($question_publish == "draft" && !is_super_admin($user_id)) {
			$question_status = "draft";
			if ($approved_questions == "on") {
				$questions_count = wpqa_count_posts_by_user($user_id,"question");
				if ($questions_count > 0) {
					$question_status = "publish";
				}
			}
		}
		
		/* Create question */
		
		$data = array(
			'post_content' => ($editor_question_details == "on"?wpqa_kses_stip($question_add['comment']):wpqa_kses_stip_wpautop($question_add['comment'])),
			'post_title'   => wpqa_kses_stip($question_add['title']),
			'post_status'  => $question_status,
			'post_author'  => ((!is_user_logged_in() && $ask_question_no_register == "on") || $question_add['anonymously_question']?0:$user_id),
			'post_type'	   => 'question',
		);
			
		$question_id = wp_insert_post($data);
			
		if ($question_id == 0 || is_wp_error($question_id)) wp_die(esc_html__("Error in question.","wpqa"));
		
		if (empty($question_add['user_id']) && isset($question_sort["categories_question"]["value"]) && $question_sort["categories_question"]["value"] == "categories_question" && isset($question_add['category']) && $question_add['category']) {
			if (is_array($question_add['category'])) {
				$cat_ids = array_map( 'intval', $question_add['category'] );
				$cat_ids = array_unique( $cat_ids );
			}else {
				$cat_ids = array();
				$cat_ids[] = get_term_by('id',(is_array($question_add['category'])?end($question_add['category']):$question_add['category']),'question-category')->slug;
			}
			if (sizeof($cat_ids)>0) :
				wp_set_object_terms($question_id,$cat_ids,'question-category');
			endif;
		}
		
		if ($question_add['question_poll'] && $question_add['question_poll'] != "")  {
			update_post_meta($question_id,'question_poll',$question_add['question_poll']);
			if ($question_add['question_image_poll']) {
				update_post_meta($question_id,'question_image_poll',esc_html($question_add['question_image_poll']));
			}
		}else {
			update_post_meta($question_id,'question_poll',2);
		}
		
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		
		if (isset($_FILES['ask'])) {
			$files = $_FILES['ask'];
			if (isset($files) && $files) {
				foreach ($files['name'] as $key => $value) {
					if ($files['name'][$key]) {
						$file = array(
							'name'	 => $files['name'][$key]["image"],
							'type'	 => $files['type'][$key]["image"],
							'tmp_name' => $files['tmp_name'][$key]["image"],
							'error'	=> $files['error'][$key]["image"],
							'size'	 => $files['size'][$key]["image"]
						);
						if ($files['error'][$key]["image"] != 0) {
							unset($files['name'][$key]);
							unset($files['type'][$key]);
							unset($files['tmp_name'][$key]);
							unset($files['error'][$key]);
							unset($files['size'][$key]);
						}
					}
				}
			}
			
			if (isset($files) && $files) {
				foreach ($files['name'] as $key => $value) {
					if ($files['name'][$key]) {
						$file = array(
							'name'	 => $files['name'][$key]["image"],
							'type'	 => $files['type'][$key]["image"],
							'tmp_name' => $files['tmp_name'][$key]["image"],
							'error'	=> $files['error'][$key]["image"],
							'size'	 => $files['size'][$key]["image"]
						);
						$attachment = wp_handle_upload($file,array('test_form'=>false),current_time('mysql'));
						if (!isset($attachment['error']) && $attachment) :
							$attachment_data = array(
								'post_mime_type' => $attachment['type'],
								'post_title'	 => preg_replace('/\.[^.]+$/','',basename($attachment['file'])),
								'post_content'   => '',
								'post_status'	 => 'inherit',
								'post_author'    => ((!is_user_logged_in() && $ask_question_no_register == "on") || $question_add['anonymously_question']?0:$user_id),
							);
							$attachment_id = wp_insert_attachment($attachment_data,$attachment['file'],$question_id);
							$attachment_metadata = wp_generate_attachment_metadata($attachment_id,$attachment['file']);
							wp_update_attachment_metadata($attachment_id,$attachment_metadata);
							$question_add['ask'][$key]["image"] = array("id" => $attachment_id,"url" => wp_get_attachment_url($attachment_id));
						endif;
					}
				}
			}
		}

		if (isset($question_add['ask']) && $question_add['ask'] != "") {
			update_post_meta($question_id,'ask',$question_add['ask']);
		}else {
			delete_post_meta($question_id,'ask');
		}
		
		if ($question_add['remember_answer']) {
			update_post_meta($question_id,'remember_answer',esc_html($question_add['remember_answer']));
		}
		
		if ($question_add['private_question']) {
			update_post_meta($question_id,'private_question',esc_html($question_add['private_question']));
			update_post_meta($question_id,'private_question_author',((!is_user_logged_in() && $ask_question_no_register == "on") || $question_add['anonymously_question']?0:$user_id));
		}
		
		if ($question_add['anonymously_question']) {
			update_post_meta($question_id,'anonymously_question',esc_html($question_add['anonymously_question']));
			update_post_meta($question_id,'anonymously_user',(is_user_logged_in()?$user_id:0));
		}
		
		if ($question_add['user_id'] && $question_add['user_id'] != "") {
			update_post_meta($question_id,'user_id',(int)$question_add['user_id']);
		}
		
		if (isset($question_sort["video_desc_active"]["value"]) && $question_sort["video_desc_active"]["value"] == "video_desc_active") {
			if ($question_add['video_description'])
				update_post_meta($question_id,'video_description',esc_html($question_add['video_description']));
			
			if ($question_add['video_type'])
				update_post_meta($question_id,'video_type',esc_html($question_add['video_type']));
				
			if ($question_add['video_id'])
				update_post_meta($question_id,'video_id',esc_html($question_add['video_id']));
		}
		
		$sticky_questions = get_option('sticky_questions');
		$sticky_posts = get_option('sticky_posts');
		if (isset($question_add['sticky']) && $question_add['sticky'] == "sticky") {
			update_post_meta($question_id,'sticky',1);
			if (is_array($sticky_questions)) {
				if (!in_array($question_id,$sticky_questions)) {
					$array_merge = array_merge($sticky_questions,array($question_id));
					update_option("sticky_questions",$array_merge);
				}
			}else {
				update_option("sticky_questions",array($question_id));
			}
			if (is_array($sticky_posts)) {
				if (!in_array($question_id,$sticky_posts)) {
					$array_merge = array_merge($sticky_posts,array($question_id));
					update_option("sticky_posts",$array_merge);
				}
			}else {
				update_option("sticky_posts",array($question_id));
			}
		}else {
			if (is_array($sticky_questions) && in_array($question_id,$sticky_questions)) {
				$sticky_questions = wpqa_remove_item_by_value($sticky_questions,$question_id);
				update_option('sticky_questions',$sticky_questions);
			}
			if (is_array($sticky_posts) && in_array($question_id,$sticky_posts)) {
				$sticky_posts = wpqa_remove_item_by_value($sticky_posts,$question_id);
				update_option('sticky_posts',$sticky_posts);
			}
			delete_post_meta($question_id,'sticky');
		}
		
		if (isset($_FILES['attachment_m'])) {
			$files = $_FILES['attachment_m'];
			if (isset($files) && $files) {
				foreach ($files['name'] as $key => $value) {
					if ($files['name'][$key]) {
						$file = array(
							'name'	 => $files['name'][$key]["file_url"],
							'type'	 => $files['type'][$key]["file_url"],
							'tmp_name' => $files['tmp_name'][$key]["file_url"],
							'error'	=> $files['error'][$key]["file_url"],
							'size'	 => $files['size'][$key]["file_url"]
						);
						if ($files['error'][$key]["file_url"] != 0) {
							unset($files['name'][$key]);
							unset($files['type'][$key]);
							unset($files['tmp_name'][$key]);
							unset($files['error'][$key]);
							unset($files['size'][$key]);
						}
					}
				}
			}
			
			if (isset($files) && $files) {
				foreach ($files['name'] as $key => $value) {
					if ($files['name'][$key]) {
						$file = array(
							'name'	 => $files['name'][$key]["file_url"],
							'type'	 => $files['type'][$key]["file_url"],
							'tmp_name' => $files['tmp_name'][$key]["file_url"],
							'error'	=> $files['error'][$key]["file_url"],
							'size'	 => $files['size'][$key]["file_url"]
						);
						$attachment = wp_handle_upload($file,array('test_form'=>false),current_time('mysql'));
						if (!isset($attachment['error']) && $attachment) :
							$attachment_data = array(
								'post_mime_type' => $attachment['type'],
								'post_title'	 => preg_replace('/\.[^.]+$/','',basename($attachment['file'])),
								'post_content'   => '',
								'post_status'	 => 'inherit',
								'post_author'    => ((!is_user_logged_in() && $ask_question_no_register == "on") || $question_add['anonymously_question']?0:$user_id),
							);
							$attachment_id = wp_insert_attachment($attachment_data,$attachment['file'],$question_id);
							$attachment_metadata = wp_generate_attachment_metadata($attachment_id,$attachment['file']);
							wp_update_attachment_metadata($attachment_id,$attachment_metadata);
							$attachment_m_array[] = array("added_file" => $attachment_id);
						endif;
					}
					if (get_post_meta($question_id,'attachment_m')) {
						delete_post_meta($question_id,'attachment_m');
					}
					if (isset($attachment_m_array)) {
						add_post_meta($question_id,'attachment_m',$attachment_m_array);
					}
				}
			}
		}
		
		/* Featured image */
		
		if (isset($question_sort["featured_image"]["value"]) && $question_sort["featured_image"]["value"] == "featured_image") {
			$featured_image = '';
			
			if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
				$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/gif","image/tiff","image/ico");
				if (!in_array($_FILES['featured_image']['type'],$types)) :
					$errors->add('upload-error',esc_html__("Attachment Error, Please upload image only.","wpqa"));
					return $errors;
				endif;
				
				$featured_image = wp_handle_upload($_FILES['featured_image'],array('test_form'=>false),current_time('mysql'));
				
				if (isset($featured_image['error'])) :
					$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $featured_image['error']);
					return $errors;
				endif;
			endif;
			
			if ($featured_image) :
				$featured_image_data = array(
					'post_mime_type' => $featured_image['type'],
					'post_title'	 => preg_replace('/\.[^.]+$/','',basename($featured_image['file'])),
					'post_content'   => '',
					'post_status'	 => 'inherit',
					'post_author'    => ((!is_user_logged_in() && $ask_question_no_register == "on") || $question_add['anonymously_question']?0:$user_id),
				);
				$featured_image_id = wp_insert_attachment($featured_image_data,$featured_image['file'],$question_id);
				$featured_image_metadata = wp_generate_attachment_metadata($featured_image_id,$featured_image['file']);
				wp_update_attachment_metadata($featured_image_id, $featured_image_metadata);
				$set_post_thumbnail = set_post_thumbnail($question_id,$featured_image_id);
			endif;
		}
		
		/* Tags */
		
		if (empty($question_add['user_id']) && isset($question_add['question_tags']) && $question_add['question_tags']) :
					
			$tags = explode(',',trim(stripslashes($question_add['question_tags'])));
			$tags = array_map('strtolower',$tags);
			$tags = array_map('trim',$tags);
	
			if (sizeof($tags)>0) :
				wp_set_object_terms($question_id,$tags,'question_tags');
			endif;
			
		endif;
		
		if (!is_user_logged_in() && $ask_question_no_register == "on" && $user_id == 0) {
			$question_username = sanitize_text_field($question_add['username']);
			$question_email = sanitize_text_field($question_add['email']);
			update_post_meta($question_id,'question_username',$question_username);
			update_post_meta($question_id,'question_email',$question_email);
		}else {
			$pay_ask = wpqa_options("pay_ask");
			if ($pay_ask == "on") {
				$_allow_to_ask = (int)get_user_meta($user_id,$user_id."_allow_to_ask",true);
				if ($_allow_to_ask == "" || $_allow_to_ask < 0) {
					$_allow_to_ask = 0;
				}
				if ($_allow_to_ask > 0) {
					$_allow_to_ask--;
				}
				update_user_meta($user_id,$user_id."_allow_to_ask",$_allow_to_ask);
				
				$_coupon = get_user_meta($user_id,$user_id."_coupon",true);
				$_coupon_value = get_user_meta($user_id,$user_id."_coupon_value",true);
				if (isset($_coupon) && $_coupon != "") {
					update_post_meta($question_id,'_coupon',$_coupon);
					delete_user_meta($user_id,$user_id."_coupon");
				}
				if (isset($_coupon_value) && $_coupon_value != "") {
					update_post_meta($question_id,'_coupon_value',$_coupon_value);
					delete_user_meta($user_id,$user_id."_coupon_value");
				}
				
				$_paid_question = get_user_meta($user_id,'_paid_question',true);
				if (isset($_paid_question) && $_paid_question != "") {
					update_post_meta($question_id,'_paid_question',$_paid_question);
					delete_user_meta($user_id,'_paid_question');
				}
				
				$item_transaction = get_user_meta($user_id,'item_transaction',true);
				if (isset($item_transaction) && $item_transaction != "") {
					update_post_meta($question_id,'item_transaction',$item_transaction);
					delete_user_meta($user_id,'item_transaction');
				}
				
				$paypal_sandbox = get_user_meta($user_id,'paypal_sandbox',true);
				if (isset($paypal_sandbox) && $paypal_sandbox != "") {
					update_post_meta($question_id,'paypal_sandbox',$paypal_sandbox);
					delete_user_meta($user_id,'paypal_sandbox');
				}
				
			}
			
			$active_points = wpqa_options("active_points");
			
			if ($points && $active_points == "on" && $question_points_active == "on") {
				wpqa_add_points($user_id,$question_points,"-","question_point",$question_id);
				update_post_meta($question_id,"point_back","yes");
				update_post_meta($question_id,"what_point",$question_points);
			}
			
		}
		
		update_post_meta($question_id,"question_vote",0);
		update_post_meta($question_id,"post_from_front","from_front");
		
		do_action('wpqa_finished_add_question',$question_id,$question_add);
		
		/* Successful */
		return $question_id;
	}
}
/* Question */
function wpqa_question($atts, $content = null) {
	$a = shortcode_atts( array(
	    'type'  => '',
	    'popup' => ''
	), $atts );
	$out = '';
	$ask_question_no_register = wpqa_options("ask_question_no_register");
	$ask_question = wpqa_options("ask_question");
	$editor_question_details = wpqa_options("editor_question_details");
	$custom_permission = wpqa_options("custom_permission");
	$pay_ask = wpqa_options("pay_ask");
	$payment_group = wpqa_options("payment_group");
	$user_get_current_user_id = get_current_user_id();
	
	if (is_user_logged_in()) {
		$user_is_login = get_userdata($user_get_current_user_id);
		$user_login_group = key($user_is_login->caps);
		$roles = $user_is_login->allcaps;
	}
	
	if (($custom_permission == "on" && is_user_logged_in() && !is_super_admin($user_get_current_user_id) && empty($roles["ask_question"])) || ($custom_permission == "on" && !is_user_logged_in() && $ask_question != "on")) {
		$out .= '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have a permission to add a question","wpqa").(!is_user_logged_in()?", ".esc_html__("You must login to ask question","wpqa"):'').'.</p></div>';
		if (!is_user_logged_in()) {
			$out .= do_shortcode("[wpqa_login]");
		}
	}else if (!is_user_logged_in() && $ask_question_no_register != "on") {
		$out .= '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("You must login to ask question.","wpqa").'</p></div>'.do_shortcode("[wpqa_login]");
	}else {
		if (!is_user_logged_in() && $pay_ask == "on") {
			$out .= '<div class="alert-message error"><i class="icon-cancel"></i>'.esc_html__("You must login to ask question.","wpqa").'</p></div>'.do_shortcode("[wpqa_login]");
		}else {
			$points_user = (int)(is_user_logged_in()?get_user_meta($user_get_current_user_id,"points",true):0);
			$_allow_to_ask = (int)(isset($user_get_current_user_id) && $user_get_current_user_id != ""?get_user_meta($user_get_current_user_id,$user_get_current_user_id."_allow_to_ask",true):"");
			if ($user_get_current_user_id > 0 && isset($_POST["process"]) && $_POST["process"] == "ask") {
				/* Pay by points */
				if (isset($_POST["points"]) && $_POST["points"] > 0) {
					$points_price = (int)$_POST["points"];
					if ($points_price <= $points_user) {
						wpqa_add_points($user_get_current_user_id,$points_price,"-","ask_points");
						wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("You have just bought to ask question by points.","wpqa").'</p></div>','wpqa_session');
					}else {
						$buy_points_payment = wpqa_options("buy_points_payment");
						wpqa_session('<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you haven't enough points","wpqa").($buy_points_payment == "on"?', <a href="'.wpqa_buy_points_permalink().'">'.esc_html__("You can buy the points from here.","wpqa").'</a>':'.').'</p></div>','wpqa_session');
						wp_safe_redirect(esc_url(wpqa_add_question_permalink()));
						die();
					}
				}
				/* Number allow to ask question */
				if ($_allow_to_ask == "" || $_allow_to_ask < 0) {
					$_allow_to_ask = 0;
				}
				$_allow_to_ask++;
				update_user_meta($user_get_current_user_id,$user_get_current_user_id."_allow_to_ask",$_allow_to_ask);
				wp_safe_redirect(esc_url(wpqa_add_question_permalink()));
				die();
			}
			
			if (isset($_allow_to_ask) && (int)$_allow_to_ask < 1 && $pay_ask == "on" && $payment_group[$user_login_group] != $user_login_group) {
				$out .= wpqa_get_payment_coupons($user_get_current_user_id,(isset($a["popup"]) && $a["popup"] == "popup"?"popup":""));
			}else {
				$question_points_active = wpqa_options("question_points_active");
				$question_points = wpqa_options("question_points");
				$form_type = (isset($_POST["form_type"]) && $_POST["form_type"] != ""?esc_html($_POST["form_type"]):"");
				if ($question_points_active != "on" || ($points_user >= $question_points && $question_points_active == "on")) {
					if ($question_points_active == "on") {
						$out .= '<div class="alert-message"><i class="icon-lamp"></i><p>'.sprintf(esc_html__("Will lose %s points when add a new question.","wpqa"),$question_points).'</p></div>';
					}
					$out = wpqa_add_edit_question("add",(isset($a["popup"]) && $a["popup"] == "popup"?"popup":false),(isset($a["type"]) && $a["type"] == "user"?"user":false));
				}else {
					$out .= sprintf(esc_html__("Sorry do not have the minimum points Please do answer questions, even gaining points (The minimum points = %s).","wpqa"),$question_points);
				}
			}
		}
	}
	return $out;
}
/* Edit question attrs */
function wpqa_edit_question_attr() {
	$out = '';
	if (!is_user_logged_in()) {
		$out .= '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("You must login to edit question.","wpqa").'</p></div>'.do_shortcode("[wpqa_login]");
	}else {
		$edit_question = wpqa_options("question_edit");
		$user_id = get_current_user_id();
		if ($edit_question == "on" || is_super_admin($user_id)) {
			$get_post = (int)get_query_var(apply_filters('wpqa_edit_questions','edit_question'));
			$get_question = get_post($get_post);
			if (isset($get_post) && $get_post != 0 && $get_question && $get_question->post_type == "question") {
				if ($get_question->post_author != 0 || is_super_admin($user_id)) {
					if (($get_question->post_author == $user_id && $user_id != 0 && $get_question->post_status == "publish") || is_super_admin($user_id)) {
						$get_question_user_id = get_post_meta($get_post,"user_id",true);
						$out .= wpqa_add_edit_question("edit",false,($get_question_user_id != "" && $get_question_user_id > 0?"user":false));
					}else {
						$out .= '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry you can't edit this question.","wpqa").'</p></div>';
					}
				}else {
					$out .= '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry you can't edit this question.","wpqa").'</p></div>';
				}
			}else {
				$out .= '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry no question has you select or not found.","wpqa").'</p></div>';
			}
		}else {
			$out .= '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry, you do not have a permission to edit a question.","wpqa").'</p></div>';
		}
	}
	return $out;
}
/* Edit question */
function wpqa_edit_question() {
	if (isset($_POST["form_type"]) && $_POST["form_type"] == "edit_question") :
		$return = wpqa_process_edit_questions();
		if (is_wp_error($return)) :
   			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
   		else :
   			$question_approved = wpqa_options("question_approved");
			if ($question_approved == "on" || is_super_admin(get_current_user_id())) {
				wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Has been edited successfully.","wpqa").'</p></div>','wpqa_session');
				wp_redirect(get_permalink($return));
			}else {
				wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Has been Edited successfully, The question is under review.","wpqa").'</p></div>','wpqa_session');
				wp_redirect(esc_url(home_url('/')));
			}
			exit;
   		endif;
	endif;
}
add_filter('wpqa_edit_question','wpqa_edit_question');
/* Process edit questions */
function wpqa_process_edit_questions() {
	global $question_edit;
	set_time_limit(0);
	$errors = new WP_Error();
	$question_edit = array();
	$form_type = (isset($_POST["form_type"]) && $_POST["form_type"] != ""?$_POST["form_type"]:"");
	if ($form_type == "edit_question") {
		$fields = array(
			'title','comment','category','question_poll','question_image_poll','remember_answer','question_tags','video_type','video_id','video_description','sticky','ask','private_question','anonymously_question','featured_image'
		);
		
		$fields = apply_filters('wpqa_edit_question_fields',$fields);
		
		foreach ($fields as $field) :
			if (isset($_POST[$field])) $question_edit[$field] = $_POST[$field]; else $question_edit[$field] = '';
		endforeach;
		
		/* Validate Required Fields */

		if (!isset($_POST['wpqa_edit_question_nonce']) || !wp_verify_nonce($_POST['wpqa_edit_question_nonce'],'wpqa_edit_question_nonce')) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are an error, Please try again.","wpqa"));
		}

		$get_question = (int)get_query_var(apply_filters('wpqa_edit_questions','edit_question'));
		$get_question_user_id = get_post_meta($get_question,"user_id",true);
		$get_post_q = get_post($get_question);
		$user_id = get_current_user_id();
		if (isset($get_question) && $get_question != 0 && $get_post_q && $get_post_q->post_type == "question") {
			if (($get_post_q->post_author != $user_id || $get_post_q->post_status != "publish") && !is_super_admin($user_id)) {
				$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Sorry you can't edit this question.","wpqa"));
			}
		}else {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Sorry no question select or not found.","wpqa"));
		}
		
		if ($get_question_user_id != "" && $get_question_user_id > 0) {
			$question_sort = wpqa_options("ask_user_items");
			$comment_question = wpqa_options("content_ask_user");
			$editor_question_details = wpqa_options("editor_ask_user");
		}else {
			$question_sort = wpqa_options("ask_question_items");
			$comment_question = wpqa_options("comment_question");
			$editor_question_details = wpqa_options("editor_question_details");
		}
		
		if (empty($question_edit['title'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (title).","wpqa"));
		
		if (empty($get_question_user_id) && isset($question_sort["categories_question"]["value"]) && $question_sort["categories_question"]["value"] == "categories_question" && (empty($question_edit['category']) || $question_edit['category'] == '-1')) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (category).","wpqa"));
		
		if (isset($question_edit['question_poll']) && $question_edit['question_poll'] == "on") {
			if (isset($question_edit['ask']) && is_array($question_edit['ask'])) {
				foreach($question_edit['ask'] as $ask) {
					if (count($question_edit['ask']) < 2) {
						$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Please enter at least two values in poll.","wpqa"));
					}
					if (((isset($question_edit['question_image_poll']) && $question_edit['question_image_poll'] == "on" && $poll_image_title == "on" && $poll_image_title_required == "on") || (!isset($question_edit['question_image_poll']) && $poll_image_title != "on" && $poll_image_title_required != "on")) && empty($ask['title'])) {
						$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Please enter values in poll.","wpqa"));
					}
				}
			}else {
				$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Please enter at least two values in poll.","wpqa"));
			}
		}
		if (isset($question_sort["comment_question"]["value"]) && $question_sort["comment_question"]["value"] == "comment_question" && $comment_question == "on" && empty($question_edit['comment'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (content).","wpqa"));
		if (isset($question_sort["video_desc_active"]["value"]) && $question_sort["video_desc_active"]["value"] == "video_desc_active" && $question_edit['video_description'] == "on" && empty($question_edit['video_id'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (Video ID).","wpqa"));
		
		do_action('wpqa_edit_question_errors',$errors,$question_edit);
		
		if (sizeof($errors->errors)>0) return $errors;
		
		$question_id = $get_question;
		
		$question_approved = wpqa_options("question_approved");
		
		/* Edit question */
		
		$post_name = array();
		$change_question_url = wpqa_options("change_question_url");
		if ($change_question_url == "on") {
			$post_name = array('post_name' => wpqa_kses_stip($question_edit['title']));
		}
		
		$data = array(
			'ID'           => (int)sanitize_text_field($question_id),
			'post_content' => ($editor_question_details == "on"?wpqa_kses_stip($question_edit['comment']):wpqa_kses_stip_wpautop($question_edit['comment'])),
			'post_title'   => wpqa_kses_stip($question_edit['title']),
			'post_status'  => ($question_approved == "on" || is_super_admin($user_id)?"publish":"draft"),
			'post_author'  => ($question_edit['anonymously_question']?0:$get_post_q->post_author),
		);
		
		wp_update_post(array_merge($post_name,$data));
		
		if (empty($get_question_user_id) && isset($question_sort["categories_question"]["value"]) && $question_sort["categories_question"]["value"] == "categories_question" && isset($question_edit['category']) && $question_edit['category']) {
			if (is_array($question_edit['category'])) {
				$cat_ids = array_map( 'intval', $question_edit['category'] );
				$cat_ids = array_unique( $cat_ids );
			}else {
				$cat_ids = array();
				$cat_ids[] = get_term_by('id',(is_array($question_edit['category'])?end($question_edit['category']):$question_edit['category']),'question-category')->slug;
			}
			if (sizeof($cat_ids)>0) :
				wp_set_object_terms($question_id,$cat_ids,'question-category');
			endif;
		}
		
		if ($question_edit['question_poll'] && $question_edit['question_poll'] != "")  {
			$ask = get_post_meta($question_id,'ask',true);
			update_post_meta($question_id,'question_poll',esc_html($question_edit['question_poll']));
			if ($question_edit['question_image_poll'] && $question_edit['question_image_poll'] != "") {
				update_post_meta($question_id,'question_image_poll',esc_html($question_edit['question_image_poll']));
			}else {
				delete_post_meta($question_id,'question_image_poll');
			}

			require_once(ABSPATH . 'wp-admin/includes/image.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');

			$files = $_FILES['ask'];
			if (isset($files) && $files) {
				foreach ($files['name'] as $key => $value) {
					if ($files['name'][$key]) {
						$file = array(
							'name'	 => $files['name'][$key]["image"],
							'type'	 => $files['type'][$key]["image"],
							'tmp_name' => $files['tmp_name'][$key]["image"],
							'error'	=> $files['error'][$key]["image"],
							'size'	 => $files['size'][$key]["image"]
						);
						if ($files['error'][$key]["image"] != 0) {
							unset($files['name'][$key]);
							unset($files['type'][$key]);
							unset($files['tmp_name'][$key]);
							unset($files['error'][$key]);
							unset($files['size'][$key]);
						}
					}
				}
			}
			
			if (isset($files) && $files) {
				foreach ($files['name'] as $key => $value) {
					if ($files['name'][$key]) {
						$file = array(
							'name'	 => $files['name'][$key]["image"],
							'type'	 => $files['type'][$key]["image"],
							'tmp_name' => $files['tmp_name'][$key]["image"],
							'error'	=> $files['error'][$key]["image"],
							'size'	 => $files['size'][$key]["image"]
						);
						$attachment = wp_handle_upload($file,array('test_form'=>false),current_time('mysql'));
						if (!isset($attachment['error']) && $attachment) :
							$attachment_data = array(
								'post_mime_type' => $attachment['type'],
								'post_title'	 => preg_replace('/\.[^.]+$/','',basename($attachment['file'])),
								'post_content'   => '',
								'post_status'	 => 'inherit',
								'post_author'    => (!is_user_logged_in() && $ask_question_no_register == "on"?0:$user_id),
							);
							$attachment_id = wp_insert_attachment($attachment_data,$attachment['file'],$question_id);
							$attachment_metadata = wp_generate_attachment_metadata($attachment_id,$attachment['file']);
							wp_update_attachment_metadata($attachment_id,$attachment_metadata);
							$question_edit['ask'][$key]['image'] = array('id' => $attachment_id,'url' => wp_get_attachment_url($attachment_id));
						endif;
					}
				}
			}
			
			if ($question_edit['question_image_poll'] && $question_edit['question_image_poll'] != "") {
				foreach ($question_edit['ask'] as $key => $value) {
					if (!isset($value['image'])) {
						$question_edit['ask'][$key]['image'] = $ask[$key]['image'];
					}
				}
			}
			
			if (isset($question_edit['ask']) && $question_edit['ask'] != "") {
				update_post_meta($question_id,'ask',$question_edit['ask']);
			}else {
				delete_post_meta($question_id,'ask');
			}
		}else {
			update_post_meta($question_id,'question_poll',2);
		}
		
		if ($question_edit['remember_answer'] && $question_edit['remember_answer'] != "") {
			update_post_meta($question_id,'remember_answer',esc_html($question_edit['remember_answer']));
		}else {
			delete_post_meta($question_id,'remember_answer');
		}
		
		if ($question_edit['private_question'] && $question_edit['private_question'] != "") {
			update_post_meta($question_id,'private_question',esc_html($question_edit['private_question']));
			$anonymously_user = get_post_meta($question_id,'anonymously_user',true);
			update_post_meta($question_id,'private_question_author',($anonymously_user > 0?$anonymously_user:$get_post_q->post_author));
		}else {
			delete_post_meta($question_id,'private_question');
			delete_post_meta($question_id,'private_question_author');
		}
		
		if (isset($question_sort["video_desc_active"]["value"]) && $question_sort["video_desc_active"]["value"] == "video_desc_active") {
			if ($question_edit['video_description'] && $question_edit['video_description'] != "") {
				update_post_meta($question_id,'video_description',esc_html($question_edit['video_description']));
			}else {
				delete_post_meta($question_id,'video_description');
			}
			
			if ($question_edit['video_type']) {
				update_post_meta($question_id,'video_type',esc_html($question_edit['video_type']));
			}
				
			if ($question_edit['video_id']) {
				update_post_meta($question_id,'video_id',esc_html($question_edit['video_id']));
			}
		}
		
		$sticky_questions = get_option('sticky_questions');
		$sticky_posts = get_option('sticky_posts');
		if (isset($question_edit['sticky']) && $question_edit['sticky'] == "sticky") {
			update_post_meta($question_id,'sticky',1);
			if (is_array($sticky_questions)) {
				if (!in_array($question_id,$sticky_questions)) {
					$array_merge = array_merge($sticky_questions,array($question_id));
					update_option("sticky_questions",$array_merge);
				}
			}else {
				update_option("sticky_questions",array($question_id));
			}
			if (is_array($sticky_posts)) {
				if (!in_array($question_id,$sticky_posts)) {
					$array_merge = array_merge($sticky_posts,array($question_id));
					update_option("sticky_posts",$array_merge);
				}
			}else {
				update_option("sticky_posts",array($question_id));
			}
		}else {
			if (is_array($sticky_questions) && in_array($question_id,$sticky_questions)) {
				$sticky_questions = wpqa_remove_item_by_value($sticky_questions,$question_id);
				update_option('sticky_questions',$sticky_questions);
			}
			if (is_array($sticky_posts) && in_array($question_id,$sticky_posts)) {
				$sticky_posts = wpqa_remove_item_by_value($sticky_posts,$question_id);
				update_option('sticky_posts',$sticky_posts);
			}
			delete_post_meta($question_id,'sticky');
		}
		
		/* Featured image */
		
		if (isset($question_sort["featured_image"]["value"]) && $question_sort["featured_image"]["value"] == "featured_image") {
			$featured_image = '';

			require_once(ABSPATH . 'wp-admin/includes/image.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			
			if(isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
				$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/gif","image/tiff","image/ico");
				if (!in_array($_FILES['featured_image']['type'],$types)) :
					$errors->add('upload-error',esc_html__("Attachment Error, Please upload image only.","wpqa"));
					return $errors;
				endif;
				
				$featured_image = wp_handle_upload($_FILES['featured_image'],array('test_form'=>false),current_time('mysql'));
				
				if (isset($featured_image['error'])) :
					$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $featured_image['error']);
					return $errors;
				endif;
				
			endif;
			if ($featured_image) :
				$ask_question_no_register = wpqa_options("ask_question_no_register");
				$featured_image_data = array(
					'post_mime_type' => $featured_image['type'],
					'post_title'     => preg_replace('/\.[^.]+$/','',basename($featured_image['file'])),
					'post_content'   => '',
					'post_status'    => 'inherit',
					'post_author'    => (!is_user_logged_in() && $ask_question_no_register == "on"?0:$user_id)
				);
				$featured_image_id = wp_insert_attachment($featured_image_data,$featured_image['file'],$question_id);
				$featured_image_metadata = wp_generate_attachment_metadata($featured_image_id,$featured_image['file']);
				wp_update_attachment_metadata($featured_image_id, $featured_image_metadata);
				set_post_thumbnail($question_id,$featured_image_id);
			endif;
		}
		
		/* Tags */
		
		if (empty($get_question_user_id) && isset($question_edit['question_tags']) && $question_edit['question_tags']) :
					
			$tags = explode(',',trim(stripslashes($question_edit['question_tags'])));
			$tags = array_map('strtolower',$tags);
			$tags = array_map('trim',$tags);
	
			if (sizeof($tags) > 0) :
				wp_set_object_terms($question_id,$tags,'question_tags');
			endif;
			
		endif;
		
		do_action('wpqa_finished_edit_question',$question_id,$question_edit);
		
		/* Successful */
		return $question_id;
	}
}
/* Add & Edit question */
function wpqa_add_edit_question($type,$popup = false,$user = false) {
	global $question_add,$question_edit;
	$ask_question_no_register = wpqa_options("ask_question_no_register");
	if ($user == "user") {
		$question_sort = wpqa_options("ask_user_items");
		$comment_question = wpqa_options("content_ask_user");
		$editor_question_details = wpqa_options("editor_ask_user");
	}else {
		$question_sort = wpqa_options("ask_question_items");
		$comment_question = wpqa_options("comment_question");
		$editor_question_details = wpqa_options("editor_question_details");
	}
	
	$rand = rand(1,1000);
	
	if ($type == "edit") {
		$get_question = (int)get_query_var(apply_filters('wpqa_edit_questions','edit_question'));
		$get_post_q = get_post($get_question);
		$q_tag = "";
		if ($terms = wp_get_object_terms( $get_question, 'question_tags' )) :
			$terms_array = array();
			foreach ($terms as $term) :
				$terms_array[] = $term->name;
				$q_tag = implode(' , ', $terms_array);
			endforeach;
		endif;
		$question_category = wp_get_post_terms($get_question,'question-category',array("fields" => "ids"));
	}
	
	if ($type == "add") {
		$the_captcha = wpqa_options("the_captcha");
		if (isset($question_sort) && is_array($question_sort)) {
			$question_sort = array_merge($question_sort,array("the_captcha" => array("value" => ($the_captcha == "on"?"the_captcha":0))));
		}
	}
	
	$out = '';
	if ($user == "user") {
		if (wpqa_is_user_profile()) {
			$get_user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
			$display_name = get_the_author_meta('display_name',$get_user_id);
		}
		if (!isset($get_user_id)) {
			$get_user_id = wpqa_add_question_user();
			if ($get_user_id > 0) {
				$display_name = get_the_author_meta('display_name',$get_user_id);
			}
		}
		if (isset($display_name) && $display_name != "") {
			$out .= '<div class="ask-user-question">';
				$out .= wpqa_get_user_avatar(array("user_id" => $get_user_id,"size" => 42)).
				sprintf(esc_html__("Ask %s a question","wpqa"),$display_name).
			'</div>';
		}
	}
	$out .= '<form class="form-post wpqa_form" method="post" enctype="multipart/form-data">'.(isset($_POST["form_type"]) && $_POST["form_type"] == $type."_question"?apply_filters('wpqa_'.($user == "user"?"user":$type).'_question',($user == "user"?"user":$type)):"").'
		<div class="form-inputs clearfix">';
			if ($type == "add" && !is_user_logged_in() && $ask_question_no_register == "on") {
				$out .= '<p>
					<label for="question-username-'.$rand.'">'.esc_html__("Username","wpqa").'<span class="required">*</span></label>
					<input name="username" id="question-username-'.$rand.'" class="the-username" type="text" value="'.(isset($question_add['username'])?esc_attr($question_add['username']):'').'">
					<i class="icon-user"></i>
					<span class="form-description">'.esc_html__("Please type your username.","wpqa").'</span>
				</p>
				
				<p>
					<label for="question-email-'.$rand.'">'.esc_html__("E-Mail","wpqa").'<span class="required">*</span></label>
					<input name="email" id="question-email-'.$rand.'" class="the-email" type="text" value="'.(isset($question_add['email'])?esc_attr($question_add['email']):'').'">
					<i class="icon-mail"></i>
					<span class="form-description">'.esc_html__("Please type your E-Mail.","wpqa").'</span>
				</p>';
			}
			
			if (is_user_logged_in()) {
				$user_id = get_current_user_id();
				$display_name = get_the_author_meta('display_name',$user_id);
			}
			
			if ($type == "edit") {
				$question_poll          = get_post_meta($get_question,"question_poll",true);
				$question_image_poll    = get_post_meta($get_question,"question_image_poll",true);
				$q_anonymously_question = get_post_meta($get_question,"anonymously_question",true);
				$q_remember_answer      = get_post_meta($get_question,"remember_answer",true);
				$q_private_question     = get_post_meta($get_question,"private_question",true);
				$question_user_id       = get_post_meta($get_question,"user_id",true);
				if ($user != "user") {
					$q_sticky            = is_sticky($get_question);
					$q_video_description = get_post_meta($get_question,"video_description",true);
					$q_video_type        = get_post_meta($get_question,"video_type",true);
					$q_video_id          = get_post_meta($get_question,"video_id",true);
				}
			}
			
			$out .= '<p>
				<label for="question-title-'.$rand.'">'.esc_html__("Question Title","wpqa").'<span class="required">*</span></label>
				<input name="title" id="question-title-'.$rand.'" class="the-title" type="text" value="'.($type == "add" && isset($question_add['title'])?wpqa_kses_stip($question_add['title']):($type == "edit"?(isset($question_edit['title'])?wpqa_kses_stip($question_edit['title']):wpqa_kses_stip($get_post_q->post_title)):"")).'">
				<i class="icon-chat"></i>
				<span class="form-description">'.esc_html__("Please choose an appropriate title for the question so it can be answered easier.","wpqa").'</span>
			</p>';
			
			if (isset($question_sort) && is_array($question_sort)) {
				foreach ($question_sort as $sort_key => $sort_value) {
					if ($sort_key == "categories_question" && $user != "user" && isset($question_sort["categories_question"]["value"]) && $question_sort["categories_question"]["value"] == "categories_question") {
						$category_single_multi = wpqa_options("category_single_multi");
						$category_area = '<div class="wpqa_category">
							<label for="question-category-'.$rand.'">'.esc_html__("Category","wpqa").'<span class="required">*</span></label>
							'.apply_filters('wpqa_select_categories',wpqa_select_categories($rand,($type == "add" && isset($question_add['category'])?$question_add['category']:($type == "edit"?(isset($question_edit['category'])?$question_edit['category']:$question_category):"")),null,($type == 'edit'?$get_question:''),'question-category'),$type,$question_add,$question_edit,(isset($get_question)?$get_question:0)).
							($category_single_multi != 'multi'?'<i class="icon-folder"></i>':'').'
							<span class="form-description">'.esc_html__("Please choose the appropriate section so question can be searched easier.","wpqa").'</span>
						</div>'.
						apply_filters('wpqa_add_edit_question_after_category',false,$type,$question_add,$question_edit,(isset($get_question)?$get_question:0));
						
						$out .= apply_filters('wpqa_add_edit_question_category',$category_area,$type,$question_add,$question_edit,(isset($get_question)?$get_question:0));
					}else if ($sort_key == "tags_question" && $user != "user" && isset($question_sort["tags_question"]["value"]) && $question_sort["tags_question"]["value"] == "tags_question") {
						$out .= '<p class="wpqa_tag">
							<label for="question_tags-'.$rand.'">'.esc_html__("Tags","wpqa").'</label>
							<input type="text" class="input question_tags" name="question_tags" id="question_tags-'.$rand.'" value="'.($type == "add" && isset($question_add['question_tags'])?$question_add['question_tags']:($type == "edit"?(isset($question_edit['question_tags'])?$question_edit['question_tags']:$q_tag):"")).'" data-seperator=",">
							<span class="form-description">'.esc_html__("Please choose suitable Keywords Ex: ","wpqa").'<span class="color">'.esc_html__("question, poll","wpqa").'</span>.</span>
						</p>';
					}else if ($sort_key == "poll_question" && $user != "user" && isset($question_sort["poll_question"]["value"]) && $question_sort["poll_question"]["value"] == "poll_question") {
						if ($type == "add" && isset($question_add['question_poll']) && ($question_add['question_poll'] == "on" || $question_add['question_poll'] == 1)) {
							$active_poll = true;
						}else if ($type == "edit" && ((isset($question_edit['question_poll']) && ($question_edit['question_poll'] == "on" || $question_edit['question_poll'] == 1) || (!isset($question_edit['question_poll']) && ($question_poll == "on" || $question_poll == 1))))) {
							$active_poll = true;
						}
						$out .= '<p class="wpqa_checkbox_p">
							<label for="question_poll-'.$rand.'">
								<span class="wpqa_checkbox"><input type="checkbox" id="question_poll-'.$rand.'" class="question_poll" value="on" name="question_poll"'.(isset($active_poll)?" checked='checked'":"").'></span>
								<span class="wpqa_checkbox_span">'.esc_html__("This question is a poll?","wpqa")." ".esc_html__("If you want to be doing a poll click here.","wpqa").'</span>
							</label>
						</p>
						
						<div class="clearfix"></div>
						<div class="poll_options'.(isset($active_poll)?"":" wpqa_hide").'">';
							$poll_image = wpqa_options("poll_image");
							$poll_image_title = wpqa_options("poll_image_title");
							if ($poll_image == "on") {
								$out .= '<p class="wpqa_checkbox_p">
									<label for="question_image_poll-'.$rand.'">
										<span class="wpqa_checkbox"><input type="checkbox" id="question_image_poll-'.$rand.'" class="question_image_poll" value="on" name="question_image_poll"'.($type == "add" && isset($question_add['question_image_poll']) && ($question_add['question_image_poll'] == "on" || $question_add['question_image_poll'] == 1)?" checked='checked'":($type == "edit" && ((isset($question_edit['question_image_poll']) && ($question_edit['question_image_poll'] == "on" || $question_edit['question_image_poll'] == 1) || (!isset($question_edit['question_image_poll']) && ($question_image_poll == "on" || $question_image_poll == 1))))?" checked='checked'":"")).'></span>
										<span class="wpqa_checkbox_span">'.esc_html__("Image poll?","wpqa").'</span>
									</label>
								</p>
								<div class="clearfix"></div>';
							}
							$out .= '<ul class="question_items question_polls_item">';
								if ($type == "edit") {
									if (isset($question_edit['ask']) && is_array($question_edit['ask'])) {
										$q_ask = $question_edit['ask'];
									}else {
										$q_ask = get_post_meta($get_question,"ask",true);
									}
								}
								if ($type == "add" && isset($question_add['ask']) && is_array($question_add['ask'])) {
									$q_ask = $question_add['ask'];
								}

								if (isset($q_ask) && is_array($q_ask)) {
									foreach($q_ask as $ask) {
										if ($poll_image == "on" && (($type == "add" && isset($question_add['question_image_poll']) && ($question_add['question_image_poll'] == "on" || $question_add['question_image_poll'] == 1)) || ($type == "edit" && ((isset($question_edit['question_image_poll']) && ($question_edit['question_image_poll'] == "on" || $question_edit['question_image_poll'] == 1) || (!isset($question_edit['question_image_poll']) && ($question_image_poll == "on" || $question_image_poll == 1))))))) {
											$active_the_image = true;
										}
										$out .= '<li id="poll_li_'.(int)$ask['id'].'">';
											if (isset($active_the_image) && $active_the_image == true) {
												$out .= '<div class="attach-li">
													<div class="fileinputs">
														<input type="file" class="file" name="ask['.(int)$ask['id'].'][image]" id="ask['.(int)$ask['id'].'][image]">
														<i class="icon-camera"></i>
														<div class="fakefile">
															<button type="button">'.esc_html__("Select file","wpqa").'</button>
															<span><i class="icon-arrow-up"></i>'.esc_html__("Browse","wpqa").'</span>
														</div>
													</div>
												</div>';
											}
											$out .= '<div class="poll-li">';
												if (!isset($active_the_image) || $poll_image != 'on' || ($poll_image == 'on' && $poll_image_title == 'on')) {
													$out .= '<p>
														<input class="ask" name="ask['.(int)$ask['id'].'][title]" value="'.(isset($ask['title']) && $ask['title'] != ""?wpqa_kses_stip($ask['title']):"").'" type="text">
														<i class="icon-comment"></i>
													</p>';
												}
												$out .= '<input name="ask['.(int)$ask['id'].'][id]" value="'.(int)$ask['id'].'" type="hidden">
												<div class="del-item-li"><i class="icon-cancel"></i></div>
												<div class="move-poll-li"><i class="icon-menu"></i></div>
											</div>
										</li>';
									}
								}else {
									$out .= '<li id="poll_li_1">';
										if (isset($active_the_image) && $active_the_image == true) {
											$out .= '<div class="poll_image_div attach-li">
												<div class="fileinputs">
													<input type="file" class="file" name="ask[1][image]" id="ask[1][image]">
													<i class="icon-camera"></i>
													<div class="fakefile">
														<button type="button">'.esc_html__("Select file","wpqa").'</button>
														<span><i class="icon-arrow-up"></i>'.esc_html__("Browse","wpqa").'</span>
													</div>
												</div>
											</div>';
										}
										$out .= '<div class="poll-li">
											<p class="poll_title_p">
												<input class="ask" name="ask[1][title]" value="" type="text">
												<i class="icon-comment"></i>
											</p>
											<input name="ask[1][id]" value="1" type="hidden">
											<div class="del-item-li"><i class="icon-cancel"></i></div>
											<div class="move-poll-li"><i class="icon-menu"></i></div>
										</div>
									</li>';
								}
							$out .= '</ul>
							<button type="button" class="button-default-3 add_poll_button_js">'.esc_html__("Add More Answers","wpqa").'</button>
							<div class="clearfix"></div>
						</div>';
					}else if ($sort_key == "attachment_question" && $user != "user" && isset($question_sort["attachment_question"]["value"]) && $question_sort["attachment_question"]["value"] == "attachment_question") {
						if ($type == "edit") {
							$added_file = get_post_meta($get_question,"added_file",true);
							if ($added_file != "") {
								$out .= '<ul class="wpqa-delete-attachment"><li><a href="'.wp_get_attachment_url($added_file).'"><i class="icon-attach"></i>'.esc_html__('Attachment','wpqa').'</a><a class="delete-this-attachment single-attachment" href="'.$added_file.'"><i class="icon-trash"></i>'.esc_html__('Delete','wpqa').'</a><div class="loader_2 loader_4"></div></li></ul>';
							}
							$attachment_m = get_post_meta($get_question,"attachment_m",true);
							if (isset($attachment_m) && is_array($attachment_m) && !empty($attachment_m)) {
								$out .= '<ul class="wpqa-delete-attachment">';
									foreach ($attachment_m as $key => $value) {
										$out .= '<li><a href="'.wp_get_attachment_url($value["added_file"]).'"><i class="icon-attach"></i>'.esc_html__('Attachment','wpqa').'</a><a class="delete-this-attachment" data-id="'.$get_question.'" href="'.$value["added_file"].'"><i class="icon-trash"></i>'.esc_html__('Delete','wpqa').'</a><div class="loader_2 loader_4"></div></li>';
									}
								$out .= '</ul>';
							}
						}
						if ($type == "add") {
							$out .= '<div class="question-multiple-upload question-upload-attachment">
								<label>'.esc_html__("Attachment","wpqa").'</label>
								<div class="clearfix"></div>
								<ul class="question_items question_upload_item"></ul>
								<button type="button" class="button-default-3 add_upload_button_js">'.esc_html__("Add Field","wpqa").'</button>
								<div class="clearfix"></div>
							</div>';
						}
					}else if ($sort_key == "featured_image" && $user != "user" && isset($question_sort["featured_image"]["value"]) && $question_sort["featured_image"]["value"] == "featured_image") {
						if ($type == "edit") {
							$_thumbnail_id = get_post_meta($get_question,"_thumbnail_id",true);
							if ($_thumbnail_id != "") {
								$out .= '<div class="clearfix"></div>
								<div class="wpqa-delete-image">
									<span class="wpqa-delete-image-span">'.wpqa_get_aq_resize_img(250,250,"",$_thumbnail_id,"no","").'</span>
									<div class="clearfix"></div>
									<div class="button-default wpqa-remove-image" data-name="_thumbnail_id" data-type="post_meta" data-id="'.$get_question.'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'">'.esc_html__("Delete","wpqa").'</div>
									<div class="loader_2 loader_4"></div>
								</div>';
							}
						}
						$out .= '<div class="question-multiple-upload question-upload-featured">
							<label for="featured_image-'.$rand.'">'.esc_html__("Featured image","wpqa").'</label>
							<div class="clearfix"></div>
							<div class="fileinputs">
								<input type="file" class="file" name="featured_image" id="featured_image-'.$rand.'">
								<i class="icon-camera"></i>
								<div class="fakefile">
									<button type="button">'.esc_html__("Select file","wpqa").'</button>
									<span><i class="icon-arrow-up"></i>'.esc_html__("Browse","wpqa").'</span>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>';
					}else if ($sort_key == "comment_question" && isset($question_sort["comment_question"]["value"]) && $question_sort["comment_question"]["value"] == "comment_question") {
						$out .= '<div class="wpqa_textarea'.($editor_question_details == "on"?"":" wpqa_textarea_p").'">
							<label for="question-details-'.$type.'-'.$rand.'">'.esc_html__("Details","wpqa").($comment_question == "on"?'<span class="required">*</span>':'').'</label>';
							if ($editor_question_details == "on") {
								$settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
								$settings = apply_filters('wpqa_question_editor_setting',$settings);
								ob_start();
								wp_editor(($type == "add" && isset($question_add['comment'])?wpqa_kses_stip($question_add['comment'],"yes"):($type == "edit"?(isset($question_edit['comment'])?wpqa_kses_stip($question_edit['comment'],"yes"):wpqa_kses_stip($get_post_q->post_content,"yes")):"")),"question-details-".$type.'-'.$rand,$settings);
								$editor_contents = ob_get_clean();
								$out .= '<div class="the-details the-textarea">'.$editor_contents.'</div>';
							}else {
								$out .= '<textarea name="comment" id="question-details-'.$type.'-'.$rand.'" class="the-textarea" aria-required="true" cols="58" rows="8">'.($type == "add" && isset($question_add['comment'])?wpqa_kses_stip($question_add['comment'],"yes"):($type == "edit"?(isset($question_edit['comment'])?wpqa_kses_stip($question_edit['comment'],"yes"):wpqa_kses_stip($get_post_q->post_content,"yes")):"")).'</textarea>
								<i class="icon-pencil"></i>';
							}
							$out .= '<span class="form-description">'.esc_html__("Type the description thoroughly and in details.","wpqa").'</span>
						</div>';
					}else if ($sort_key == "video_desc_active" && $user != "user" && isset($question_sort["video_desc_active"]["value"]) && $question_sort["video_desc_active"]["value"] == "video_desc_active") {
						$out .= '<p class="wpqa_checkbox_p">
							<label for="video_description-'.$rand.'">
								<span class="wpqa_checkbox"><input type="checkbox" id="video_description-'.$rand.'" class="video_description_input" name="video_description" value="on"'.($type == "add" && isset($question_add['video_description']) && $question_add['video_description'] == "on"?" checked='checked'":($type == "edit" && ((isset($question_edit['video_description']) && $question_edit['video_description'] == "on") || (!isset($question_edit['video_description']) && $q_video_description == "on"))?" checked='checked'":"")).'></span>
								<span class="wpqa_checkbox_span">'.esc_html__("Add a video to describe the problem better.","wpqa").'</span>
							</label>
						</p>
						
						<div class="video_description wpqa_hide"'.($type == "add" && isset($question_add['video_description']) && $question_add['video_description'] == "on"?" style='display:block;'":($type == "edit" && ((isset($question_edit['video_description']) && $question_edit['video_description'] == "on") || $q_video_description == "on")?" style='display:block;'":"")).'>
							<p>
								<label for="video_type-'.$rand.'">'.esc_html__("Video type","wpqa").'</label>
								<span class="styled-select">
									<select id="video_type-'.$rand.'" name="video_type">
										<option value="youtube"'.($type == "add" && isset($question_add['video_type']) && $question_add['video_type'] == "youtube"?' selected="selected"':($type == "edit"?((isset($question_edit['video_type']) && $question_edit['video_type'] == "youtube") || (isset($q_video_type) && $q_video_type == "youtube")?' selected="selected"':''):'')).'>Youtube</option>
										<option value="vimeo"'.($type == "add" && isset($question_add['video_type']) && $question_add['video_type'] == "vimeo"?' selected="selected"':($type == "edit"?((isset($question_edit['video_type']) && $question_edit['video_type'] == "vimeo") || (isset($q_video_type) && $q_video_type == "vimeo")?' selected="selected"':''):'')).'>Vimeo</option>
										<option value="daily"'.($type == "add" && isset($question_add['video_type']) && $question_add['video_type'] == "daily"?' selected="selected"':($type == "edit"?((isset($question_edit['video_type']) && $question_edit['video_type'] == "daily") || (isset($q_video_type) && $q_video_type == "daily")?' selected="selected"':''):'')).'>Dialymotion</option>
										<option value="facebook"'.($type == "add" && isset($question_add['video_type']) && $question_add['video_type'] == "facebook"?' selected="selected"':($type == "edit"?((isset($question_edit['video_type']) && $question_edit['video_type'] == "facebook") || (isset($q_video_type) && $q_video_type == "facebook")?' selected="selected"':''):'')).'>Facebook</option>
									</select>
								</span>
								<i class="icon-video"></i>
								<span class="form-description">'.esc_html__("Choose from here the video type.","wpqa").'</span>
							</p>
							
							<p>
								<label for="video_id-'.$rand.'">'.esc_html__("Video ID","wpqa").'</label>
								<input name="video_id" id="video_id-'.$rand.'" class="video_id" type="text" value="'.esc_html(($type == "add" && isset($question_add['video_id'])?$question_add['video_id']:($type == "edit"?(isset($question_edit['video_id'])?$question_edit['video_id']:$q_video_id):""))).'">
								<i class="icon-play"></i>
								<span class="form-description">'.esc_html__("Put here the video id : https://www.youtube.com/watch?v=sdUUx5FdySs Ex: 'sdUUx5FdySs'.","wpqa").'</span>
							</p>
							'.apply_filters('wpqa_add_edit_question_in_video',false,$type,$question_add,$question_edit,(isset($get_question)?$get_question:0)).'
						</div>
						'.apply_filters('wpqa_add_edit_question_after_video',false,$type,$question_add,$question_edit,(isset($get_question)?$get_question:0));
					}else if ($sort_key == "remember_answer" && isset($question_sort["remember_answer"]["value"]) && $question_sort["remember_answer"]["value"] == "remember_answer") {
						$out .= apply_filters('wpqa_add_edit_question_before_remember_answer',false,$type,$question_add,$question_edit,(isset($get_question)?$get_question:0)).'
						<p class="wpqa_checkbox_p">
							<label for="remember_answer-'.$rand.'">
								<span class="wpqa_checkbox"><input type="checkbox" id="remember_answer-'.$rand.'" class="remember_answer" name="remember_answer" value="on"'.($type == "add" && isset($question_add['remember_answer']) && $question_add['remember_answer'] == "on"?" checked='checked'":($type == "edit" && ((isset($question_edit['remember_answer']) && $question_edit['remember_answer'] == "on") || (!isset($question_edit['remember_answer']) && $q_remember_answer == "on"))?" checked='checked'":"")).'></span>
								<span class="wpqa_checkbox_span">'.esc_html__("Get notification by email when someone answers this question.","wpqa").'</span>
							</label>
						</p>';
					}else if (is_user_logged_in() && $sort_key == "private_question" && isset($question_sort["private_question"]["value"]) && $question_sort["private_question"]["value"] == "private_question") {
						$out .= '<p class="wpqa_checkbox_p">
							<label for="private_question-'.$rand.'">
								<span class="wpqa_checkbox"><input type="checkbox" id="private_question-'.$rand.'" class="private_question" name="private_question" value="on"'.($type == "add" && isset($question_add['private_question']) && $question_add['private_question'] == "on"?" checked='checked'":($type == "edit" && ((isset($question_edit['private_question']) && $question_edit['private_question'] == "on") || (!isset($question_edit['private_question']) && $q_private_question == "on"))?" checked='checked'":"")).'></span>
								<span class="wpqa_checkbox_span">'.esc_html__("This question is a private question?","wpqa").'</span>
							</label>
						</p>';
					}else if ($type == "add" && $sort_key == "anonymously_question" && isset($question_sort["anonymously_question"]["value"]) && $question_sort["anonymously_question"]["value"] == "anonymously_question") {
						$out .= '<p class="wpqa_checkbox_p ask_anonymously_p">
							<label for="anonymously_question-'.$rand.'">
								<span class="wpqa_checkbox"><input type="checkbox" id="anonymously_question-'.$rand.'" class="ask_anonymously" name="anonymously_question" value="on"'.($type == "add" && isset($question_add['anonymously_question']) && $question_add['anonymously_question'] == "on"?" checked='checked'":($type == "edit" && ((isset($question_edit['anonymously_question']) && $question_edit['anonymously_question'] == "on") || (!isset($question_edit['anonymously_question']) && $q_anonymously_question == "on"))?" checked='checked'":"")).'></span>
								<span class="wpqa_checkbox_span">'.esc_html__("Ask Anonymously","wpqa").'</span>';
								if (is_user_logged_in()) {
									$out .= '<span class="anonymously_span ask_named">'.wpqa_get_user_avatar(array("user_id" => (isset($user_id) && $user_id > 0?$user_id:0),"size" => 25,"user_name" => (isset($user_id) && $user_id > 0?$display_name:""))).'<span>'.$display_name.' '.esc_html__("asks","wpqa").'</span>
									</span>
									<span class="anonymously_span ask_none">
										<img alt="'.esc_attr__("Anonymous","wpqa").'" src="'.plugin_dir_url(dirname(__FILE__)).'images/avatar.png">
										<span>'.esc_html__("Anonymous asks","wpqa").'</span>
									</span>';
								}
							$out .= '</label>
						</p>';
					}else if ($sort_key == "the_captcha" && isset($question_sort["the_captcha"]["value"]) && $question_sort["the_captcha"]["value"] == "the_captcha") {
						$out .= wpqa_add_captcha($the_captcha,wpqa_options("captcha_style"),wpqa_options("captcha_question"),wpqa_options("captcha_answer"),wpqa_options("show_captcha_answer"),"question",$rand);
					}else if ($sort_key == "terms_active" && $type == "add" && isset($question_sort["terms_active"]["value"]) && $question_sort["terms_active"]["value"] == "terms_active") {
						$terms_link = wpqa_options("terms_link".($user == "user"?"_user":""));
						$terms_link_page = wpqa_options('terms_page'.($user == "user"?"_user":""));
						$out .= '<p class="wpqa_checkbox_p">
							<label for="terms_active-'.$rand.'">
								<span class="wpqa_checkbox"><input type="checkbox" id="terms_active-'.$rand.'" name="terms_active" value="on" '.(isset($question_add['terms_active']) && $question_add['terms_active'] == "on"?"checked='checked'":"").'></span>
								<span class="wpqa_checkbox_span">'.sprintf(wp_kses(__("By posting your question, you agree to the <a target='%s' href='%s'>terms of service</a>.","wpqa"),array('a' => array('href' => array(),'target' => array()))),(wpqa_options("terms_active_target") == "same_page"?"_self":"_blank"),(isset($terms_link) && $terms_link != ""?$terms_link:(isset($terms_link_page) && $terms_link_page != ""?get_page_link($terms_link_page):"#"))).'</span>
								<span class="required">*</span>
							</label>
						</p>';
					}
				}
			}
			
			if (is_user_logged_in() && is_super_admin($user_id) && $user != "user") {
				$out .= '<p class="wpqa_checkbox_p">
					<label for="sticky-'.$rand.'">
						<span class="wpqa_checkbox"><input type="checkbox" id="sticky-'.$rand.'" class="sticky_input" name="sticky" value="sticky"'.($type == "add" && isset($question_add['sticky']) && $question_add['sticky'] == "sticky"?" checked='checked'":($type == "edit" && ((isset($question_edit['sticky']) && $question_edit['sticky'] == "sticky") || (!isset($question_edit['sticky']) && $q_sticky))?" checked='checked'":"")).'></span>
						<span class="wpqa_checkbox_span">'.esc_html__("Stick this question","wpqa")." ".esc_html__("Note: this option shows for the admin only!","wpqa").'</span>
					</label>
				</p>';
			}
		
		$out .= '</div>
		
		<p class="form-submit">';
			if ($type == "edit") {
				$out .= '<input type="hidden" name="ID" value="'.$get_question.'">';
			}
			if (wpqa_is_add_questions()) {
				$get_user_id = apply_filters("wpqa_add_question_user",esc_attr(get_query_var(apply_filters('wpqa_add_questions','add_question'))));
			}
			if (($type == "edit" && $question_user_id != "" && $question_user_id > 0) || ($user == "user" && isset($get_user_id) && $get_user_id != "")) {
				$out .= '<input type="hidden" class="ask_question_user" name="user_id" value="'.($type == "edit"?(int)$question_user_id:(int)$get_user_id).'">';
			}
			if ($popup == "popup") {
				$out .= '<input type="hidden" name="question_popup" value="popup">';
			}
			$out .= '<input type="hidden" name="form_type" value="'.$type.'_question">
			<input type="hidden" name="wpqa_'.$type.'_question_nonce" value="'.wp_create_nonce("wpqa_".$type."_question_nonce").'">
			<input type="submit" value="'.($type == "add"?esc_html__("Publish Your Question","wpqa"):esc_html__("Edit Your Question","wpqa")).'" class="button-default button-hide-click">
			<span class="load_span"><span class="loader_2"></span></span>
		</p>
	
	</form>';
	return $out;
}?>