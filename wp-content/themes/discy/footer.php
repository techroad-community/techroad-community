<?php $site_users_only = (class_exists("WPQA")?wpqa_site_users_only():"");
$wp_page_template = discy_post_meta("_wp_page_template","",false);
if ($site_users_only != "yes" && $wp_page_template != "template-landing.php") {
	include locate_template("includes/footer-code.php");
}

wp_footer();?>
</body>
</html>