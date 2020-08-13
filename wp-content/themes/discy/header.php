<?php $site_users_only = (class_exists("WPQA")?wpqa_site_users_only():"");
$wp_page_template = discy_post_meta("_wp_page_template","",false);?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php echo ($site_users_only == "yes" || $wp_page_template == "template-landing.php"?"dicsuss-html-login ":"")?>no-js no-svg"<?php echo (is_singular('question')?' itemscope="" itemtype="https://schema.org/QAPage"':'')?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	
	<!-- Global site tag (gtag.js) - Google Ads: 987337206 -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=AW-987337206"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'AW-987337206');
	</script>
	<!-- Global site tag (gtag.js) - Google Ads: 987337206 end -->

	<?php wp_head();?>
</head>
<body <?php body_class();?>>
	<?php $logo_display = discy_options("logo_display");
	$logo_img    = discy_image_url_id(discy_options("logo_img"));
	$retina_logo = discy_image_url_id(discy_options("retina_logo"));
	$logo_height = discy_options("logo_height");
	$logo_width  = discy_options("logo_width");
	if ($site_users_only == "yes" || $wp_page_template == "template-landing.php") {
		include locate_template("includes/login-page.php");
		get_footer();
		die();
	}else {
		include locate_template("includes/header-code.php");
	}?>