<?php

/* @author    2codeThemes
*  @package   WPQA/shortcodes
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Signup attr */
if (!function_exists('wpqa_signup_attr')) :
	function wpqa_signup_attr($atts, $content = null) {
		global $posted;
		$a = shortcode_atts( array(
		    'dark_button' => '',
		), $atts );
		$out = '';
		if (is_user_logged_in()) {
			$out .= wpqa_login_already();
		}else {
			$ajax_file = wpqa_options("ajax_file");
			$ajax_file = ($ajax_file == "theme"?plugins_url('includes/ajax.php',dirname(__FILE__)):admin_url("admin-ajax.php"));
			$protocol = is_ssl() ? 'https' : 'http';
			$rand_w = rand(1,1000);
			if (shortcode_exists('wpqa_social_login') || shortcode_exists('rdp-linkedin-login') || shortcode_exists('oa_social_login') || shortcode_exists('wordpress_social_login') || shortcode_exists('apsl-login') || shortcode_exists('apsl-login-lite') || shortcode_exists('nextend_social_login')) {
				$out .= '<div class="wpqa_login_social">';
					$out .= (shortcode_exists('wpqa_social_login')?do_shortcode("[wpqa_social_login]"):"").
					(shortcode_exists('rdp-linkedin-login')?do_shortcode("[rdp-linkedin-login]"):"").
					(shortcode_exists('oa_social_login')?do_shortcode("[oa_social_login]"):"").
					(shortcode_exists('wordpress_social_login')?do_shortcode("[wordpress_social_login]"):"").
					(shortcode_exists('apsl-login')?do_shortcode("[apsl-login]"):"").
					(shortcode_exists('apsl-login-lite')?do_shortcode("[apsl-login-lite]"):"").
					(shortcode_exists('nextend_social_login')?do_shortcode("[nextend_social_login]"):"");
					$out .= '<div class="wpqa_login_social_div"><span>'.esc_html__("or use","wpqa").'</span></div>
				</div>';
			}
			$out .= '
			<form method="post" class="signup_form wpqa_form" enctype="multipart/form-data">
				<div class="wpqa_error_desktop wpqa_hide"><div class="wpqa_error"></div></div>
				<div class="wpqa_success"></div>
				<div class="form-inputs clearfix">
					<p>
						<label for="user_name_'.$rand_w.'">'.esc_html__("Username","wpqa").'<span class="required">*</span></label>
						<input type="text" class="required-item" name="user_name" id="user_name_'.$rand_w.'" value="'.(isset($posted["user_name"])?$posted["user_name"]:"").'">
						<i class="icon-user"></i>
					</p>
					<p>
						<label for="email_'.$rand_w.'">'.esc_html__("E-Mail","wpqa").'<span class="required">*</span></label>
						<input type="email" class="required-item" name="email" id="email_'.$rand_w.'" value="'.(isset($posted["email"])?$posted["email"]:"").'">
						<i class="icon-mail"></i>
					</p>
					<p>
						<label for="pass1_'.$rand_w.'">'.esc_html__("Password","wpqa").'<span class="required">*</span></label>
						<input type="password" class="required-item" name="pass1" id="pass1_'.$rand_w.'" autocomplete="off">
						<i class="icon-lock-open"></i>
					</p>
					<p>
						<label for="pass2_'.$rand_w.'">'.esc_html__("Confirm Password","wpqa").'<span class="required">*</span></label>
						<input type="password" class="required-item" name="pass2" id="pass2_'.$rand_w.'" autocomplete="off">
						<i class="icon-lock"></i>
					</p>';
					
					$out .= wpqa_add_captcha(wpqa_options("the_captcha_register"),wpqa_options("captcha_style"),wpqa_options("captcha_question"),wpqa_options("captcha_answer"),wpqa_options("show_captcha_answer"),"register",$rand_w);
					
					$terms_active_register = wpqa_options("terms_active_register");
					$terms_link_register = wpqa_options("terms_link_register");
					if ($terms_active_register == "on") {
						$terms_page_register = wpqa_options('terms_page_register');
						$out .= '<p class="wpqa_checkbox_p">
							<label for="agree_terms-'.$rand_w.'">
								<span class="wpqa_checkbox"><input type="checkbox" id="agree_terms-'.$rand_w.'" name="agree_terms" value="on" '.(isset($posted['agree_terms']) && $posted['agree_terms'] == "on"?"checked='checked'":"").'></span>
								<span class="wpqa_checkbox_span">'.sprintf(wp_kses(__("By registering, you agree to the <a target='%s' href='%s'>terms of service</a>.","wpqa"),array('a' => array('href' => array(),'target' => array()))),(wpqa_options("terms_active_target_register") == "same_page"?"_self":"_blank"),(isset($terms_link_register) && $terms_link_register != ""?$terms_link_register:(isset($terms_page_register) && $terms_page_register != ""?get_page_link($terms_page_register):"#"))).'<span class="required">*</span></span>
							</label>
						</p>';
					}
				$out .= '</div>

				<div class="clearfix"></div>
				<div class="wpqa_error_mobile wpqa_hide"><div class="wpqa_error"></div></div>

				<p class="form-submit">
					<span class="load_span"><span class="loader_2"></span></span>
					<input type="hidden" name="form_type" value="wpqa-signup">
					<input type="hidden" name="ajax_url" value="'.$ajax_file.'">
					<input type="hidden" name="redirect_to" value="'.esc_url(wp_unslash( $protocol.'://'.wpqa_server('HTTP_HOST').wpqa_server('REQUEST_URI'))).'">
					<input type="hidden" name="wpqa_signup_nonce" value="'.wp_create_nonce("wpqa_signup_nonce").'">
					<input type="submit" name="register" value="'.esc_attr__("Signup","wpqa").'" class="button-default '.(isset($a["dark_button"]) && $a["dark_button"] == "dark_button"?"dark_button":"").'">
				</p>
			</form>';
		}
		return $out;
	}
