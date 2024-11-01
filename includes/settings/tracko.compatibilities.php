<?php
/*!
* WordPress TRACKO
*
*/

/**
* Check and upgrade compatibilities from old TRACKO versions
*
* Here we attempt to:
*	- set to default all settings when TRACKO is installed
*	- make TRACKO compatible when updating from older versions, by registering new options
*
* Side note: Over time, the number of options have become too long, and as you can notice
*            things are not optimal. If you have any better idea on how to tackle this issue,
*            please don't hesitate to share it.
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// --------------------------------------------------------------------

/**
* Check and upgrade compatibilities from old TRACKO versions
*/
function tracko_update_compatibilities()
{

	
	delete_option( 'tracko_settings_development_mode_enabled' );
	delete_option( 'tracko_settings_debug_mode_enabled' );

	update_option( 'tracko_settings_welcome_panel_enabled', 1 );

	if( ! get_option( 'tracko_settings_redirect_url' ) )
	{
		update_option( 'tracko_settings_redirect_url', home_url() );
	}

	if( ! get_option( 'tracko_settings_force_redirect_url' ) )
	{
		update_option( 'tracko_settings_force_redirect_url', 2 );
	}

	if( ! get_option( 'tracko_settings_connect_with_label' ) )
	{
		update_option( 'tracko_settings_connect_with_label', __("Connect with:", 'wordpress-tracko') );
	}

	if( ! get_option( 'tracko_settings_users_avatars' ) )
	{
		update_option( 'tracko_settings_users_avatars', 1 );
	}

	if( ! get_option( 'tracko_settings_use_popup' ) )
	{
		update_option( 'tracko_settings_use_popup', 2 );
	}

	if( ! get_option( 'tracko_settings_widget_display' ) )
	{
		update_option( 'tracko_settings_widget_display', 1 );
	}

	if( ! get_option( 'tracko_settings_authentication_widget_css' ) )
	{
		update_option( 'tracko_settings_authentication_widget_css', ".wp-tracko-connect-with {}\n.wp-tracko-provider-list {}\n.wp-tracko-provider-list a {}\n.wp-tracko-provider-list img {}\n.tracko_connect_with_provider {}" );
	}

	# if no idp is enabled then we enable the default providers (facebook, google, twitter)
	global $TRACKO_PROVIDERS_CONFIG;
	$nok = true;
	foreach( $TRACKO_PROVIDERS_CONFIG AS $item )
	{
		$provider_id = $item["provider_id"];

		if( get_option( 'tracko_settings_' . $provider_id . '_enabled' ) )
		{
			$nok = false;
		}
	}

	if( $nok )
	{
		foreach( $TRACKO_PROVIDERS_CONFIG AS $item )
		{
			$provider_id = $item["provider_id"];

			if( isset( $item["default_network"] ) && $item["default_network"] ){
				update_option( 'tracko_settings_' . $provider_id . '_enabled', 1 );
			}
		}
	}

	global $wpdb;

	# migrate steam users id to id64. Prior to 2.2
	$sql = "UPDATE {$wpdb->prefix}trackousersprofiles
		SET identifier = REPLACE( identifier, 'http://steamcommunity.com/openid/id/', '' )
		WHERE provider = 'Steam' AND identifier like 'http://steamcommunity.com/openid/id/%' ";
	$wpdb->query( $sql );
}

// --------------------------------------------------------------------

/**
* Old junk
*
* Seems like some people are using TRACKO _internal_ functions for some reason...
*
* Here we keep few of those old/depreciated/undocumented/internal functions, so their websites
* doesn't break when updating to newer versions.
*
* TO BE REMOVED AS OF TRACKO 3.0
**
* Ref: developer-api-migrating-2.2.html
*/

// 2.1.6
function tracko_render_login_form(){ tracko_deprecated_function( __FUNCTION__, '2.2.3' ); return tracko_render_auth_widget(); }
function tracko_render_comment_form(){ tracko_deprecated_function( __FUNCTION__, '2.2.3' ); tracko_action_wordpress_tracko_login(); }
function tracko_render_login_form_login_form(){ tracko_deprecated_function( __FUNCTION__, '2.2.3' ); tracko_action_wordpress_tracko_login(); }
function tracko_render_login_form_login_on_register_and_login(){ tracko_deprecated_function( __FUNCTION__, '2.2.3' ); tracko_action_wordpress_tracko_login(); }
function tracko_render_login_form_login(){ tracko_deprecated_function( __FUNCTION__, '2.2.3' ); tracko_action_wordpress_tracko_login(); }
function tracko_shortcode_handler(){ tracko_deprecated_function( __FUNCTION__, '2.2.3' ); return tracko_shortcode_wordpress_tracko_login(); }

// 2.2.2
function tracko_render_tracko_widget_in_comment_form(){ tracko_deprecated_function( __FUNCTION__, '2.2.3' ); tracko_action_wordpress_tracko_login(); }
function tracko_render_tracko_widget_in_wp_login_form(){ tracko_deprecated_function( __FUNCTION__, '2.2.3' ); tracko_action_wordpress_tracko_login(); }
function tracko_render_tracko_widget_in_wp_register_form(){ tracko_deprecated_function( __FUNCTION__, '2.2.3' ); tracko_action_wordpress_tracko_login(); }
function tracko_user_custom_avatar($avatar, $mixed, $size, $default, $alt){ tracko_deprecated_function( __FUNCTION__, '2.2.3' ); return tracko_get_wp_user_custom_avatar($html, $mixed, $size, $default, $alt); }
function tracko_bp_user_custom_avatar($html, $args){ tracko_deprecated_function( __FUNCTION__, '2.2.3' ); return tracko_get_bp_user_custom_avatar($html, $args); }

// nag about it
function tracko_deprecated_function( $function, $version )
{
	// user should be admin and logged in
	if( current_user_can('manage_options') )
	{
		trigger_error( sprintf( __('%1$s is <strong>deprecated</strong> since TRACKO %2$s! For more information, check TRACKO Developer API - Migration.'), $function, $version ), E_USER_NOTICE );
	}
}

?>