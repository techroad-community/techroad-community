<?php /* Activation */
add_filter('wpqa_init','wpqa_activation_account');
if (!function_exists('wpqa_activation_account')) :
	function wpqa_activation_account($return = false,$confirm = false) {
		$confirm_email = wpqa_options("confirm_email");
		if (is_user_logged_in()) {
			$user_id = get_current_user_id();
			$if_user_id = get_user_by("id",$user_id);
		}
		if (is_user_logged_in() && isset($if_user_id->caps["activation"]) && $if_user_id->caps["activation"] == 1) {
			$get_user_a = (isset($_GET['u'])?esc_attr($_GET['u']):"");
			$get_activate = (isset($_GET['activate'])?esc_attr($_GET['activate']):"");
			if (isset($_GET['u']) && isset($_GET['activate'])) {
				$activation = get_user_meta($user_id,"activation",true);
				if ($activation == $get_activate) {
					$default_group = wpqa_options("default_group");
					$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
					wp_update_user( array ('ID' => $user_id, 'role' => $default_group) ) ;
					delete_user_meta($user_id,"activation");
					wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Your membership is now activated.","wpqa").'</p></div>','wpqa_session');
					wp_safe_redirect(esc_url(home_url('/')));
					exit();
				}else {
					if ($return == true) {
						echo '<div class="alert-message error"><i class="icon-cancel"></i><p>'.sprintf(wp_kses(__("Your confirmation link has expired or not valid, Please request a new link, kindly <a href='%s'>Click here</a> to re-send another confirmation email.","wpqa"),array('a' => array('href' => array()))),esc_url(add_query_arg(array("get_activate" => "do"),esc_url(home_url('/'))))).'</p></div>';
					}
				}
			}else if (!isset($_GET['activate']) && !isset($_SESSION['wpqa_session'])) {
				if (isset($_GET['get_activate']) && $_GET['get_activate'] == "do") {
					$user_email = get_the_author_meta("user_email",$user_id);
					$display_name = get_the_author_meta("display_name",$user_id);
					$rand_a = rand(1,1000000000000);
					update_user_meta($user_id,"activation",$rand_a);
					$confirm_link = esc_url(add_query_arg(array("u" => $user_id,"activate" => $rand_a),esc_url(home_url('/'))));
					$send_text = wpqa_send_email(wpqa_options("email_confirm_link"),$user_id,"","","",$confirm_link);
					$last_message_email = wpqa_email_code($send_text);
					$email_title = wpqa_options("title_confirm_link");
					$email_title = ($email_title != ""?$email_title:esc_html__("Confirm account","wpqa"));
					wpqa_sendEmail(wpqa_options("email_template"),get_bloginfo('name'),esc_html($user_email),esc_html($display_name),$email_title,$last_message_email);
					wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Check your email again.","wpqa").'</p></div>','wpqa_session');
					wp_safe_redirect(esc_url(home_url('/')));
					die();
				}else {
					if ($return == true) {
						echo '<div class="alert-message error"><i class="icon-cancel"></i><p>'.sprintf(wp_kses(__("A confirmation email has been sent to your registered email account, If you have not received the confirmation email, kindly <a href='%s'>Click here</a> to re-send another confirmation email.","wpqa"),array('a' => array('href' => array()))),esc_url(add_query_arg(array("get_activate" => "do"),esc_url(home_url('/'))))).'</p></div>';
					}
				}
			}
			if ($confirm == true) {
				get_footer();
				die();
			}
		}else {
			if (isset($_GET['u']) && isset($_GET['activate'])) {
				wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.sprintf(wp_kses(__("You are already logged in, you have activated your membership, go to your <a href='%s'>profile</a>.","wpqa"),array('a' => array('href' => array()))),wpqa_profile_url($user_id)).'</p></div>','wpqa_session');
				wp_safe_redirect(esc_url(home_url('/')));
				die();
			}
		}
	}
endif;?>