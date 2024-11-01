<?php
if ( ! function_exists( 'get_plugin_data' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
$this_plugin    = get_plugin_data( SLP_EXT_FILE, false, false );
$min_wp_version = '5.3';

if ( ! defined( 'SLP_EXT_VERSION' ) ) ( define( 'SLP_EXT_VERSION', $this_plugin['Version'] ) );

if ( ! defined( 'SLPLUS_PLUGINDIR' ) ) {
	add_action(
		'admin_notices',
		create_function(
			'',
			"echo '<div class=\"error\"><p>" .
			sprintf(
				__( '%s requires Store Locator Plus to function properly. ', 'slp-extenders' ),
				$this_plugin['Name']
			) . '<br/>' .
			__( 'This plugin has been deactivated.', 'slp-extenders' ) .
			__( 'Please install Store Locator Plus.', 'slp-extenders' ) .
			"</p></div>';"
		)
	);
	deactivate_plugins( plugin_basename( SLP_EXT_FILE ) );

	return;
}

global $wp_version;
if ( version_compare( $wp_version, $min_wp_version, '<' ) ) {
	add_action(
		'admin_notices',
		create_function(
			'',
			"echo '<div class=\"error\"><p>" .
			sprintf(
				__( '%s requires WordPress %s to function properly. ', 'slp-extenders' ),
				$this_plugin['Name'],
				$min_wp_version
			) .
			__( 'This plugin has been deactivated.', 'slp-extenders' ) .
			__( 'Please upgrade WordPress.', 'slp-extenders' ) .
			"</p></div>';"
		)
	);
	deactivate_plugins( plugin_basename( SLP_EXT_FILE ) );

	return;
}

// Go forth and sprout your tentacles...
// Get some Store Locator Plus sauce.
//
require_once( SLP_EXT_REL_DIR . 'include/SLP_Extenders.php' );
//error_log( ' LOADER for SLP_Extenders::init for SLP_EXT_FILE = ' . SLP_EXT_FILE );
SLP_Extenders::init();
