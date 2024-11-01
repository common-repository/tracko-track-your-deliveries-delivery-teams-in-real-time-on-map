<?php
/*
Plugin Name: Tracko – Track Your Deliveries & Delivery Teams in Real Time on Map 
Plugin URI: http://tracko.link/
Description: Tracko woo commerce plugin provides woo commerce sites the technology to track their delivery teams in real time on map. It empowers your delivery teams to receive orders as per location and navigate the location on map.
Version: 1.0.1
Author: Tracko
Author URI: https://www.twistfuture.com/
License: MIT License
Text Domain: Tracko
Domain Path: /languages
*/

// Exit if accessed directly TRACKO
if( !defined( 'ABSPATH' ) ) exit;

// --------------------------------------------------------------------

global $TRACKO_VERSION;
global $TRACKO_PROVIDERS_CONFIG;
global $TRACKO_COMPONENTS;
global $TRACKO_ADMIN_TABS;

$TRACKO_VERSION = "1.0.0";

/**
* This file might be used to :
*     1. Redefine TRACKO constants, so you can move TRACKO folder around.
*     2. Define TRACKO Pluggable PHP Functions. See http://tracko.link/features.php
*     5. Implement your TRACKO hooks.
*/
if( file_exists( WP_PLUGIN_DIR . '/wp-tracko-custom.php' ) )
{
	include_once( WP_PLUGIN_DIR . '/wp-tracko-custom.php' );
}


/**
* Define TRACKO constants, if not already defined
*/
defined( 'TRACKO_ABS_PATH' ) || define( 'TRACKO_ABS_PATH', plugin_dir_path( __FILE__ ) );
defined( 'TRACKO_PLUGIN_URL' ) || define( 'TRACKO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
/* 
includes 
*/
require_once( TRACKO_ABS_PATH . 'class-tracko.php' );

# TRACKO Setup & Settings
require_once( TRACKO_ABS_PATH . 'includes/settings/tracko.providers.php'); 			// List of supported providers (mostly provided by hybridauth library)
require_once( TRACKO_ABS_PATH . 'includes/settings/tracko.database.php'); 			// Install/Uninstall TRACKO database tables
require_once( TRACKO_ABS_PATH . 'includes/settings/tracko.initialization.php'); 	// Check TRACKO requirements and register TRACKO settings
require_once( TRACKO_ABS_PATH . 'includes/settings/tracko.compatibilities.php'); 	// Check and upgrade TRACKO database/settings (for older versions)

# TRACKO Admin interfaces
if( is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) )
{
	require_once( TRACKO_ABS_PATH . 'includes/admin/tracko.admin.ui.php'); // The entry point to TRACKO Admin interfaces
}


// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'tracko_main_class', 'tracko_plugin_activate' ) );
//Code For Deactivation 
register_deactivation_hook( __FILE__, array( 'tracko_main_class', 'tracko_plugin_deactivate_plugin' ) );
tracko_main_class::get_instance();

//added for redirect after plugin activation.
register_activation_hook(__FILE__, 'tracko_app_plugin_activate');
add_action('admin_init', 'tracko_app_plugin_redirect');


function tracko_app_plugin_activate() {
    add_option('tracko_app_plugin_do_activation_redirect', true);
	
}
// Solution 1
function tracko_app_plugin_redirect() {
    if (get_option('tracko_app_plugin_do_activation_redirect', false)) {
        delete_option('tracko_app_plugin_do_activation_redirect');
        wp_redirect('admin.php?page=wordpress-tracko');
	exit;
    }
}

/**
* Attempt to install/migrate/repair TRACKO upon activation
*
* Create tracko tables
* Migrate old versions
* Register default components
*/
function tracko_install()
{
	tracko_database_install();

	tracko_update_compatibilities();

	tracko_register_components();
}

register_activation_hook( __FILE__, 'tracko_install' );

/**
* Add a settings to plugin_action_links
*/
function tracko_add_plugin_action_links( $links, $file )
{
	static $this_plugin;

	if( ! $this_plugin )
	{
		$this_plugin = plugin_basename( __FILE__ );
	}

	if( $file == $this_plugin )
	{
		$tracko_links  = '<a href="admin.php?page=wordpress-tracko">' . __( "Settings" ) . '</a>';

		array_unshift( $links, $tracko_links );
	}

	return $links;
}

add_filter( 'plugin_action_links', 'tracko_add_plugin_action_links', 10, 2 );

/**
* Add faq and user guide links to plugin_row_meta
*/
function tracko_add_plugin_row_meta( $links, $file )
{
	static $this_plugin;

	if( ! $this_plugin )
	{
		$this_plugin = plugin_basename( __FILE__ );
	}
	
	if( $file == $this_plugin )
	{
		
		$links[2] = '<a href="http://tracko.link/">'.__( "View details" , 'wordpress-tracko' ).'</a>';
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'tracko_add_plugin_row_meta', 10, 2 );

/**
* Loads the plugin's translated strings.
*
* http://codex.wordpress.org/Function_Reference/load_plugin_textdomain
*/
if( ! function_exists( 'tracko_load_plugin_textdomain' ) )
{
	function tracko_load_plugin_textdomain()
	{
		load_plugin_textdomain( 'wordpress-tracko', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

add_action( 'plugins_loaded', 'tracko_load_plugin_textdomain' );

/**
* Return the current used TRACKO version
*/
if( ! function_exists( 'tracko_get_version' ) )
{
	function tracko_get_version()
	{
		global $TRACKO_VERSION;
		return $TRACKO_VERSION;
	}
}
// --------------------------------------------------------------------
