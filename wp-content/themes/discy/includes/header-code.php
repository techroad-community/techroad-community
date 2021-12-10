<?php $loader_option = discy_options("loader");
$mobile_bar = discy_options("mobile_bar");
$mobile_sign = discy_options("mobile_sign");
$header_boxed = discy_options("header_boxed");
$header_height = discy_options("header_height");
$header_style = discy_options("header_style");
$header_skin = discy_options("header_skin");
$header_fixed = discy_options("header_fixed");
$search_value = apply_filters("wpqa_get_search_filter",false);
$header_search = discy_options("header_search");
$big_search = discy_options("big_search");
$live_search = discy_options('live_search');
$header_user_login = discy_options("header_user_login");
$user_login_style = discy_options("user_login_style");
$header_notifications = discy_options("header_notifications");
$active_notifications = discy_options("active_notifications");
$active_activity_log = discy_options("active_activity_log");
$notifications_style = discy_options("notifications_style");
$active_message = discy_options("active_message");
$header_messages = discy_options("header_messages");
$messages_style = discy_options("messages_style");
$active_points = discy_options("active_points");
$custom_page_setting = discy_post_meta("custom_page_setting");
if ((is_single() || is_page()) && isset($custom_page_setting) && $custom_page_setting == "on") {
	$breadcrumbs = discy_post_meta("breadcrumbs");
}else {
	$breadcrumbs = discy_options("breadcrumbs");
}?>
<div class="background-cover"></div>
<?php if ($loader_option == "on") {?>
	<div class="loader"><i class="loader_html fa-spin"></i></div>
<?php }

if (is_user_logged_in()) {
	$user_id = get_current_user_id();
}

$category_id  = "";
if (is_category() || is_single() || is_tax("question-category")) {
	if (is_tax("question-category")) {
		$tax_id = get_term_by('slug',get_query_var('term'),"question-category");
		$category_id = $tax_id->term_id;
	}else if (is_category()) {
		$category_id = esc_attr(get_query_var('cat'));
	}else if (is_single()) {
		if (is_singular("question")) {
			$get_category = get_the_terms(get_the_ID(),"question-category");
		}else {
	    	$get_category = get_the_category(get_the_ID());
	    }
	    if (!empty($get_category[0]->term_id)) {
	    	$category_single_id = $get_category[0]->term_id;
	    	$custom_logo = discy_term_meta("custom_logo",$category_single_id);
	    	if (isset($custom_logo) && $custom_logo == "on") {
	    		$logo_single = discy_term_meta("logo_single",$category_single_id);
	    		if ($logo_single == "on") {
	    			$category_id = $category_single_id;
	    		}
	    	}
	    }
	}
	$custom_logo = discy_term_meta("custom_logo",$category_id);
	if ($custom_logo == "on") {
		$logo_display = discy_term_meta("logo_display",$category_id);
		$logo_img     = discy_image_url_id(discy_term_meta("logo_img",$category_id));
		$retina_logo  = discy_image_url_id(discy_term_meta("retina_logo",$category_id));
		$logo_height  = discy_term_meta("logo_height",$category_id);
		$logo_width   = discy_term_meta("logo_width",$category_id);
	}
}

/* Head content */
do_action("wpqa_head_content");
$confirm_email = (class_exists("WPQA")?wpqa_users_confirm_mail():"");?>

