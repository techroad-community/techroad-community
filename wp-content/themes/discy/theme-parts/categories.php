<?php if (is_page()) {
	$cat_sort  = discy_post_meta("cat_sort");
	$cat_order = discy_post_meta("cat_order");
	$cats_tax  = discy_post_meta("cats_tax");
	$number    = discy_post_meta("cats_per_page");
}else {
	$cat_sort  = (isset($_GET["cat_filter"]) && $_GET["cat_filter"] != ""?esc_html($_GET["cat_filter"]):"count");
	$cat_order = "DESC";
	$cats_tax  = wpqa_search_type();
}

if ($cats_tax == 'post' || $cats_tax == 'category') {
	$cat_type = 'category';
	$post_type_cats = 'post';
}else {
	$cat_type = 'question-category';
	$post_type_cats = 'question';
}

$cat_sort = (isset($_GET["cat_filter"]) && $_GET["cat_filter"] != ""?esc_html($_GET["cat_filter"]):(isset($cat_sort) && $cat_sort != ""?$cat_sort:"count"));

$discy_sidebar = discy_sidebars("sidebar_where");

$search_value = wpqa_search();
if ($search_value != "") {
	$search_args = array('search' => $search_value);
}else {
	$search_args = array();
}

$number = (isset($number) && $number > 0?$number:apply_filters('discy_cats_per_page',4*get_option('posts_per_page',10)));
$paged  = (get_query_var("paged") != ""?(int)get_query_var("paged"):(get_query_var("page") != ""?(int)get_query_var("page"):1));
$offset = ($paged-1)*$number;
$cat_order = (isset($_GET["cat_filter"]) && $_GET["cat_filter"] == "name"?"ASC":$cat_order);
$cats  = get_terms($cat_type,array_merge($search_args,array('hide_empty' => 0)));
$terms = get_terms($cat_type,array_merge($search_args,array(
	'orderby'    => $cat_sort,
	'order'      => $cat_order,
	'number'     => $number,
	'offset'     => $offset,
	'hide_empty' => 0
)));

$all_cat_pages = ceil(count($cats)/$number);
if (!empty($terms) && !is_wp_error($terms)) {
	$term_list = '<div class="row cats-sections">';
		foreach ($terms as $term) {
			$term_list .= '<div class="col '.($discy_sidebar == "full"?"col4":"col6").'">
				<div class="cat-sections">
					<a href="'.esc_url(get_term_link($term)).'" title="'.esc_attr(sprintf(($cats_tax == 'post' || $cats_tax == 'category'?esc_html__('View all posts under %s','discy'):esc_html__('View all questions under %s','discy')),$term->name)).'"><i class="icon-folder"></i>'.$term->name.'</a>
				</div>
			</div>';
		}
	$term_list .= '</div>';
	echo ($term_list);
	if ($all_cat_pages > 1) {
		echo '<div class="main-pagination"><div class="pagination">'.
		    paginate_links(array(
		    	'base'      => esc_url(add_query_arg('page','%#%')),
		    	'format'    => (wpqa_is_search()?'':'page/%#%/'),
		    	'current'   => max(1, $paged),
		    	'total'     => $all_cat_pages,
		    	'prev_text' => '<i class="icon-left-open"></i>',
		    	'next_text' => '<i class="icon-right-open"></i>',
		    )).
	    '</div></div>';
	}
}else {
	$no_cats = true;
}

if ($search_value != "" && isset($no_cats) && $no_cats == true) {
	include locate_template("theme-parts/search-none.php");
}?>