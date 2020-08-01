<?php
$related_number         = $related_number ? $related_number : 4;
$related_number_sidebar = $related_number_sidebar ? $related_number_sidebar : 6;
$related_number_sidebar = $related_style == "links"?$related_number:$related_number_sidebar;
$related_number_full    = $related_number_full ? $related_number_full : 8;
$related_number_full    = $related_style == "links"?$related_number:$related_number_full;
$excerpt_related_title  = isset($excerpt_related_title) ? $excerpt_related_title : 10;

$tags = wp_get_post_tags($post->ID);
if (isset($tags) && !empty($tags) && $query_related == "tags") {
	$term_list = wp_get_post_terms($post->ID, 'post_tag', array("fields" => "ids"));
	$related_query_ = array('tag__in' => $term_list);
}else if ($query_related == "author") {
	$related_query_ = array('author' => $post->post_author);
}else {
	$categories = get_the_category($post->ID);
	$category_ids = array();
	foreach ($categories as $l_category) {
		$category_ids[] = $l_category->term_id;
	}
	$related_query_ = array('category__in' => $category_ids);
}

if (isset($related_style) && $related_style == "links") {
	$args_images = array();
}else {
	$args_images = array('meta_key' => '_thumbnail_id');
}

if ($discy_sidebar == "centered") {
	$post_width = 269;
	$post_height = 180;
	$related_post_columns = "col6";
}else if ($discy_sidebar == "menu_sidebar") {
	$post_width = 300;
	$post_height = 180;
	$related_post_columns = "col6";
}else if ($discy_sidebar == "menu_left") {
	$post_width = 283;
	$post_height = 165;
	$related_post_columns = "col4";
	$related_number = $related_number_sidebar;
}else if ($discy_sidebar == "full") {
	$post_width = 255;
	$post_height = 150;
	$related_post_columns = "col3";
	$related_number = $related_number_full;
}else {
	$post_width = 256;
	$post_height = 150;
	$related_post_columns = "col4";
	$related_number = $related_number_sidebar;
}

$show_defult_image = apply_filters('discy_show_defult_image_post',true);
if ($show_defult_image != true) {
	$post_width = "";
	$post_height = "";
}

$args = array_merge($args_images,$related_query_,array('post__not_in' => array($post->ID),'posts_per_page'=> $related_number,'cache_results' => false,'no_found_rows' => true));
$related_query = new WP_Query( $args );

if (($query_related == "tags" || $query_related == "author") && !$related_query->have_posts()) {
	$categories = get_the_category($post->ID);
	$category_ids = array();
	foreach ($categories as $l_category) {
		$category_ids[] = $l_category->term_id;
	}
	$related_query_ = array('category__in' => $category_ids);
	
	$args = array_merge($args_images,$related_query_,array('post__not_in' => array($post->ID),'posts_per_page'=> $related_number,'cache_results' => false,'no_found_rows' => true));
	$related_query = new WP_Query( $args );
}

if ($related_query->have_posts()) {?>
	<div class="related-post<?php echo ($related_style == "links"?" related-post-links":"")?>">
		<div class="post-inner">
			<h3 class="section-title"><?php esc_html_e('Related Posts',"discy")?></h3>
			<?php if ($related_style == "links") {
				echo '<ul>';
			}else {
				echo '<div class="row">';
			}
				while ( $related_query->have_posts() ) : $related_query->the_post();
					if ($related_style == "links" && $excerpt_related_title > 0) {?>
						<li>
							<a itemprop="url" href="<?php the_permalink();?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark"><i class="icon-right-thin"></i><?php discy_excerpt_title($excerpt_related_title)?></a>
						</li>
					<?php }else {?>
						<div class="col <?php echo esc_attr($related_post_columns)?>">
							<article <?php post_class('clearfix');?> itemscope="" itemtype="https://schema.org/Article">
								<div class="related-image">
									<a href="<?php echo esc_url( get_permalink() )?>">
										<?php echo discy_get_aq_resize_img($post_width,$post_height);?>
									</a>
								</div>
								<?php if ($date_in_related == "on" || $comment_in_related == "on") {?>
									<div class="post-meta clearfix">
										<?php discy_meta($date_in_related,"",$comment_in_related)?>
									</div>
								<?php }?>
								<h2 class="post-title"><a class="post-title" href="<?php echo esc_url( get_permalink() )?>" itemprop="url" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark"><?php discy_excerpt_title($excerpt_related_title)?></a></h2>
							</article>
						</div>
					<?php }
				endwhile;
			if ($related_style == "links") {
				echo '</ul>';
			}else {
				echo '</div>';
			}?>
			<div class="clearfix"></div>
		</div>
	</div><!-- End related-post -->
<?php }
wp_reset_postdata();?>