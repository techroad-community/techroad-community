<?php $custom_call_action = discy_post_meta("custom_call_action");
if ($custom_call_action == "on") {
	$action_button = discy_post_meta("action_button");
	$action_logged = discy_post_meta("action_logged");
}else {
	$action_button = discy_options("action_button");
	$action_logged = discy_options("action_logged");
}
if ((is_user_logged_in() && ($action_logged == "logged" || $action_logged == "both")) || (!is_user_logged_in() && ($action_logged == "unlogged" || $action_logged == "both"))) {
	if ($custom_call_action == "on") {
		$call_action = discy_post_meta("call_action");
		$action_skin = discy_post_meta("action_skin");
		$action_style = discy_post_meta("action_style");
		$action_headline = discy_post_meta("action_headline");
		$action_paragraph = discy_post_meta("action_paragraph");
	}else {
		$call_action = discy_options("call_action");
		$action_home_pages = discy_options("action_home_pages");
		$action_pages = discy_options("action_pages");
		$action_pages = explode(",",$action_pages);
		$action_skin = discy_options("action_skin");
		$action_style = discy_options("action_style");
		$action_headline = discy_options("action_headline");
		$action_paragraph = discy_options("action_paragraph");
	}
	if ($call_action == "on" && ($custom_call_action == "on" || (((is_front_page() || is_home()) && $action_home_pages == "home_page") || $action_home_pages == "all_pages" || ($action_home_pages == "custom_pages" && is_page() && isset($action_pages) && is_array($action_pages) && isset($post->ID) && in_array($post->ID,$action_pages))))) {?>
		<div class="call-action-unlogged call-action-<?php echo esc_attr($action_skin).' call-action-'.esc_attr($action_style)?>">
			<div class="call-action-opacity"></div>
			<div class="discy-container">
				<div class="<?php echo ($action_style == "style_1"?"col6":"col12")?>">
					<?php if ($action_headline != "") {?>
						<h3><?php echo discy_kses_stip($action_headline)?></h3>
					<?php }
					if ($action_paragraph != "") {?>
						<p><?php echo discy_kses_stip($action_paragraph)?></p>
					<?php }
				if ($action_style == "style_1") {?>
					</div>
					<div class="col3">
				<?php }
				$show_action = false;
				if (is_user_logged_in()) {
					if (($action_logged == "logged" || $action_logged == "both") && ($action_button != "login" && $action_button != "signup")) {
						$show_action = true;
					}
				}else {
					if (($action_logged == "unlogged" || $action_logged == "both") || ($action_button == "login" || $action_button == "signup")) {
						$show_action = true;
					}
				}
				$discy_signup_call_action = apply_filters('discy_signup_call_action',true);
				if ($show_action == true && $discy_signup_call_action == true) {
					if ($action_button == "question") {
						$filter_class = "question";
						$action_button_target = "_self";
						$action_button_class = "wpqa-question";
						$action_button_link = (class_exists("WPQA")?wpqa_add_question_permalink():"#");
						$action_button_text = esc_html__("Ask A Question","discy");
					}else if ($action_button == "post") {
						$filter_class = "post";
						$action_button_class = "wpqa-post";
						$action_button_link = (class_exists("WPQA")?wpqa_add_post_permalink():"#");
						$action_button_text = esc_html__("Add A New Post","discy");
					}else if ($action_button == "login") {
						$filter_class = "login";
						$action_button_class = "login-panel";
						$action_button_link = (class_exists("WPQA")?wpqa_login_permalink():"#");
						$action_button_text = esc_html__("Login","discy");
					}else if ($action_button == "signup") {
						$filter_class = "signup";
						$action_button_class = "signup-panel";
						$action_button_link = (class_exists("WPQA")?wpqa_signup_permalink():"#");
						$action_button_text = esc_html__("Create A New Account","discy");
					}else {
						$filter_class = $action_button_class = "";
						if ($custom_call_action == "on") {
							$action_button_target = discy_post_meta("action_button_target");
							$action_button_link = discy_post_meta("action_button_link");
							$action_button_text = discy_post_meta("action_button_text");
						}else {
							$action_button_target = discy_options("action_button_target");
							$action_button_link = discy_options("action_button_link");
							$action_button_text = discy_options("action_button_text");
						}
					}
					$action_button_target = (isset($action_button_target) && $action_button_target == "same_page"?"_self":"_blank");?>
					<a target="<?php echo esc_attr($action_button_target)?>" class="<?php echo esc_attr($action_button_class)?> button-default<?php echo ($action_skin != "dark"?"-3":"")?> call-action-button<?php echo apply_filters('wpqa_pop_up_class','').(isset($filter_class) && $filter_class != ''?apply_filters('wpqa_pop_up_class_'.$filter_class,''):'')?>" href="<?php echo esc_url($action_button_link)?>"><?php echo esc_html($action_button_text)?></a>
				<?php }else {
					do_action("discy_after_button_call_action");
				}?>
				</div>
				<?php do_action("discy_after_call_action");?>
			</div><!-- End discy-container -->
		</div><!-- End call-action-unlogged -->
	<?php }
}?>