endif;
/* Signup jQuery */
if (!function_exists('wpqa_signup_jquery')) :
	function wpqa_signup_jquery() {
		global $posted;
		$allow_spaces = wpqa_options("allow_spaces");
		$errors = new WP_Error();
		if ( isset( $_REQUEST['redirect_to'] ) ) $redirect_to = $_REQUEST['redirect_to']; else $redirect_to = esc_url(home_url('/'));
		// Process signup form
		$posted = array(
			'user_name'    => esc_html($_POST['user_name']),
			'email'        => esc_html($_POST['email']),
			'pass1'        => esc_html($_POST['pass1']),
			'pass2'        => esc_html($_POST['pass2']),
			'agree_terms'  => (isset($_POST['agree_terms']) && $_POST['agree_terms'] != ""?esc_html($_POST['agree_terms']):""),
			'redirect_to'  => $_POST['redirect_to'],
			'wpqa_captcha' => (isset($_POST['wpqa_captcha']) && $_POST['wpqa_captcha'] != ""?esc_html($_POST['wpqa_captcha']):""),
		);
		$posted = array_map('stripslashes', $posted);
		$posted['username'] = sanitize_user((isset($posted['username'])?$posted['username']:""));
		// Validation
		if ( empty($posted['user_name']) ) {
			$errors->add('required-user_name',esc_html__("Please enter your name.","wpqa"));
		}
		if ( $allow_spaces != "on" && $posted['user_name'] == trim($posted['user_name']) && strpos($posted['user_name'], ' ') !== false ) {
			$errors->add('required-user_name',esc_html__("Please enter your name without any spaces.","wpqa"));
		}
		if ( empty($posted['email']) ) {
			$errors->add('required-email',esc_html__("Please enter your email.","wpqa"));
		}
		if ( empty($posted['pass1']) ) {
			$errors->add('required-pass1',esc_html__("Please enter your password.","wpqa"));
		}
		if ( empty($posted['pass2']) ) {
			$errors->add('required-pass2',esc_html__("Please rewrite password.","wpqa"));
		}
		if ( $posted['pass1'] !== $posted['pass2'] ) {
			$errors->add('required-pass1',esc_html__("Password does not match.","wpqa"));
		}
		
		$the_captcha_register = wpqa_options("the_captcha_register");
		$captcha_style = wpqa_options("captcha_style");
		$captcha_question = wpqa_options("captcha_question");
		$captcha_answer = wpqa_options("captcha_answer");
		$show_captcha_answer = wpqa_options("show_captcha_answer");
		if ($the_captcha_register == "on") {
			if (empty($posted["wpqa_captcha"])) {
				$errors->add('required-captcha', esc_html__("There are required fields (captcha).","wpqa"));
			}
			if ($captcha_style == "question_answer") {
				if ($captcha_answer != $posted["wpqa_captcha"]) {
					$errors->add('required-captcha-error', esc_html__('The captcha is incorrect, Please try again.','wpqa'));
				}
			}else {
				if(!session_id()) session_start();
				if (isset($_SESSION["wpqa_code_captcha_register"]) && $_SESSION["wpqa_code_captcha_register"] != $posted["wpqa_captcha"]) {
					$errors->add('required-captcha-error', esc_html__('The captcha is incorrect, Please try again.','wpqa'));
				}
			}
		}
		
		$terms_active_register = wpqa_options("terms_active_register");
		if ($terms_active_register == "on" && $posted['agree_terms'] != "on") {
			$errors->add('required-terms', esc_html__("There are required fields (Agree of the terms).","wpqa"));
		}
		// Check the username
		if ( username_exists( $posted['user_name'] ) ) :
			$errors->add('required-user_name',esc_html__("This username is already registered.","wpqa"));
		endif;
		// Check the e-mail address
		if ( !is_email( $posted['email'] ) ) :
			$errors->add('required-email',esc_html__("Please write correctly email.","wpqa"));
		elseif ( email_exists( $posted['email'] ) ) :
			$errors->add('required-email',esc_html__("This email is already registered.","wpqa"));
		endif;
		if (wpqa_is_ajax()) :
			// Result
			$result = array();
			if ( !$errors->get_error_code() ) :
				do_action('register_post', $posted['user_name'], $posted['email'], $errors);
				$errors = apply_filters( 'registration_errors', $errors, $posted['user_name'], $posted['email'] );
				// if there are no errors, let's create the user account
				if ( !$errors->get_error_code() ) :
					$user_id = wp_create_user( $posted['user_name'], $posted['pass1'], $posted['email'] );
					if (is_wp_error($user_id)) {
						$errors->add('error', sprintf('<strong>'.esc_html__('Error:','wpqa').'</strong> '.esc_html__('Sorry, You can not register, Please contact the webmaster','wpqa').': ',get_option('admin_email')));
						$result['success'] = 0;
						foreach ($errors->errors as $error) {
							$result['error'] = $error[0];
							break;
						}
					}else {
						$edit_profile_items_1 = wpqa_options("edit_profile_items_1");
						$names_register = (isset($edit_profile_items_1["names"]["value"]) && $edit_profile_items_1["names"]["value"] == "names"?"on":0);
						$profile_picture = (isset($edit_profile_items_1["image_profile"]["value"]) && $edit_profile_items_1["image_profile"]["value"] == "image_profile"?"on":0);
						$country_register = (isset($edit_profile_items_1["country"]["value"]) && $edit_profile_items_1["country"]["value"] == "country"?"on":0);
						$city_register = (isset($edit_profile_items_1["city"]["value"]) && $edit_profile_items_1["city"]["value"] == "city"?"on":0);
						$profile_credential_register = (isset($edit_profile_items_3["profile_credential"]["value"]) && $edit_profile_items_3["profile_credential"]["value"] == "profile_credential"?"on":0);
						$phone_register = (isset($edit_profile_items_1["phone"]["value"]) && $edit_profile_items_1["phone"]["value"] == "phone"?"on":0);
						$gender_register = (isset($edit_profile_items_1["gender"]["value"]) && $edit_profile_items_1["gender"]["value"] == "gender"?"on":0);
						$age_register = (isset($edit_profile_items_1["age"]["value"]) && $edit_profile_items_1["age"]["value"] == "age"?"on":0);
						
						$names_required = wpqa_options("names_required");
						$profile_picture_required = wpqa_options("profile_picture_required");
						$country_required = wpqa_options("country_required");
						$city_required = wpqa_options("city_required");
						$profile_credential_required = wpqa_options("profile_credential_required");
						$phone_required = wpqa_options("phone_required");
						$gender_required = wpqa_options("gender_required");
						$age_required = wpqa_options("age_required");
						
						$confirm_email = wpqa_options("confirm_email");
						if ($confirm_email == "on") {
							$activation = get_role("activation");
							if (!isset($activation)) {
								add_role("activation","activation",array('read' => false));
							}
							$rand_a = rand(1,1000000000000);
							update_user_meta($user_id,"activation",$rand_a);
							$confirm_link = esc_url(add_query_arg(array("u" => $user_id,"activate" => $rand_a),esc_url(home_url('/'))));
							$send_text = wpqa_send_email(wpqa_options("email_confirm_link_2"),$user_id,"","","",$confirm_link);
							$last_message_email = wpqa_email_code($send_text);
							$email_title = wpqa_options("title_confirm_link_2");
							$email_title = ($email_title != ""?$email_title:esc_html__("Confirm account","wpqa"));
							wpqa_sendEmail(wpqa_options("email_template"),get_bloginfo('name'),esc_html($posted['email']),esc_html($posted['user_name']),$email_title,$last_message_email);
							wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Check your email please to activate your membership.","wpqa").'</p></div>','wpqa_session');
						}else {
							$default_group = wpqa_options("default_group");
							$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
						}
						wp_update_user( array ('ID' => $user_id, 'role' => ($confirm_email == "on"?'activation':$default_group),'user_nicename' => $posted['user_name'],'nickname' => $posted['user_name']) ) ;
						
						if (($profile_picture === "on" && $profile_picture_required == "on") || ($country_register === "on" && $country_required == "on") || ($city_register === "on" && $city_required == "on") || ($profile_credential_register === "on" && $profile_credential_required == "on") || ($age_register === "on" && $age_required == "on") || ($phone_register === "on" && $phone_required == "on") || ($gender_register === "on" && $gender_required == "on") || ($names_register === "on" && $names_required == "on")) {
							update_user_meta($user_id,"update_profile","yes");
						}
						
						$secure_cookie = is_ssl() ? true : false;
						wp_set_auth_cookie($user_id, true, $secure_cookie);
						
						$after_register = wpqa_options("after_register");
						$after_register_link = wpqa_options("after_register_link");
						
						if (isset($posted['redirect_to']) && $after_register == "same_page") {
							$redirect_to = $posted['redirect_to'];
						}else if (isset($user_id) && $user_id > 0 && $after_register == "profile") {
							$redirect_to = wpqa_profile_url($user_id);
						}else if ($after_register == "custom_link" && $after_register_link != "") {
							$redirect_to = esc_url($after_register_link);
						}else {
							$redirect_to = esc_url(home_url('/'));
						}

						$result['success'] = 1;
						$result['redirect'] = $redirect_to;
					}
				else :
					$result['success'] = 0;
					foreach ($errors->errors as $error) {
						$result['error'] = $error[0];
						break;
					}
				endif;
			else :
				$result['success'] = 0;
				foreach ($errors->errors as $error) {
					$result['error'] = $error[0];
					break;
				}
			endif;
			echo json_encode($result);
			die();
		else :
			wpqa_signup_jquery();
		endif;
		return $errors;
	}
