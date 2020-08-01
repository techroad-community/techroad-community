<?php

/* @author    2codeThemes
*  @package   WPQA/shortcodes
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Login shortcode */
if (!function_exists('wpqa_login')) :
	function wpqa_login ($atts, $content = null) {
		$protocol = is_ssl() ? 'https' : 'http';
		$a = shortcode_atts( array(
		    'forget'   => 'forget',
		    'register' => '',
		    'un-login' => '',
		), $atts );
		$out = '';
		if (is_user_logged_in()) {
			$out .= wpqa_login_already();
		}else {
			$ajax_file = wpqa_options("ajax_file");
			$ajax_file = ($ajax_file == "theme"?plugins_url('includes/ajax.php',dirname(__FILE__)):admin_url("admin-ajax.php"));
			$rand_l = rand(1,1000);
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
			$out .= '<form class="wpqa_form login-form wpqa_login" action="'.esc_url(home_url('/')).'" method="post">
				<div class="wpqa_error_desktop wpqa_hide"><div class="wpqa_error"></div></div>
				
				<div class="form-inputs clearfix">
					<p class="login-text">
						<label for="username_'.$rand_l.'">'.apply_filters("wpqa_username_login",esc_html__("Username","wpqa")).'<span class="required">*</span></label>
						<input id="username_'.$rand_l.'" class="required-item" type="text" name="log">
						<i class="icon-user"></i>
					</p>
					<p class="login-password">
						<label for="password_'.$rand_l.'">'.esc_html__("Password","wpqa").'<span class="required">*</span></label>
						<input id="password_'.$rand_l.'" class="required-item" type="password" name="pwd">
						<i class="icon-lock-open"></i>
					</p>'.
					
					wpqa_add_captcha(wpqa_options("the_captcha_login"),wpqa_options("captcha_style"),wpqa_options("captcha_question"),wpqa_options("captcha_answer"),wpqa_options("show_captcha_answer"),"login",$rand_l).'
					
				</div>
				
				<div class="rememberme normal_label">
					<label><span class="wpqa_checkbox"><input type="checkbox" name="rememberme" checked="checked"></span> <span class="wpqa_checkbox_span">'.esc_html__("Remember Me!","wpqa").'</span></label>
				</div>
				
				'.(isset($a["forget"]) && $a["forget"] == "false"?'':'<a href="#" class="'.(isset($a["un-login"]) && $a["un-login"] == true?"lost-password-login":"lost-password").'">'.esc_html__("Forget Password?","wpqa").'</a>').'

				<div class="clearfix"></div>
				<div class="wpqa_error_mobile wpqa_hide"><div class="wpqa_error"></div></div>

				<p class="form-submit login-submit">
					<span class="load_span"><span class="loader_2"></span></span>
					<input type="submit" value="'.esc_attr__("Login","wpqa").'" class="button-default login-submit">
					'.(isset($a["register"]) && $a["register"] == "button"?'<input type="button" class="signup button-default" value="'.esc_attr__("Register","wpqa").'">':'').'
				</p>
				
				<input type="hidden" name="redirect_to" value="'.esc_url(wp_unslash( $protocol.'://'.wpqa_server('HTTP_HOST').wpqa_server('REQUEST_URI'))).'">
				<input type="hidden" name="wpqa_login_nonce" value="'.wp_create_nonce("wpqa_login_nonce").'">
				<input type="hidden" name="ajax_url" value="'.$ajax_file.'">
				<input type="hidden" name="form_type" value="wpqa-login">
			</form>';
		}
		return $out;
	}
