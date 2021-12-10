<?php
/**
 * Plugin Name: WPQA - The WordPress Questions And Answers Plugin
 * Plugin URI: https://2code.info/plugins/WPQA/
 * Description: Question and answer plugin with point and badges system.
 * Version: 2.9.3
 * Author: 2code
 * Author URI: https://2code.info/
 * License: GPL2
 *
 * Text Domain: wpqa
 * Domain Path: /languages/
 */


// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;

/* Defines */
define("wpqa_options","discy_options");
define("wpqa_meta","discy");
define("wpqa_terms","discy");
if (!defined("prefix_meta")) {
	define("prefix_meta",wpqa_meta."_");
}
if (!defined("prefix_terms")) {
	define("prefix_terms",wpqa_terms."_");
}

/* Class */
register_activation_hook(__FILE__,array('WPQA','activate'));
register_activation_hook(__FILE__,array('WPQA','deactivate'));

/* Load plugin textdomain */
function wpqa_load_textdomain() {
    load_plugin_textdomain('wpqa',false,dirname(plugin_basename(__FILE__)).'/languages/');
}
add_action('plugins_loaded','wpqa_load_textdomain');

/* Load the core */
require_once plugin_dir_path(__FILE__).'includes/class-wpqa.php';