<?php
/*!
* WordPress TRACKO
*

*/

/**
* Check TRACKO requirements and register TRACKO settings 
*/

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// --------------------------------------------------------------------

/**
* Check TRACKO minimum requirements. Display fail page if they are not met.
*
* This function will only test the strict minimal
*/
function tracko_check_requirements()
{
	if (! version_compare( PHP_VERSION, '5.2.0', '>=' ))
	{
		return false;
	}
	return true;
}

// --------------------------------------------------------------------

/** list of TRACKO components */
$TRACKO_COMPONENTS = ARRAY(
	"tracko-plugin-settings"=> array( "type" => "core","label" => __("TRACKO Plugin Settings",'wordpress-tracko'),"description" => __("TRACKO Plugin Settings.",'wordpress-tracko') )
);

/** list of TRACKO admin tabs */
$TRACKO_ADMIN_TABS = ARRAY(  
	"tracko-plugin-settings" => 
		array( 
		"label" => __("TRACKO Plugin Settings",'wordpress-tracko'), 
		"visible" => true  , 
		"component" => "tracko-plugin-settings", 
		"default" => true 
		)
);

// --------------------------------------------------------------------

/**
* Register a new TRACKO component 
*/
function tracko_register_component( $component, $label, $description, $version, $author, $author_url, $component_url )
{
	GLOBAL $TRACKO_COMPONENTS;

	$config = array();

	$config["type"]          = "addon"; // < force to addon
	$config["label"]         = $label;
	$config["description"]   = $description;
	$config["version"]       = $version;
	$config["author"]        = $author;
	$config["author_url"]    = $author_url;
	$config["component_url"] = $component_url;

	$TRACKO_COMPONENTS[ $component ] = $config;
}

// --------------------------------------------------------------------

/**
* Register new TRACKO admin tab
*/
function tracko_register_admin_tab( $component, $tab, $label, $action, $visible = false, $pull_right = false ) 
{ 
	GLOBAL $TRACKO_ADMIN_TABS;

	$config = array();

	$config["component"]  = $component;
	$config["label"]      = $label;
	$config["visible"]    = $visible;
	$config["action"]     = $action;
	$config["pull_right"] = $pull_right;

	$TRACKO_ADMIN_TABS[ $tab ] = $config;
}

// --------------------------------------------------------------------

/**
* Check if a component is enabled
*/
function tracko_is_component_enabled( $component )
{ 
	if( get_option( "tracko_components_" . $component . "_enabled" ) == 1 )
	{
		return true;
	}

	return false;
}

// --------------------------------------------------------------------

/**
* Register TRACKO components (Bulk action)
*/
function tracko_register_components()
{
	GLOBAL $TRACKO_COMPONENTS;
	GLOBAL $TRACKO_ADMIN_TABS;

	// HOOKABLE:
	do_action( 'tracko_register_components' );

	foreach( $TRACKO_ADMIN_TABS as $tab => $config )
	{
		$TRACKO_ADMIN_TABS[ $tab ][ "enabled" ] = false; 
	}

	foreach( $TRACKO_COMPONENTS as $component => $config )
	{
		$TRACKO_COMPONENTS[ $component ][ "enabled" ] = false;

		$is_component_enabled = get_option( "tracko_components_" . $component . "_enabled" );
		
		if( $is_component_enabled == 1 )
		{
			$TRACKO_COMPONENTS[ $component ][ "enabled" ] = true;
		}

		if( $TRACKO_COMPONENTS[ $component ][ "type" ] == "core" )
		{
			$TRACKO_COMPONENTS[ $component ][ "enabled" ] = true;

			if( $is_component_enabled != 1 )
			{
				update_option( "tracko_components_" . $component . "_enabled", 1 );
			}
		}
	}

	foreach( $TRACKO_ADMIN_TABS as $tab => $config )
	{
		$component = $config[ "component" ] ;

		if( $TRACKO_COMPONENTS[ $component ][ "enabled" ] )
		{
			$TRACKO_ADMIN_TABS[ $tab ][ "enabled" ] = true;
		}
	}
}

// --------------------------------------------------------------------

