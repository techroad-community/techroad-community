<?php
add_action('wp_enqueue_scripts','discy_enqueue_parent_theme_style',20);
function discy_enqueue_parent_theme_style() {
	wp_enqueue_style('discy-child-theme',get_stylesheet_uri(),'',null,'all');
}
function wpbsearchform( $form ) {
 
    $form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
    <div><label class="screen-reader-text" for="s">' . __('Search for:') . '</label>
    <input type="text" value="' . get_search_query() . '" name="s" id="s" />
    <input type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'" />
    </div>
    </form>';
 
    return $form;
}
 
add_shortcode('wpbsearch', 'wpbsearchform');
add_filter("wpqa_search_value_filter","discy_child_search_value_filter",1,1);
function discy_child_search_value_filter($search) {
	$search_explode = explode("(",$search);
	return (isset($search_explode[0]) && $search_explode[0] != ""?$search_explode[0]:$search);
}