endif;
/* Signup rocess */
if (!function_exists('wpqa_signup_process')) :
	function wpqa_signup_process() {
		if (isset($_POST['form_type']) && $_POST['form_type'] == "wpqa-signup") :
			global $wpqa_signup_errors;
			$wpqa_signup_errors = wpqa_signup_jquery();
		endif;
	}
endif;
add_action('wpqa_init','wpqa_signup_process');
/* Registration save */
add_action('user_register','wpqa_registration_save',10,1);
add_action('wpqa_user_register','wpqa_registration_save',10,1);
if (!function_exists('wpqa_registration_save')) :
	function wpqa_registration_save ($user_id) {
		$register_default_options = wpqa_options("register_default_options");
		$default_options = array("follow_email","show_point_favorite","received_email","received_message");
		foreach ($default_options as $key) {
			if (is_array($register_default_options) && in_array($key,$register_default_options)) {
				update_user_meta($user_id,$key,"on");
			}
		}
		$point_new_user = (int)wpqa_options("point_new_user");
		$active_points = wpqa_options("active_points");
		$gift_site = get_user_meta($user_id,"gift_site",true);
		if ($user_id > 0 && $gift_site == "" && $point_new_user > 0 && $active_points == "on") {
			update_user_meta($user_id,"gift_site","done");
			wpqa_add_points($user_id,$point_new_user,"+","gift_site");
			wpqa_notifications_activities($user_id,"","","","","gift_site","notifications",$point_new_user." ".esc_html__("Points","wpqa"));
		}
	}
endif;
/* Sanitize user */
add_filter('sanitize_user','wpqa_sanitize_user',10,3);
function wpqa_sanitize_user($username,$raw_username,$strict) {
	$username = wp_strip_all_tags($raw_username);
	$username = remove_accents($username);
	$username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|','',$username);
	$username = preg_replace('/&.+?;/','',$username);
	if ($strict) {
		$username = preg_replace('|[^a-z\p{Arabic}\p{Cyrillic}0-9 _.\-@]|iu','',$username);
	}
	$username = trim($username);
	$username = preg_replace('|\s+|',' ',$username);
	return $username;
}
/* Register URL */
add_filter('register_url','wpqa_register_url',10,1);
function wpqa_register_url() {
	return wpqa_signup_permalink();
}?>