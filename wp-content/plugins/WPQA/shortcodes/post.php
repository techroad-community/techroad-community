<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

ob_start();
/* wpqa_add_post_attr */
function wpqa_add_post_attr($atts, $content = null) {
	$a = shortcode_atts( array(
	    'popup' => ''
	), $atts );
	$add_post_no_register = wpqa_options("add_post_no_register");
	$add_post = wpqa_options("add_post");
	$custom_permission = wpqa_options("custom_permission");
	if (is_user_logged_in()) {
		$user_get_current_user_id = get_current_user_id();
		$user_is_login = get_userdata($user_get_current_user_id);
		$roles = $user_is_login->allcaps;
	}
	
	if (($custom_permission == "on" && is_user_logged_in() && !is_super_admin($user_get_current_user_id) && empty($roles["add_post"])) || ($custom_permission == "on" && !is_user_logged_in() && $add_post != "on")) {
		$out = '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have a permission to add a post.","wpqa").'</p></div>';
	}else if (!is_user_logged_in() && $add_post_no_register != "on") {
		$out = '<div class="alert-message error"><i class="icon-cancel"></i></i><p>'.esc_html__("You must login to add post.","wpqa").'</p></div>'.do_shortcode("[wpqa_login]");
	}else {
		$out = wpqa_add_edit_post("add",(isset($a["popup"]) && $a["popup"] == "popup"?"popup":false));
	}
	return $out;
}
/* wpqa_edit_post_attr */
function wpqa_edit_post_attr() {
	$out = '';
	if (!is_user_logged_in()) {
		$out .= '<div class="alert-message error"><i class="icon-cancel"></i><p>'.esc_html__("You must login to edit post.","wpqa").'</p></div>'.do_shortcode("[wpqa_login]");
	}else {
		$can_edit_post = wpqa_options("can_edit_post");
		$user_id = get_current_user_id();
		if ($can_edit_post == "on" || is_super_admin($user_id)) {
			$get_post = (int)get_query_var(apply_filters('wpqa_edit_posts','edit_post'));
			$get_post_p = get_post($get_post);
			if (isset($get_post) && $get_post != 0 && $get_post_p && $get_post_p->post_type == "post") {
				if ($get_post_p->post_author != 0 || is_super_admin($user_id)) {
					if (($get_post_p->post_author == $user_id && $user_id != 0 && $get_post_p->post_status == "publish") || is_super_admin($user_id)) {
						$out .= wpqa_add_edit_post("edit");
					}else {
						$out .= '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry you can't edit this post.","wpqa").'</p></div>';
					}
				}else {
					$out .= '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry you can't edit this post.","wpqa").'</p></div>';
				}
			}else {
				$out .= '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry no post has you select or not found.","wpqa").'</p></div>';
			}
		}else {
			$out .= '<div class="alert-message warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry, you do not have a permission to edit a post.","wpqa").'</p></div>';
		}
	}
	return $out;
}
/* wpqa_add_post */
function wpqa_add_post($type) {
	if (isset($_POST["form_type"]) && $_POST["form_type"] == "add_post") :
		$return = wpqa_process_new_posts();
		if (is_wp_error($return)) :
   			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
   		else :
   			$get_post = get_post($return);
   			if ($get_post->post_type == "post") {
   				$user_id = get_current_user_id();
   				if (is_user_logged_in()) {
   					$post_publish = wpqa_options("post_publish");
   				}else {
   					$post_publish = wpqa_options("post_publish_unlogged");
   				}
				$approved_posts = wpqa_options("approved_posts");
				$post_status = "publish";
				if ($post_publish == "draft" && !is_super_admin($user_id)) {
					$post_status = "draft";
					if ($approved_posts == "on") {
						$posts_count = wpqa_count_posts_by_user($user_id,"post");
						if ($posts_count > 0) {
							$post_status = "publish";
						}
					}
				}
				
				if ($post_status == "draft") {
					$send_email_draft_posts = wpqa_options("send_email_draft_posts");
					if ($send_email_draft_posts == "on") {
						$send_text = wpqa_send_email(wpqa_options("email_draft_posts"),"",$return);
						$last_message_email = wpqa_email_code($send_text);
						$email_title = wpqa_options("title_new_draft_posts");
						$email_title = ($email_title != ""?$email_title:esc_html__("New post for review","wpqa"));
						wpqa_sendEmail(wpqa_options("email_template"),get_bloginfo('name'),get_bloginfo("admin_email"),get_bloginfo('name'),$email_title,$last_message_email);
					}
					wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Your post was successfully added, It's under review.","wpqa").'</p></div>','wpqa_session');
					if ($user_id > 0) {
						wpqa_notifications_activities($user_id,"","","","","approved_post","activities");
					}
					wp_redirect(esc_url(home_url('/')));
				}else {
					if ($user_id > 0) {
						wpqa_notifications_activities($user_id,"","",$return,"","add_post","activities");
					}
					$not_user = ($get_post->post_author > 0?$get_post->post_author:0);
					$the_author = 0;
					if ($get_post->post_author == 0) {
						$the_author = get_post_meta($return,'question_username',true);
					}
					wpqa_post_publish($return,$get_post->post_type,$the_author,"",$not_user,"",$user_id);
					update_post_meta($return,'post_approved_before',"yes");
					wp_redirect(get_permalink($return));
				}
			}
			exit;
   		endif;
	endif;
}
add_filter('wpqa_add_post','wpqa_add_post');
/* wpqa_process_new_posts */
function wpqa_process_new_posts() {
	global $post_add;
	set_time_limit(0);
	$errors = new WP_Error();
	$post_add = array();
	$user_id = get_current_user_id();
	$form_type = (isset($_POST["form_type"]) && $_POST["form_type"] != ""?$_POST["form_type"]:"");
	if ($form_type == "add_post") {
		$add_post_items = wpqa_options("add_post_items");
		$content_post = (isset($add_post_items["content_post"]["value"]) && $add_post_items["content_post"]["value"] == "content_post"?"on":"");
		$add_post_no_register = wpqa_options("add_post_no_register");
		
		$fields = array(
			'title','comment','category','post_tag','attachment','wpqa_captcha','username','email'
		);

		$fields = apply_filters('wpqa_add_post_fields',$fields);
		
		foreach ($fields as $field) :
			if (isset($_POST[$field])) $post_add[$field] = $_POST[$field]; else $post_add[$field] = '';
		endforeach;

		if (!isset($_POST['wpqa_add_post_nonce']) || !wp_verify_nonce($_POST['wpqa_add_post_nonce'],'wpqa_add_post_nonce')) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are an error, Please try again.","wpqa"));
		}
		
		if (!is_user_logged_in() && $add_post_no_register == "on" && $user_id == 0) {
			if (empty($post_add['username'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (username).","wpqa"));
			if (empty($post_add['email'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (email).","wpqa"));
			if (!is_email($post_add['email'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Please write correctly email.","wpqa"));
		}
		
		/* Validate Required Fields */

		if (empty($post_add['title'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (title).","wpqa"));
		if (empty($post_add['category']) || $post_add['category'] == '-1') $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (category).","wpqa"));
		if ($content_post == "on") {
			if (empty($post_add['comment'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (details).","wpqa"));
		}
		
		$the_captcha_post = wpqa_options("the_captcha_post");
		$captcha_users = wpqa_options("captcha_users");
		$captcha_style = wpqa_options("captcha_style");
		$captcha_question = wpqa_options("captcha_question");
		$captcha_answer = wpqa_options("captcha_answer");
		$show_captcha_answer = wpqa_options("show_captcha_answer");
		if ($the_captcha_post == "on" && ($captcha_users == "both" || ($captcha_users == "unlogged" && !is_user_logged_in()))) {
			if (empty($post_add["wpqa_captcha"])) {
				$errors->add('required-captcha',esc_html__("There are required fields (captcha).","wpqa"));
			}
			if ($captcha_style == "question_answer") {
				if ($captcha_answer != $post_add["wpqa_captcha"]) {
					$errors->add('required-captcha-error',esc_html__('The captcha is incorrect, Please try again.','wpqa'));
				}
			}else {
				if(!session_id()) session_start();
				if (isset($_SESSION["wpqa_code_captcha_post"]) && $_SESSION["wpqa_code_captcha_post"] != $post_add["wpqa_captcha"]) {
					$errors->add('required-captcha-error',esc_html__('The captcha is incorrect, Please try again.','wpqa'));
				}
			}
		}

		do_action('wpqa_add_post_errors',$errors,$post_add);
		
		if (sizeof($errors->errors)>0) return $errors;
		
		/* Create post */

		if (is_user_logged_in()) {
			$post_publish = wpqa_options("post_publish");
		}else {
			$post_publish = wpqa_options("post_publish_unlogged");
		}
		$approved_posts = wpqa_options("approved_posts");
		$post_status = "publish";
		if ($post_publish == "draft" && !is_super_admin($user_id)) {
			$post_status = "draft";
			if ($approved_posts == "on") {
				$posts_count = wpqa_count_posts_by_user($user_id,"post");
				if ($posts_count > 0) {
					$post_status = "publish";
				}
			}
		}

		$data = array(
			'post_content' => wpqa_kses_stip_wpautop($post_add['comment']),
			'post_title'   => wpqa_kses_stip($post_add['title']),
			'post_status'  => $post_status,
			'post_author'  => (!is_user_logged_in() && $add_post_no_register == "on"?0:$user_id),
			'post_type'    => 'post',
		);
			
		$post_id = wp_insert_post($data);
			
		if ($post_id==0 || is_wp_error($post_id)) wp_die(esc_html__("Error in post.","wpqa"));
		
		$terms = array();
		if ($post_add['category']) $terms[] = get_term_by('id',(is_array($post_add['category'])?end($post_add['category']):$post_add['category']),'category')->slug;
		if (sizeof($terms)>0) wp_set_object_terms($post_id,$terms,'category');
	
		$attachment = '';
	
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
			
		if(isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
				
			$attachment = wp_handle_upload($_FILES['attachment'],array('test_form'=>false),current_time('mysql'));
						
			if (isset($attachment['error'])) :
				$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $attachment['error']);
				
				return $errors;
			endif;
			
		endif;
		if ($attachment) :
			$attachment_data = array(
				'post_mime_type' => $attachment['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($attachment['file'])),
				'post_content'   => '',
				'post_status'	=> 'inherit',
				'post_author'	=> (!is_user_logged_in() && $add_post_no_register == "on"?0:$user_id)
			);
			$attachment_id = wp_insert_attachment($attachment_data,$attachment['file'],$post_id);
			$attachment_metadata = wp_generate_attachment_metadata($attachment_id,$attachment['file']);
			wp_update_attachment_metadata($attachment_id, $attachment_metadata);
			$set_post_thumbnail = set_post_thumbnail($post_id,$attachment_id);
			if (!$set_post_thumbnail) {
				add_post_meta($post_id,'added_file',$attachment_id,true);
			}
		endif;
		
		/* Tags */
		
		if (isset($post_add['post_tag']) && $post_add['post_tag']) :
					
			$tags = explode(',',trim(stripslashes($post_add['post_tag'])));
			$tags = array_map('strtolower',$tags);
			$tags = array_map('trim',$tags);
	
			if (sizeof($tags)>0) :
				wp_set_object_terms($post_id,$tags,'post_tag');
			endif;
			
		endif;
		
		if (!is_user_logged_in() && $add_post_no_register == "on" && $user_id == 0) {
			$post_username = sanitize_text_field($post_add['username']);
			$post_email = sanitize_text_field($post_add['email']);
			update_post_meta($post_id,'post_username',$post_username);
			update_post_meta($post_id,'post_email',$post_email);
		}else {
			$point_add_post = (int)wpqa_options("point_add_post");
			$active_points = wpqa_options("active_points");
			if ($post_status == "publish" && $point_add_post > 0 && $active_points == "on") {
				wpqa_add_points($user_id,$point_add_post,"+","add_post",$post_id);
			}
		}
		
		update_post_meta($post_id,"post_from_front","from_front");
		do_action('wpqa_finished_add_post',$post_id,$post_add);
		
		/* Successful */
		return $post_id;
	}
}
/* wpqa_edit_post */
function wpqa_edit_post($type) {
	if (isset($_POST["form_type"]) && $_POST["form_type"] == "edit_post") :
		$return = wpqa_process_edit_posts();
		if (is_wp_error($return)) :
   			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
   		else :
			$post_approved = wpqa_options("post_approved");
   			if ($post_approved == "on" || is_super_admin(get_current_user_id())) {
   				wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Has been edited successfully.","wpqa").'</p></div>','wpqa_session');
   				wp_redirect(get_permalink($return));
   			}else {
   				wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Your post was successfully added, It's under review.","wpqa").'</p></div>','wpqa_session');
   				wp_redirect(esc_url(home_url('/')));
   			}
			exit;
   		endif;
	endif;
}
add_filter('wpqa_edit_post','wpqa_edit_post');
/* wpqa_process_edit_posts */
function wpqa_process_edit_posts() {
	global $post_edit;
	set_time_limit(0);
	$errors = new WP_Error();
	$post_edit = array();
	$user_id = get_current_user_id();
	$add_post_items = wpqa_options("add_post_items");
	$content_post = (isset($add_post_items["content_post"]["value"]) && $add_post_items["content_post"]["value"] == "content_post"?"on":"");
	$fields = array(
		'title','comment','category','attachment','post_tag'
	);

	$fields = apply_filters('wpqa_edit_post_fields',$fields);
	
	foreach ($fields as $field) :
		if (isset($_POST[$field])) $post_edit[$field] = $_POST[$field]; else $post_edit[$field] = '';
	endforeach;
	
	/* Validate Required Fields */

	if (!isset($_POST['wpqa_edit_post_nonce']) || !wp_verify_nonce($_POST['wpqa_edit_post_nonce'],'wpqa_edit_post_nonce')) {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are an error, Please try again.","wpqa"));
	}
	
	$get_post = (int)get_query_var(apply_filters('wpqa_edit_posts','edit_post'));
	$get_post_p = get_post($get_post);
	
	if (isset($get_post) && $get_post != 0 && $get_post_p && $get_post_p->post_type == "post") {
		if (($get_post_p->post_author != $user_id || $get_post_p->post_status != "publish") && !is_super_admin($user_id)) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Sorry you can't edit this post.","wpqa"));
		}
	}else {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("Sorry no post select or not found.","wpqa"));
	}
	if (empty($post_edit['title'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (title).","wpqa"));
	if (empty($post_edit['category']) || $post_edit['category'] == '-1') $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (category).","wpqa"));
	
	if ($content_post == "on") {
		if (empty($post_edit['comment'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").' :&nbsp;</strong> '.esc_html__("There are required fields (content).","wpqa"));
	}

	do_action('wpqa_edit_post_errors',$errors,$post_edit);

	if (sizeof($errors->errors)>0) return $errors;
	
	$post_id = $get_post;
	
	$post_approved = wpqa_options("post_approved");
	
	/* Edit post */
	
	$post_name = array();
	$change_post_url = wpqa_options("change_post_url");
	if ($change_post_url == "on") {
		$post_name = array('post_name' => wpqa_kses_stip($post_edit['title']));
	}
	
	$data = array(
		'ID'           => sanitize_text_field($post_id),
		'post_content' => wpqa_kses_stip_wpautop($post_edit['comment']),
		'post_title'   => wpqa_kses_stip($post_edit['title']),
		'post_status'  => ($post_approved == "on" || is_super_admin($user_id)?"publish":"draft"),
	);
	
	wp_update_post(array_merge($post_name,$data));
	
	$terms = array();
	if ($post_edit['category']) $terms[] = get_term_by('id',$post_edit['category'],'category')->slug;
	if (sizeof($terms)>0) wp_set_object_terms($post_id,$terms,'category');
	
	$attachment = '';

	require_once(ABSPATH . 'wp-admin/includes/image.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
		
	if(isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
			
		$attachment = wp_handle_upload($_FILES['attachment'],array('test_form'=>false),current_time('mysql'));
					
		if (isset($attachment['error'])) :
			$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $attachment['error']);
			
			return $errors;
		endif;
		
	endif;
	if ($attachment) :
		$attachment_data = array(
			'post_mime_type' => $attachment['type'],
			'post_title'     => preg_replace('/\.[^.]+$/','',basename($attachment['file'])),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_author'    => (!is_user_logged_in() && $add_post_no_register == "on"?0:$user_id)
		);
		$attachment_id = wp_insert_attachment($attachment_data,$attachment['file'],$post_id);
		$attachment_metadata = wp_generate_attachment_metadata($attachment_id,$attachment['file']);
		wp_update_attachment_metadata($attachment_id, $attachment_metadata);
		set_post_thumbnail($post_id,$attachment_id);
	endif;
	
	/* Tags */
	
	if (isset($post_edit['post_tag']) && $post_edit['post_tag']) :
				
		$tags = explode(',',trim(stripslashes($post_edit['post_tag'])));
		$tags = array_map('strtolower',$tags);
		$tags = array_map('trim',$tags);

		if (sizeof($tags)>0) :
			wp_set_object_terms($post_id,$tags,'post_tag');
		endif;
		
	endif;

	do_action('wpqa_finished_edit_post',$post_id,$post_edit);
	
	/* Successful */
	return $post_id;
}
/* wpqa_add_edit_post */
function wpqa_add_edit_post($type,$popup = false) {
	global $post_edit,$post_add;
	$add_post_no_register = wpqa_options("add_post_no_register");
	$add_post_items = wpqa_options("add_post_items");
	$tags_post = (isset($add_post_items["tags_post"]["value"]) && $add_post_items["tags_post"]["value"] == "tags_post"?"on":"");
	$featured_image = (isset($add_post_items["featured_image"]["value"]) && $add_post_items["featured_image"]["value"] == "featured_image"?"on":"");
	$content_post = (isset($add_post_items["content_post"]["value"]) && $add_post_items["content_post"]["value"] == "content_post"?"on":"");
	$editor_post_details = wpqa_options("editor_post_details");
	$rand = rand(1,1000);
	
	if ($type == "edit") {
		$get_post = (int)get_query_var(apply_filters('wpqa_edit_posts','edit_post'));
		$get_post_p = get_post($get_post);
		$p_tag = "";
		if ($terms = wp_get_object_terms( $get_post, 'post_tag' )) :
			$terms_array = array();
			foreach ($terms as $term) :
				$terms_array[] = $term->name;
				$p_tag = implode(' , ', $terms_array);
			endforeach;
		endif;
		
		$category = wp_get_post_terms($get_post,'category',array("fields" => "ids"));
		if (isset($category) && is_array($category) && isset($category[0])) {
			$category = $category[0];
		}
	}
	
	$out = '<form class="form-post wpqa_form" method="post" enctype="multipart/form-data">'.(isset($_POST["form_type"]) && $_POST["form_type"] == $type."_post"?apply_filters('wpqa_'.$type.'_post',$type):"").'
		<div class="form-inputs clearfix">';
			if ($type == "add" && !is_user_logged_in() && $add_post_no_register == "on") {
				$out .= '<p>
					<label for="post-username-'.$rand.'">'.esc_html__("Username","wpqa").'<span class="required">*</span></label>
					<input name="username" id="post-username-'.$rand.'" class="the-username" type="text" value="'.(isset($post_add['username'])?$post_add['username']:'').'">
					<i class="icon-user"></i>
					<span class="form-description">'.esc_html__("Please type your username.","wpqa").'</span>
				</p>
				
				<p>
					<label for="post-email-'.$rand.'">'.esc_html__("E-Mail","wpqa").'<span class="required">*</span></label>
					<input name="email" id="post-email-'.$rand.'" class="the-email" type="text" value="'.(isset($post_add['email'])?$post_add['email']:'').'">
					<i class="icon-mail"></i>
					<span class="form-description">'.esc_html__("Please type your E-Mail.","wpqa").'</span>
				</p>';
			}
			
			$out .= '<p>
				<label for="post-title-'.$rand.'">'.esc_html__("Post Title","wpqa").'<span class="required">*</span></label>
				<input type="text" name="title" id="post-title-'.$rand.'" value="'.($type == "add" && isset($post_add['title'])?wpqa_kses_stip($post_add['title']):($type == "edit"?(isset($post_edit['title'])?wpqa_kses_stip($post_edit['title']):wpqa_kses_stip($get_post_p->post_title)):"")).'">
				<i class="icon-chat"></i>
				<span class="form-description">'.esc_html__("Please choose an appropriate title for the post.","wpqa").'</span>
			</p>';
			
			$out .= '<p>
				<label for="post-category-'.$rand.'">'.esc_html__("Category","wpqa").'<span class="required">*</span></label>
				'.wpqa_select_categories($rand,($type == "add" && isset($post_add['category'])?$post_add['category']:($type == "edit"?(isset($post_edit['category'])?$post_edit['category']:$category):"")),null,($type == "edit"?$get_post:""),'category').'
				<i class="icon-folder"></i>
				<span class="form-description">'.esc_html__("Please choose the appropriate section so easily search for your post.","wpqa").'</span>
			</p>'.apply_filters('wpqa_add_edit_post_after_category',false,$type,$post_add,$post_edit,(isset($get_post)?$get_post:0));
			
			if (isset($add_post_items) && is_array($add_post_items)) {
				foreach ($add_post_items as $sort_key => $sort_value) {
					if ($sort_key == "tags_post" && isset($add_post_items["tags_post"]["value"]) && $add_post_items["tags_post"]["value"] == "tags_post") {
						$out .= '<p class="wpqa_tag">
							<label for="post_tag-'.$rand.'">'.esc_html__("Tags","wpqa").'</label>
							<input type="text" class="input post_tag" name="post_tag" id="post_tag-'.$rand.'" value="'.($type == "add" && isset($post_add['post_tag'])?$post_add['post_tag']:($type == "edit"?(isset($post_edit['post_tag'])?$post_edit['post_tag']:$p_tag):"")).'" data-seperator=",">
							<span class="form-description">'.esc_html__("Please choose suitable Keywords Ex: ","wpqa").'<span class="color">'.esc_html__("post, video","wpqa").'</span>.</span>
						</p>'.apply_filters('wpqa_add_edit_post_after_tags',false,$type,$post_add,$post_edit,(isset($get_post)?$get_post:0));
					}else if ($sort_key == "featured_image" && isset($add_post_items["featured_image"]["value"]) && $add_post_items["featured_image"]["value"] == "featured_image") {
						if ($type == "edit") {
							$_thumbnail_id = get_post_meta($get_post,"_thumbnail_id",true);
							if ($_thumbnail_id != "") {
								$out .= '<div class="clearfix"></div>
								<div class="wpqa-delete-image">
									<span class="wpqa-delete-image-span">'.wpqa_get_aq_resize_img(250,250,"",$_thumbnail_id,"no","").'</span>
									<div class="clearfix"></div>
									<div class="button-default wpqa-remove-image" data-name="_thumbnail_id" data-type="post_meta" data-id="'.$get_post.'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'">'.esc_html__("Delete","wpqa").'</div>
									<div class="loader_2 loader_4"></div>
								</div>';
							}
						}
						$out .= '<label for="attachment-'.$rand.'">'.apply_filters("wpqa_filter_featured_image",esc_html__("Featured image","wpqa")).'</label>
						<div class="fileinputs">
							<input type="file" class="file" name="attachment" id="attachment-'.$rand.'">
							<div class="fakefile">
								<button type="button">'.esc_html__("Select file","wpqa").'</button>
								<span><i class="icon-arrow-up"></i>'.esc_html__("Browse","wpqa").'</span>
							</div>
							<i class="icon-camera"></i>
						</div>'.apply_filters('wpqa_add_edit_post_after_featured_image',false,$type,$post_add,$post_edit,(isset($get_post)?$get_post:0));
					}else if ($sort_key == "content_post" && isset($add_post_items["content_post"]["value"]) && $add_post_items["content_post"]["value"] == "content_post") {
						$out .= '<div class="wpqa_textarea'.($editor_post_details == "on"?"":" wpqa_textarea_p").'">
							<label for="post-details-'.$rand.'">'.esc_html__("Details","wpqa").($content_post == "on"?'<span class="required">*</span>':'').'</label>';
							if ($editor_post_details == "on") {
								$settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
								$settings = apply_filters('wpqa_post_editor_setting',$settings);
								ob_start();
								wp_editor(($type == "add" && isset($post_add['comment'])?wpqa_kses_stip($post_add['comment'],"yes"):($type == "edit"?(isset($post_edit['comment'])?wpqa_kses_stip($post_edit['comment'],"yes"):wpqa_kses_stip($get_post_p->post_content,"yes")):"")),"post-details-".$rand,$settings);
								$editor_contents = ob_get_clean();
								
								$out .= '<div class="the-details the-textarea">'.$editor_contents.'</div>';
							}else {
								$out .= '<textarea name="comment" id="post-details-'.$rand.'" class="the-textarea" aria-required="true" cols="58" rows="8">'.($type == "add" && isset($post_add['comment'])?wpqa_kses_stip($post_add['comment']):($type == "edit"?(isset($post_edit['comment'])?wpqa_kses_stip($post_edit['comment'],"yes"):wpqa_kses_stip($get_post_p->post_content,"yes")):"")).'</textarea>
								<i class="icon-pencil"></i>';
							}
						$out .= '</div>';
					}
				}
			}

			$out .= apply_filters('wpqa_add_edit_post_after_details',false,$type,$post_add,$post_edit,(isset($get_post)?$get_post:0));
			
			if ($type == "add") {
				$out .= '<div class="form-inputs clearfix">
					'.wpqa_add_captcha(wpqa_options("the_captcha_post"),wpqa_options("captcha_style"),wpqa_options("captcha_question"),wpqa_options("captcha_answer"),wpqa_options("show_captcha_answer"),"post",$rand).'
				</div>';
			}
		
		$out .= '</div>
		
		<p class="form-submit">';
			if ($type == "edit") {
				$out .= '<input type="hidden" name="ID" value="'.$get_post.'">';
			}
			if ($popup == "popup") {
				$out .= '<input type="hidden" name="post_popup" value="popup">';
			}
			$out .= '<input type="hidden" name="form_type" value="'.$type.'_post">
			<input type="hidden" name="wpqa_'.$type.'_post_nonce" value="'.wp_create_nonce("wpqa_".$type."_post_nonce").'">
			<input type="submit" value="'.($type == "add"?esc_html__("Publish Your Post","wpqa"):esc_html__("Edit Your Post","wpqa")).'" class="button-default button-hide-click">
			<span class="load_span"><span class="loader_2"></span></span>
		</p>
	
	</form>';
	return $out;
}?>