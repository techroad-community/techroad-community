<?php if ($wp_page_template == "template-landing.php") {
	$home_page = (int)discy_post_meta("home_page");
	if (is_user_logged_in()) {
		if (is_home() || is_front_page()) {
			if ($home_page != "" && $home_page > 0) {
				wp_redirect(esc_url(get_permalink($home_page)));
				exit;
			}
		}else {
			wp_redirect(esc_url(home_url('/')));
			exit;
		}
	}
	$register_style     = discy_post_meta("register_style");
	$register_menu      = discy_post_meta("register_menu");
	$register_headline  = discy_post_meta("register_headline");
	$register_paragraph = discy_post_meta("register_paragraph");

	$custom_logo = discy_post_meta("custom_logo");
	if ($custom_logo == "on") {
		$logo_display = "custom_image";
		$logo_img     = discy_image_url_id(discy_post_meta("logo_landing"));
		$retina_logo  = discy_image_url_id(discy_post_meta("logo_landing_retina"));
		$logo_height  = discy_post_meta("logo_landing_height");
		$logo_width   = discy_post_meta("logo_landing_width");
	}
}else {
	$register_style     = discy_options("register_style");
	$register_menu      = discy_options("register_menu");
	$register_headline  = discy_options("register_headline");
	$register_paragraph = discy_options("register_paragraph");
}
$its_not_login = true;
$footer_copyrights = discy_options("footer_copyrights");?>
<div class="login-page-cover"></div>
<div class="login-opacity"></div>
<div class="discy-container">
	<header class="header-login">
		<?php include locate_template("theme-parts/logo.php");?>
		<nav class="nav float_r" itemscope="" itemtype="https://schema.org/SiteNavigationElement">
			<h3 class="screen-reader-text"><?php echo esc_attr(get_bloginfo('name','display'))?> <?php esc_html_e('Navigation','discy')?></h3>
			<?php wp_nav_menu(array('container' => '','container_class' => 'nav top-nav clearfix','menu' => $register_menu,'fallback_cb' => 'discy_nav_fallback'));?>
		</nav><!-- End nav -->
		<div class="mobile-menu">
			<div class="mobile-menu-click">
				<i class="icon-menu"></i>
			</div>
		</div><!-- End mobile-menu -->
	</header>
	<?php include locate_template("includes/mobile-menu.php");
	$confirm_email = (class_exists("WPQA")?wpqa_users_confirm_mail():"");?>
	<main class="discy-login-wrap<?php echo ($confirm_email == "yes" || $register_style == "style_2"?" discy-login-2":"")?>">
		<?php do_action("wpqa_show_session");
		if ($confirm_email == "yes") {
			wpqa_activation_account(true);
		}else {?>
			<div class="<?php echo ($register_style == "style_2"?"":"centered")?>">
				<div class="login-text-col <?php echo ($register_style == "style_2"?"col12":"col8")?>">
					<?php if ($register_headline != "") {?>
						<h2><?php echo discy_kses_stip($register_headline)?></h2>
					<?php }
					if ($register_paragraph != "") {?>
						<p><?php echo discy_kses_stip($register_paragraph)?></p>
					<?php }?>
				</div>
				<div class="login-forms-col col4<?php echo ($register_style != "style_2"?"":" col4-offset")?>">
					<?php if (class_exists("WPQA")) {
						wpqa_head_content("login",$its_not_login);
					}?>
				</div>
			</div>
		<?php }?>
	</main>
	<footer class="footer-login"><p class="copyrights"><?php echo discy_kses_stip($footer_copyrights)?></p></footer>
</div>