endif;
/* Login jQuery */
if (!function_exists('wpqa_login_jquery')) :
	function wpqa_login_jquery() {
		if ( isset( $_REQUEST['redirect_to'] ) ) $redirect_to = $_REQUEST['redirect_to']; else $redirect_to = esc_url(home_url('/'));
		if ( is_ssl() && force_ssl_admin() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )$secure_cookie = false; else $secure_cookie = '';

		$errors = new WP_Error();

		$posted = array(
			'log'          => esc_html($_POST['log']),
			'pwd'          => esc_html($_POST['pwd']),
			'redirect_to'  => $_POST['redirect_to'],
			'wpqa_captcha' => (isset($_POST['wpqa_captcha']) && $_POST['wpqa_captcha'] != ""?esc_html($_POST['wpqa_captcha']):""),
		);
		$posted = array_map('stripslashes', $posted);

		$the_captcha_login = wpqa_options("the_captcha_login");
		$captcha_style = wpqa_options("captcha_style");
		$captcha_question = wpqa_options("captcha_question");
		$captcha_answer = wpqa_options("captcha_answer");
		$show_captcha_answer = wpqa_options("show_captcha_answer");
		if ($the_captcha_login == "on") {
			if (empty($posted["wpqa_captcha"])) {
				$errors->add('required-captcha', esc_html__("There are required fields (captcha).","wpqa"));
			}
			if ($captcha_style == "question_answer") {
				if ($captcha_answer != $posted["wpqa_captcha"]) {
					$errors->add('required-captcha-error', esc_html__('The captcha is incorrect, Please try again.','wpqa'));
				}
			}else {
				if(!session_id()) session_start();
				if ($_SESSION["wpqa_code_captcha_login"] != $posted["wpqa_captcha"]) {
					$errors->add('required-captcha-error', esc_html__('The captcha is incorrect, Please try again.','wpqa'));
				}
			}
		}
		
		// Check the username
		if ( !$posted['log'] ) :
			$errors->add('empty_username', sprintf(esc_html__('%s ERROR %s: please insert your name.','wpqa'),'<strong>','</strong>'));
		elseif ( !$posted['pwd'] ) :
			$errors->add('empty_password', sprintf(esc_html__('%s ERROR %s: please insert your password.','wpqa'),'<strong>','</strong>'));
		endif;

		$result = array();
		if ( !$errors->get_error_code() ) {
			$after_login = wpqa_options("after_login");
			$after_login_link = wpqa_options("after_login_link");
			if (isset($posted['redirect_to']) && $after_login == "same_page") {
				$redirect_to = $posted['redirect_to'];
			}else if (isset($user->ID) && $user->ID > 0 && $after_login == "profile") {
				$redirect_to = wpqa_profile_url($user->ID);
			}else if ($after_login == "custom_link" && $after_login_link != "") {
				$redirect_to = esc_url($after_login_link);
			}else {
				$redirect_to = esc_url(home_url('/'));
			}

			$user = wp_signon('', $secure_cookie);
			
			if (wpqa_is_ajax()) :
				if ( !is_wp_error($user) ) :
					$result['success'] = 1;
					$result['redirect'] = $redirect_to;
				else :
					$result['success'] = 0;
					foreach ($user->errors as $error) {
						$result['error'] = $error[0];
						break;
					}
				endif;
				echo json_encode($result);
				die();
			else :
				if ( !is_wp_error($user) ) :
					wp_redirect($redirect_to);
					exit;
				endif;
			endif;
			return $user;
		}else {
			$result['success'] = 0;
			foreach ($errors->errors as $error) {
				$result['error'] = $error[0];
				break;
			}
			echo json_encode($result);
			die();
			return $errors;
		}
	}
endif;
/* Is Ajax */
if (!function_exists('wpqa_is_ajax')) :
	function wpqa_is_ajax() {
		if (defined('DOING_AJAX')) return true;
		$HTTP_X_REQUESTED_WITH = wpqa_server('HTTP_X_REQUESTED_WITH');
		if (isset($HTTP_X_REQUESTED_WITH) && strtolower(wpqa_server('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest') return true;
		return false;
	}
endif;
/* Login process */
if (!function_exists('wpqa_login_process')) :
	function wpqa_login_process() {
		if (isset($_POST['login-form']) && $_POST['login-form']) :
			global $wpqa_login_errors;
			$wpqa_login_errors = wpqa_login_jquery();
		endif;
	}
endif;
add_action('wpqa_init','wpqa_login_process');
/* Login already message */
if (!function_exists('wpqa_login_already')) :
	function wpqa_login_already() {
		$user_id = get_current_user_id();
		$username = get_the_author_meta('display_name',$user_id);
		return '<div class="wpqa-login-already"><p>'.sprintf(wp_kses(__('Hello %s (not %s? <a href="%s">Log out</a>)','wpqa'),array('a' => array('href' => array(),'class' => array()))),'<strong>'.$username.'</strong>','<strong>'.$username.'</strong>',wpqa_get_logout()).'</p><p>'.sprintf(wp_kses(__('From your profile you can view your <a href="%s">recent questions</a>, <a href="%s">edit your password and profile details</a>.','wpqa'),array('a' => array('href' => array(),'class' => array()))),wpqa_get_profile_permalink($user_id,'questions'),wpqa_get_profile_permalink($user_id,'edit')).'</p></div>';
	}
endif;
/* Login URL */
$redirect_wp_admin_unlogged = wpqa_options("redirect_wp_admin_unlogged");
if ($redirect_wp_admin_unlogged == "on") {
	add_filter('login_url','wpqa_login_url',10,1);
}
if (!function_exists('wpqa_login_url')) :
	function wpqa_login_url() {
		return wpqa_login_permalink();
	}
endif;
/* Login redirect */
add_action('wpqa_init','wpqa_login_redirect',10,1);
if (!function_exists('wpqa_login_redirect')) :
	function wpqa_login_redirect() {
		$redirect_wp_admin_unlogged = wpqa_options("redirect_wp_admin_unlogged");
		if ($redirect_wp_admin_unlogged == "on" && isset($GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php' && (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] == 'login'))) {
			wp_safe_redirect(wpqa_login_permalink());
			exit;
		}
		if (is_admin() && !wpqa_is_ajax() && is_user_logged_in()) {
			$redirect_wp_admin = wpqa_options("redirect_wp_admin");
			if ($redirect_wp_admin == "on") {
				$redirect_groups = wpqa_options("redirect_groups");
				$user_info = get_userdata(get_current_user_id());
				$user_group = $user_info->roles[0];
				if (is_array($redirect_groups) && in_array($user_group,$redirect_groups)) {
					wp_safe_redirect(home_url());
					exit;
				}
			}
		}
	}
endif;?>