/**
* Register TRACKO core settings ( options; components )
*/
function tracko_register_setting()
{
	GLOBAL $TRACKO_PROVIDERS_CONFIG;
	GLOBAL $TRACKO_COMPONENTS;
	GLOBAL $TRACKO_ADMIN_TABS;

	// HOOKABLE:
	do_action( 'tracko_register_setting' );

	tracko_register_components();

	// idps credentials
	foreach( $TRACKO_PROVIDERS_CONFIG AS $item )
	{
		$provider_id          = isset( $item["provider_id"]       ) ? $item["provider_id"]       : null;
		$require_client_id    = isset( $item["require_client_id"] ) ? $item["require_client_id"] : null;
		$require_registration = isset( $item["new_app_link"]      ) ? $item["new_app_link"]      : null;
		$default_api_scope    = isset( $item["default_api_scope"] ) ? $item["default_api_scope"] : null;

		/**
		* @fixme
		*
		* Here we should only register enabled providers settings. postponed. patches are welcome.
		***
			$default_network = isset( $item["default_network"] ) ? $item["default_network"] : null;

			if( ! $default_network || get_option( 'tracko_settings_' . $provider_id . '_enabled' ) != 1 .. )
			{
				..
			}
		*/

		register_setting( 'tracko-settings-group', 'tracko_settings_' . $provider_id . '_enabled' );

		// require application?
		if( $require_registration )
		{
			// api key or id ?
			if( $require_client_id )
			{
				register_setting( 'tracko-settings-group', 'tracko_settings_' . $provider_id . '_app_id' ); 
			}
			else
			{
				register_setting( 'tracko-settings-group', 'tracko_settings_' . $provider_id . '_app_key' ); 
			}

			// api secret
			register_setting( 'tracko-settings-group', 'tracko_settings_' . $provider_id . '_app_secret' ); 

			// api scope?
			if( $default_api_scope )
			{
				if( ! get_option( 'tracko_settings_' . $provider_id . '_app_scope' ) )
				{
					update_option( 'tracko_settings_' . $provider_id . '_app_scope', $default_api_scope );
				}

				register_setting( 'tracko-settings-group', 'tracko_settings_' . $provider_id . '_app_scope' );
			}
		}
	}

	register_setting( 'tracko-settings-group-customize'        , 'tracko_settings_connect_with_label'                               ); 
	register_setting( 'tracko-settings-group-customize'        , 'tracko_settings_social_icon_set'                                  ); 
	register_setting( 'tracko-settings-group-customize'        , 'tracko_settings_users_avatars'                                    ); 
	register_setting( 'tracko-settings-group-customize'        , 'tracko_settings_use_popup'                                        ); 
	register_setting( 'tracko-settings-group-customize'        , 'tracko_settings_widget_display'                                   ); 
	register_setting( 'tracko-settings-group-customize'        , 'tracko_settings_redirect_url'                                     ); 
	register_setting( 'tracko-settings-group-customize'        , 'tracko_settings_force_redirect_url'                               ); 
	register_setting( 'tracko-settings-group-customize'        , 'tracko_settings_users_notification'                               ); 
	register_setting( 'tracko-settings-group-customize'        , 'tracko_settings_authentication_widget_css'                        ); 

	register_setting( 'tracko-settings-group-contacts-import'  , 'tracko_settings_contacts_import_facebook'                         ); 
	register_setting( 'tracko-settings-group-contacts-import'  , 'tracko_settings_contacts_import_google'                           ); 
	register_setting( 'tracko-settings-group-contacts-import'  , 'tracko_settings_contacts_import_twitter'                          ); 
	register_setting( 'tracko-settings-group-contacts-import'  , 'tracko_settings_contacts_import_linkedin'                         ); 
	register_setting( 'tracko-settings-group-contacts-import'  , 'tracko_settings_contacts_import_live'                             ); 
	register_setting( 'tracko-settings-group-contacts-import'  , 'tracko_settings_contacts_import_vkontakte'                        ); 

	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_registration_enabled'                     ); 
	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_authentication_enabled'                   ); 

	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_accounts_linking_enabled'                 );

	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_profile_completion_require_email'         );
	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_profile_completion_change_username'       );
	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_profile_completion_hook_extra_fields'     );

	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_new_users_moderation_level'               );
	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_new_users_membership_default_role'        );

	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_new_users_restrict_domain_enabled'        );
	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_new_users_restrict_domain_list'           );
	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_new_users_restrict_domain_text_bounce'    );
	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_new_users_restrict_email_enabled'         );
	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_new_users_restrict_email_list'            );
	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_new_users_restrict_email_text_bounce'     );
	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_new_users_restrict_profile_enabled'       );
	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_new_users_restrict_profile_list'          );
	register_setting( 'tracko-settings-group-bouncer'          , 'tracko_settings_bouncer_new_users_restrict_profile_text_bounce'   );

	register_setting( 'tracko-settings-group-buddypress'       , 'tracko_settings_buddypress_enable_mapping' ); 
	register_setting( 'tracko-settings-group-buddypress'       , 'tracko_settings_buddypress_xprofile_map' ); 

	register_setting( 'tracko-settings-group-debug'            , 'tracko_settings_debug_mode_enabled' ); 
	register_setting( 'tracko-settings-group-development'      , 'tracko_settings_development_mode_enabled' ); 
}

// --------------------------------------------------------------------
