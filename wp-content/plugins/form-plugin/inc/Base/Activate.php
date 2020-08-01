<?php
/**
 * @package  brandhPlugin
 */
namespace Inc\Base;

class Activate
{
	public static function activate() {
		flush_rewrite_rules();

		$default = array();

		if ( ! get_option( 'brandh_plugin' ) ) {
			update_option( 'brandh_plugin', $default );
		}

		if ( ! get_option( 'brandh_plugin_cpt' ) ) {
			update_option( 'brandh_plugin_cpt', $default );
		}

		if ( ! get_option( 'brandh_plugin_tax' ) ) {
			update_option( 'brandh_plugin_tax', $default );
		}
	}
}