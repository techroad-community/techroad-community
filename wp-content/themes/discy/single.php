<?php get_header();
	$page_id = $post_id_main = $post->ID;
	$discy_sidebar_all = $discy_sidebar = discy_sidebars("sidebar_where");
	include locate_template("includes/".(is_singular("question")?'question':'loop')."-setting.php");
	if ( have_posts() ) :?>
		<div class="post-articles<?php echo (is_singular("question")?" question-articles":"")?>">
			<?php while ( have_posts() ) : the_post();
				do_action("discy_action_before_post_content");
				include locate_template("theme-parts/content".(is_singular("question")?"-question":"").".php");
				do_action("discy_action_after_post_content");
			endwhile;?>
		</div><!-- End post-articles -->
	<?php else :
		include locate_template("theme-parts/content-none.php");
	endif;
get_footer();?>