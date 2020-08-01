<?php
/*
Plugin Name: WPCS ( Wordpress Custom Search )
Plugin URI: http://www.tech9logy.com
Description: A very simple and lightweight Plugin for adding Custom Search with taxonomy in wordpress, with just a short code.
Version: 1.1
Author: Tech9logy Creators
Author URI: http://www.tech9logy.com
*/
/* Runs on plugin activation*/

function wpcs_activation() {
	add_option( 'wpcs_top_head', 'Search', '', true );
	add_option( 'wpcs_sub_head', 'Search Anyhting you want', '', true );	
	add_option( 'wpcs_default_cat', '1', '', true );
	add_option( 'wpcs_hide_empty', '1', '', true );
	add_option( 'wpcs_hide_uncat', '1', '', true );
	add_option( 'wpcs_search_button', 'Search', '', true );
	add_option( 'wpcs_search_field', 'Search Here..', '', true );
	add_option( 'wpcs_back_color', '00AAFF', '', true );
	add_option( 'wpcs_button_color', '00AAFF', '', true );
	add_option( 'wpcs_cat_color', '68CEF9', '', true );
	add_option( 'wpcs_text_color', 'ffffff', '', true );
	add_option( 'wpcs_top_head_px', '40', '', true );
	add_option( 'wpcs_sub_head_px', '20', '', true );
	add_option( 'wpcs_thnumbnail_widget', '1', '', true );
	add_option( 'wpcs_title_widget', '1', '', true );
	add_option( 'wpcs_excerpt_length_widget', '100', '', true );
	add_option( 'wpcs_postno_widget', '3', '', true );
}
register_activation_hook(__FILE__, 'wpcs_activation');
add_action( 'admin_menu', 'register_wpcs_menu_page' );
function register_wpcs_menu_page(){

    add_menu_page( 'WPCS', 'WPCS', 'manage_options', 'wpcs-overview', 'wpcs_overview_callback', plugins_url( 'img/wpcs-icon.png', __FILE__ ) );
	add_submenu_page( 'wpcs-overview', 'Settings', 'Settings', 'manage_options', 'wpcs-settings', 'wpcs_settings_callback' );
	
}
add_action( 'wp_enqueue_scripts', 'wpcs_place_scripts' );
function wpcs_place_scripts() {
	 wp_enqueue_style( 'wpcs_stylesheet', plugins_url('css/wp-cs.css',__FILE__), array(), '', false );
	 wp_enqueue_script( 'colorjs', plugins_url('jscolor.js',__FILE__), array(), '', true );
 }
require_once(dirname (__FILE__) . '/shortcode.php');
require_once(dirname (__FILE__) . '/wpcs_widgets.php');
function wpcs_overview_callback() {
	include(dirname (__FILE__) . '/overview.php');
}
require_once(dirname (__FILE__) . '/register_setting.php');
function wpcs_settings_callback() {
	include(dirname (__FILE__) . '/setting.php');
}
{
function wpcs_getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}
function wpcs_setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

// Remove issues with prefetching adding extra views
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0); 
}
function wpcs_setview_counter_footer_function(){
	wpcs_setPostViews(get_the_ID());
}
add_action('wp_footer', 'wpcs_setview_counter_footer_function');
?>