<div id="wrap" class="<?php echo (is_user_logged_in()?"wrap-login":"wrap-not-login").($header_fixed == "on"?" fixed-enabled":"")?>">
	<div class="hidden-header<?php echo ($header_search != "on"?" header-no-search":"").($big_search == "on" && $header_search == "on"?" header-big-search":"").($header_style == "simple"?" header-simple":"").($header_height == "style_2"?" header-2":"")." header-".$header_skin.($confirm_email != "yes" && $mobile_bar == "on"?" mobile_bar_active":"")?>">
		<header class="header" itemscope="" itemtype="https://schema.org/WPHeader">
			<div class="discy-container">
				<div class="mobile-menu">
					<div class="mobile-menu-click">
						<i class="icon-menu"></i>
					</div>
				</div><!-- End mobile-menu -->
				<?php if ($header_style == "simple") {
					include locate_template("theme-parts/logo.php");
				}?>
				<div class="right-header float_r">
					<?php if (!is_user_logged_in()) {?>
						<a class="sign-in-lock mob-sign-<?php echo ($mobile_sign == "signup"?"up":"in").apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_'.($mobile_sign == "signup"?"signup":"login"),'')?>" href="<?php echo (class_exists("WPQA")?($mobile_sign == "signup"?wpqa_signup_permalink():wpqa_login_permalink()):"#")?>" data-toggle="modal"><i class="icon-lock"></i></a>
						<a class="button-default button-sign-in<?php echo apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_login','')?>" href="<?php echo (class_exists("WPQA")?wpqa_login_permalink():"#")?>" data-toggle="modal"><?php esc_html_e('Sign In','discy')?></a><a class="button-default<?php echo ($header_skin == "colored"?"":"-2")?> button-sign-up<?php echo apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_signup','')?>" href="<?php echo (class_exists("WPQA")?wpqa_signup_permalink():"#")?>"><?php esc_html_e('Sign Up','discy')?></a>
					<?php }else {
						$user_login_links = discy_options('user_login_links');
						$user_login_links = apply_filters("discy_user_login_links",$user_login_links);
						$login_links_keys = (isset($user_login_links) && is_array($user_login_links)?array_keys($user_login_links):"");?>
						<div class="user-login-area<?php echo ($active_notifications == "on"?"":" no-notifications")?>">
							<?php if (class_exists("WPQA") && $header_notifications == "on" && $active_notifications == "on") {?>
								<div class="notifications-area user-notifications<?php echo ($notifications_style == "style_2"?" user-notifications-2":"")?> float_r">
									<span class="notifications-click"></span>
									<i class="icon-bell"></i>
									<?php $notifications_number = discy_options("notifications_number");
									echo wpqa_get_notifications($user_id,$notifications_number,"on",true)?>
								</div><!-- End user-notifications -->
							<?php }
							
							if (class_exists("WPQA") && $header_messages == "on" && $active_message == "on" && $header_style == "simple") {?>
								<div class="user-messages user-notifications<?php echo ($messages_style == "style_2"?" user-notifications-2":"")?> float_r">
									<span class="notifications-click messages-click"></span>
									<i class="icon-mail"></i>
									<?php $messages_number = discy_options("messages_number");
									echo wpqa_get_messages($user_id,$messages_number,"on",true)?>
								</div><!-- End user-messages -->
							<?php }
							
							if ($header_user_login == "on") {
								$display_name = get_the_author_meta('display_name',$user_id);
								if (class_exists("WPQA") && $active_message == "on" && isset($user_login_links["messages"]["value"]) && $user_login_links["messages"]["value"] == "messages") {
									$num_message = wpqa_count_new_message((isset($user_id)?$user_id:0));
								}
								$num_message = (isset($num_message) && $num_message != "" && $num_message > 0?$num_message:0);
								if (isset($user_login_links["notifications"]["value"]) && $user_login_links["notifications"]["value"] == "notifications") {
									$num_notification = get_user_meta($user_id,$user_id.'_new_notifications',true);
								}
								$num_notification = (isset($num_notification) && $num_notification != "" && $num_notification > 0?$num_notification:0);
								$num_all = $num_message+$num_notification;?>
								<div class="user-login-click<?php echo ($user_login_style == "style_2"?" user-login-click-2":"").(isset($user_login_links) && is_array($user_login_links) && !empty($user_login_links) && ((isset($user_login_links["followers-questions"]["value"]) && $user_login_links["followers-questions"]["value"] == "followers-questions"))?" user-login-bigger":"")?> float_r">
									<?php if (isset($user_login_links) && is_array($user_login_links) && !empty($user_login_links)) {
										$i_count = 0;
										while ($i_count < count($user_login_links)) {
											if (isset($user_login_links[$login_links_keys[$i_count]]["value"]) && $user_login_links[$login_links_keys[$i_count]]["value"] != "" && $user_login_links[$login_links_keys[$i_count]]["value"] != "0") {
												$first_one = $i_count;
												break;
											}
											$i_count++;
										}
										if (isset($first_one)) {
											$first_one = $user_login_links[$login_links_keys[$first_one]]["value"];?>
											<span class="user-click"></span>
										<?php }else {?>
											<a href="<?php echo get_author_posts_url($user_id)?>" class="user-click"></a>
										<?php }
									}else {?>
										<a href="<?php echo get_author_posts_url($user_id)?>" class="user-click"></a>
									<?php }?>
									<div class="user-image float_l">
										<?php echo (isset($num_all) && $num_all != "" && $num_all > 0?'<span class="notifications-number">'.wpqa_count_number($num_all).'</span>':'');
										do_action("wpqa_user_avatar",array("user_id" => $user_id,"size" => 29,"name" => $display_name));?>
									</div>
									<div class="user-login float_l">
										<span><?php echo esc_html_e("Welcome","discy")?></span><br>
										<div class="float_l"><?php echo esc_attr($display_name)?></div>
									</div>
									<?php $show_header_menu = apply_filters("discy_show_header_menu",true,$user_login_links,$first_one,$user_id);
									if ($show_header_menu == true && isset($user_login_links) && is_array($user_login_links) && !empty($user_login_links)) {
										if (isset($first_one)) {
											$ask_question_to_users = discy_options("ask_question_to_users");
											$active_message = discy_options("active_message");?>
											<i class="icon-down-open-mini"></i>
											<ul>
												<?php foreach ($user_login_links as $key => $value) {
													do_action("discy_action_user_login_links",$user_login_links,$key,$value);
													if ($key == "user-profile" && isset($user_login_links["user-profile"]["value"]) && $user_login_links["user-profile"]["value"] == "user-profile") {?>
														<li><a href="<?php echo get_author_posts_url($user_id)?>"><i class="icon-user"></i><?php echo esc_html_e("User Profile","discy")?></a></li>
													<?php }else if ($key == "edit-profile" && isset($user_login_links["edit-profile"]["value"]) && $user_login_links["edit-profile"]["value"] == "edit-profile") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"edit")?>"><i class="icon-cog"></i><?php echo esc_html_e("Edit Profile","discy")?></a></li>
													<?php }else if ($ask_question_to_users == "on" && $key == "asked-questions" && isset($user_login_links["asked-questions"]["value"]) && $user_login_links["asked-questions"]["value"] == "asked-questions") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"asked_questions")?>"><i class="icon-sound"></i><?php esc_html_e("Asked Questions","discy")?></a></li>
													<?php }else if ($key == "best-answers" && isset($user_login_links["best-answers"]["value"]) && $user_login_links["best-answers"]["value"] == "best-answers") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"best_answers")?>"><i class="icon-graduation-cap"></i><?php esc_html_e("Best Answers","discy")?></a></li>
													<?php }else if ($key == "points" && isset($user_login_links["points"]["value"]) && $user_login_links["points"]["value"] == "points" && $active_points == "on") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"points")?>"><i class="icon-bucket"></i><?php esc_html_e("Points","discy")?></a></li>
													<?php }else if ($active_activity_log == "on" && $key == "activities" && isset($user_login_links["activities"]["value"]) && $user_login_links["activities"]["value"] == "activities") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"activities")?>"><i class="icon-cog"></i><?php esc_html_e("Activity Log","discy")?></a></li>
													<?php }else if ($active_message == "on" && $key == "messages" && isset($user_login_links["messages"]["value"]) && $user_login_links["messages"]["value"] == "messages") {?>
														<li>
															<a href="<?php do_action("wpqa_get_profile",$user_id,"messages")?>">
																<?php echo (isset($num_message) && $num_message != "" && $num_message > 0?'<span class="notifications-number">'.wpqa_count_number($num_message).'</span>':'')?>
																<i class="icon-mail"></i><?php esc_html_e("Messages","discy")?>
															</a>
														</li>
													<?php }else if ($key == "log-out" && isset($user_login_links["log-out"]["value"]) && $user_login_links["log-out"]["value"] == "log-out") {?>
														<li><a href="<?php do_action("wpqa_action_get_logout")?>"><i class="icon-logout"></i><?php esc_html_e("Logout","discy")?></a></li>
													<?php }else if ($key == "questions" && isset($user_login_links["questions"]["value"]) && $user_login_links["questions"]["value"] == "questions") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"questions")?>"><i class="icon-book-open"></i><?php esc_html_e("Questions","discy")?></a></li>
													<?php }else if ($key == "answers" && isset($user_login_links["answers"]["value"]) && $user_login_links["answers"]["value"] == "answers") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"answers")?>"><i class="icon-comment"></i><?php esc_html_e("Answers","discy")?></a></li>
													<?php }else if ($key == "polls" && isset($user_login_links["polls"]["value"]) && $user_login_links["polls"]["value"] == "polls") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"polls")?>"><i class="icon-megaphone"></i><?php esc_html_e("Polls","discy")?></a></li>
													<?php }else if ($key == "following" && isset($user_login_links["following"]["value"]) && $user_login_links["following"]["value"] == "following") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"following")?>"><i class="icon-user-add"></i><?php esc_html_e("Following","discy")?></a></li>
													<?php }else if ($key == "followers" && isset($user_login_links["followers"]["value"]) && $user_login_links["followers"]["value"] == "followers") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"followers")?>"><i class="icon-users"></i><?php esc_html_e("Followers","discy")?></a></li>
													<?php }else if ($key == "followed" && isset($user_login_links["followed"]["value"]) && $user_login_links["followed"]["value"] == "followed") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"followed")?>"><i class="icon-plus-circled"></i><?php esc_html_e("Followed","discy")?></a></li>
													<?php }else if ($key == "favorites" && isset($user_login_links["favorites"]["value"]) && $user_login_links["favorites"]["value"] == "favorites") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"favorites")?>"><i class="icon-star"></i><?php esc_html_e("Favorites","discy")?></a></li>
													<?php }else if ($key == "posts" && isset($user_login_links["posts"]["value"]) && $user_login_links["posts"]["value"] == "posts") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"posts")?>"><i class="icon-newspaper"></i><?php esc_html_e("Posts","discy")?></a></li>
													<?php }else if ($key == "comments" && isset($user_login_links["comments"]["value"]) && $user_login_links["comments"]["value"] == "comments") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"comments")?>"><i class="icon-chat"></i><?php esc_html_e("Comments","discy")?></a></li>
													<?php }else if ($key == "notifications" && isset($user_login_links["notifications"]["value"]) && $user_login_links["notifications"]["value"] == "notifications") {?>
														<li>
															<a href="<?php do_action("wpqa_get_profile",$user_id,"notifications")?>">
																<?php echo (isset($num_notification) && $num_notification != "" && $num_notification > 0?'<span class="notifications-number">'.wpqa_count_number($num_notification).'</span>':'')?>
																<i class="icon-bell"></i><?php esc_html_e("Notifications","discy")?>
															</a>
														</li>
													<?php }else if ($key == "paid-questions" && isset($user_login_links["paid-questions"]["value"]) && $user_login_links["paid-questions"]["value"] == "paid-questions") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"paid_questions")?>"><i class="icon-help-circled"></i><?php esc_html_e("Paid Questions","discy")?></a></li>
													<?php }else if ($key == "followers-questions" && isset($user_login_links["followers-questions"]["value"]) && $user_login_links["followers-questions"]["value"] == "followers-questions") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"followers_questions")?>"><i class="icon-book-open"></i><?php esc_html_e("Followers Questions","discy")?></a></li>
													<?php }else if ($key == "followers-answers" && isset($user_login_links["followers-answers"]["value"]) && $user_login_links["followers-answers"]["value"] == "followers-answers") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"followers_answers")?>"><i class="icon-comment"></i><?php esc_html_e("Followers Answers","discy")?></a></li>
													<?php }else if ($key == "followers-posts" && isset($user_login_links["followers-posts"]["value"]) && $user_login_links["followers-posts"]["value"] == "followers-posts") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"followers_posts")?>"><i class="icon-newspaper"></i><?php esc_html_e("Followers Posts","discy")?></a></li>
													<?php }else if ($key == "followers-comments" && isset($user_login_links["followers-comments"]["value"]) && $user_login_links["followers-comments"]["value"] == "followers-comments") {?>
														<li><a href="<?php do_action("wpqa_get_profile",$user_id,"followers_comments")?>"><i class="icon-chat"></i><?php esc_html_e("Followers Comments","discy")?></a></li>
													<?php }
												}?>
											</ul>
										<?php }
									}?>
								</div><!-- End user-login-click -->
							<?php }?>
						</div><!-- End user-login-area -->
					<?php }
					if (class_exists("WPQA") && is_user_logged_in() && $header_style == "simple") {?>
						<a href="<?php echo esc_url(wpqa_add_question_permalink())?>" class="button-default wpqa-question"><?php esc_html_e("Ask Question","discy")?></a>
					<?php }?>
				</div><!-- End right-header -->
				<div class="left-header float_l">
					<h2 class="screen-reader-text site_logo"><?php echo esc_attr(get_bloginfo('name','display'))?></h2>
					<?php if ($header_style != "simple") {
						include locate_template("theme-parts/logo.php");
					}?>
					<div class="mid-header float_l">
						<?php if ($header_search == "on") {?>
							<div class="header-search float_r">
								<form role="search" class="searchform main-search-form" method="get" action="<?php do_action("wpqa_search_permalink")?>">
									<div class="search-wrapper">
										<input type="search"<?php echo ($live_search == "on"?" class='live-search live-search-icon' autocomplete='off'":"")?> placeholder="<?php esc_attr_e('Type Search Words','discy')?>" name="search" value="<?php echo do_action("wpqa_get_search")?>">
										<?php if ($live_search == "on") {?>
											<div class="loader_2 search_loader"></div>
											<div class="search-results results-empty"></div>
										<?php }?>
										<input type="hidden" name="search_type" class="search_type" value="<?php do_action("wpqa_search_type")?>">
										<div class="search-click"></div>
										<button type="submit"><i class="icon-search"></i></button>
									</div>
								</form>
							</div><!-- End header-search -->
						<?php }
						if (($header_search == "on" && $big_search != "on") || $header_search != "on") {?>
							<nav class="nav float_l" itemscope="" itemtype="https://schema.org/SiteNavigationElement">
								<h3 class="screen-reader-text"><?php echo esc_attr(get_bloginfo('name','display'))?> <?php esc_html_e('Navigation','discy')?></h3>
								<?php if (is_user_logged_in()) {
									wp_nav_menu(array('container' => '','container_class' => 'nav top-nav clearfix','theme_location' => 'header_menu_login','fallback_cb' => 'discy_nav_fallback'));
								}else {
									wp_nav_menu(array('container' => '','container_class' => 'nav top-nav clearfix','theme_location' => 'header_menu','fallback_cb' => 'discy_nav_fallback'));
								}?>
							</nav><!-- End nav -->
						<?php }?>
					</div><!-- End mid-header -->
				</div><!-- End left-header -->
			</div><!-- End discy-container -->
		</header><!-- End header -->
		<?php if ($confirm_email != "yes" && $mobile_bar == "on") {?>
			<div class="mobile-bar">
				<div class="discy-container">
					<div class="mobile-bar-content">
						<div class="discy-container">
							<div class="mobile-bar-search">
								<a href="<?php do_action("wpqa_search_permalink")?>"><i class="icon-search"></i><?php esc_html_e("Search","discy")?></a>
								<form role="search" method="get" class="searchform main-search-form" action="<?php do_action("wpqa_search_permalink")?>">
									<i class="icon-left-open"></i>
									<input type="search"<?php echo ($live_search == "on"?" class='live-search' autocomplete='off'":"")?> name="search" value="<?php if ($search_value != "") {echo esc_html($search_value);}else {esc_html_e("Hit enter to search","discy");}?>" onfocus="if(this.value=='<?php esc_attr_e("Hit enter to search","discy")?>')this.value='';" onblur="if(this.value=='')this.value='<?php esc_attr_e("Hit enter to search","discy")?>';">
									<?php if ($live_search == "on") {?>
										<div class="loader_2 search_loader"></div>
										<div class="search-results results-empty"></div>
									<?php }?>
									<input type="hidden" name="search_type" class="search_type" value="<?php do_action("wpqa_search_type")?>">
								</form>
							</div>
							<div class="mobile-bar-ask">
								<?php if (class_exists("WPQA")) {?>
									<a href="<?php echo esc_url(wpqa_add_question_permalink())?>" class="wpqa-question<?php echo apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_question','')?>"><i class="icon-help-circled"></i><?php esc_html_e("Ask a Question","discy")?></a>
								<?php }?>
							</div>
						</div><!-- End discy-container -->
					</div><!-- End mobile-bar-content -->
				</div><!-- End discy-container -->
			</div><!-- End mobile-bar -->
		<?php }?>
	</div><!-- End hidden-header -->
	<?php include locate_template("includes/mobile-menu.php");

	include locate_template("includes/slider.php");

	do_action("discy_action_after_slider");

	include locate_template("includes/call-action.php");

	do_action("wpqa_cover_image");
	
	$blog_h_where = discy_options("blog_h_where");
	if ($blog_h_where == "header") {
		include locate_template("includes/blog-header-footer.php");
	}?>
	<div class="discy-content">
		<div class="discy-inner-content <?php echo ($confirm_email == "yes" || $site_users_only == "yes"?"main_full":discy_sidebars())?>">
			<div class="discy-container">
				<main class="discy-main-wrap discy-site-content float_l">
					<div class="discy-main-inner float_l">
						<?php $update_profile = (is_user_logged_in() && class_exists("WPQA")?wpqa_update_profile($user_id):"");
						$cover_image = discy_options("cover_image");
						if (!is_home() && !is_front_page() && isset($breadcrumbs) && $breadcrumbs == "on" && $confirm_email != "yes" && $site_users_only != "yes" && ($cover_image != "on" || ($cover_image == "on" && class_exists("WPQA") && !wpqa_is_user_profile()) || ($cover_image == "on" && class_exists("WPQA") && wpqa_is_user_edit_profile()))) {
							discy_breadcrumbs(($update_profile == "yes"?esc_html__("Edit profile","discy"):""),($update_profile == "yes"?false:true));
						}
						/* Header content */
						if ($confirm_email == "yes") {
							/* Session */
							do_action("wpqa_show_session");
							wpqa_activation_account(true,true);
						}
						do_action("wpqa_header_content",array("user_id" => (isset($user_id)?$user_id:0),"update_profile" => $update_profile));
						/* Header adv */
						$adv_404 = discy_options("adv_404");
						if (is_404() && $adv_404 == "on") {
							$adv_404 = "on";
						}else {
							$adv_404 = "";
						}
						if (($adv_404 != "on" && is_404()) || !is_404()) {
							echo discy_ads("header_adv_type_1","header_adv_code_1","header_adv_href_1","header_adv_img_1","","on","discy-ad-header","on");
						}?>
						<div class="clearfix"></div>