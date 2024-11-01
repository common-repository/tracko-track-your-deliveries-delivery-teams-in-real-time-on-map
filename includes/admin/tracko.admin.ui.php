<?php
/*!
* WordPress TRACKO
*/

/**
* The LOC in charge of displaying TRACKO Admin GUInterfaces
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// --------------------------------------------------------------------

/**
* Generate tracko admin pages
*
* wp-admin/admin.php?page=wordpress-tracko&..
*/
function tracko_admin_main()
{
	// HOOKABLE:
	do_action( "tracko_admin_main_start" );

	if ( ! current_user_can('manage_options') )
	{
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

	if( ! tracko_check_requirements() )
	{
		tracko_admin_ui_fail();

		exit;
	}

	GLOBAL $TRACKO_ADMIN_TABS;
	GLOBAL $TRACKO_COMPONENTS;
	GLOBAL $TRACKO_PROVIDERS_CONFIG;
	GLOBAL $TRACKO_VERSION;

	if( isset( $_REQUEST["enable"] ) && isset( $TRACKO_COMPONENTS[ $_REQUEST["enable"] ] ) )
	{
		$component = $_REQUEST["enable"];

		$TRACKO_COMPONENTS[ $component ][ "enabled" ] = true;

		update_option( "tracko_components_" . $component . "_enabled", 1 );

		tracko_register_components();
	}

	if( isset( $_REQUEST["disable"] ) && isset( $TRACKO_COMPONENTS[ $_REQUEST["disable"] ] ) )
	{
		$component = $_REQUEST["disable"];

		$TRACKO_COMPONENTS[ $component ][ "enabled" ] = false;

		update_option( "tracko_components_" . $component . "_enabled", 2 );

		tracko_register_components();
	}

	$trackop            = "tracko-plugin-settings";
	$trackodwp          = 0;
	$assets_base_url = TRACKO_PLUGIN_URL . 'assets/img/16x16/';

	if( isset( $_REQUEST["trackop"] ) )
	{
		$trackop = trim( strtolower( strip_tags( $_REQUEST["trackop"] ) ) );
	}

	tracko_admin_ui_header( $trackop );

	if( isset( $TRACKO_ADMIN_TABS[$trackop] ) && $TRACKO_ADMIN_TABS[$trackop]["enabled"] )
	{
		if( isset( $TRACKO_ADMIN_TABS[$trackop]["action"] ) && $TRACKO_ADMIN_TABS[$trackop]["action"] )
		{
			do_action( $TRACKO_ADMIN_TABS[$trackop]["action"] );
		}
		else
		{
			include "components/$trackop/index.php";
		}
	}
	else
	{
		tracko_admin_ui_error();
	}

	tracko_admin_ui_footer();

	// HOOKABLE:
	do_action( "tracko_admin_main_end" );
}


function tracko_admin_main_about()
{
	// HOOKABLE:
	do_action( "tracko_admin_main_start" );

	if ( ! current_user_can('manage_options') )
	{
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

	if( ! tracko_check_requirements() )
	{
		tracko_admin_ui_fail();

		exit;
	}

	GLOBAL $TRACKO_ADMIN_TABS;
	GLOBAL $TRACKO_COMPONENTS;
	GLOBAL $TRACKO_PROVIDERS_CONFIG;
	GLOBAL $TRACKO_VERSION;

	if( isset( $_REQUEST["enable"] ) && isset( $TRACKO_COMPONENTS[ $_REQUEST["enable"] ] ) )
	{
		$component = $_REQUEST["enable"];

		$TRACKO_COMPONENTS[ $component ][ "enabled" ] = true;

		update_option( "tracko_components_" . $component . "_enabled", 1 );

		tracko_register_components();
	}

	if( isset( $_REQUEST["disable"] ) && isset( $TRACKO_COMPONENTS[ $_REQUEST["disable"] ] ) )
	{
		$component = $_REQUEST["disable"];

		$TRACKO_COMPONENTS[ $component ][ "enabled" ] = false;

		update_option( "tracko_components_" . $component . "_enabled", 2 );

		tracko_register_components();
	}

	$trackop            = "tracko-plugin-settings";
	$trackodwp          = 0;
	$assets_base_url = TRACKO_PLUGIN_URL . 'assets/img/16x16/';

	if( isset( $_REQUEST["trackop"] ) )
	{
		$trackop = trim( strtolower( strip_tags( $_REQUEST["trackop"] ) ) );
	}

	tracko_admin_ui_header( $trackop );

	if( isset( $TRACKO_ADMIN_TABS[$trackop] ) && $TRACKO_ADMIN_TABS[$trackop]["enabled"] )
	{
		if( isset( $TRACKO_ADMIN_TABS[$trackop]["action"] ) && $TRACKO_ADMIN_TABS[$trackop]["action"] )
		{
			do_action( $TRACKO_ADMIN_TABS[$trackop]["action"] );
		}
		else
		{
			include "components/$trackop/about.php";
		}
	}
	else
	{
		tracko_admin_ui_error();
	}

	tracko_admin_ui_footer();

	// HOOKABLE:
	do_action( "tracko_admin_main_end" );
}


/**
* Render tracko admin pages header (label and tabs)
*/
function tracko_admin_ui_header( $trackop = null )
{
	// HOOKABLE:
	do_action( "tracko_admin_ui_header_start" );

	GLOBAL $TRACKO_VERSION;
	GLOBAL $TRACKO_ADMIN_TABS;

?>
<!--<a name="trackotop"></a>-->
<div class="tracko-container">

	<?php
		// nag

		if( in_array( $trackop, array( 'networks', 'login-widget' ) ) and ( isset( $_REQUEST['settings-updated'] ) or isset( $_REQUEST['enable'] ) ) )
		{
			$active_plugins = implode('', (array) get_option('active_plugins') );
			$cache_enabled  =
				strpos( $active_plugins, "w3-total-cache"   ) !== false |
				strpos( $active_plugins, "wp-super-cache"   ) !== false |
				strpos( $active_plugins, "quick-cache"      ) !== false |
				strpos( $active_plugins, "wp-fastest-cache" ) !== false |
				strpos( $active_plugins, "wp-widget-cache"  ) !== false |
				strpos( $active_plugins, "hyper-cache"      ) !== false;

			if( $cache_enabled )
			{
				?>
					<div class="fade updated" style="margin: 4px 0 20px;">
						<p>
							<?php _e("<b>Note:</b> TRACKO has detected that you are using a caching plugin. If the saved changes didn't take effect immediately then you might need to empty the cache", 'wordpress-tracko') ?>.
						</p>
					</div>
				<?php
			}
		}

		if( get_option( 'tracko_settings_development_mode_enabled' ) )
		{
	?>
				<div class="fade error tracko-error-dev-mode-on" style="margin: 4px 0 20px;">
					<p>
						<?php _e('<b>Warning:</b> You are now running TRACKO with DEVELOPMENT MODE enabled. This mode is not intend for live websites as it might raise serious security risks', 'wordpress-tracko') ?>.
					</p>
					<p>
						<a class="button-secondary" href="admin.php?page=wordpress-tracko&trackop=tools#dev-mode"><?php _e('Change this mode', 'wordpress-tracko') ?></a>
						<a class="button-secondary" href="troubleshooting-advanced.html" target="_blank"><?php _e('Read about the development mode', 'wordpress-tracko') ?></a>
					</p>
				</div>
			<?php
		}

		if( get_option( 'tracko_settings_debug_mode_enabled' ) )
		{
			?>
				<div class="fade updated tracko-error-debug-mode-on" style="margin: 4px 0 20px;">
					<p>
						<?php _e('<b>Note:</b> You are now running TRACKO with DEBUG MODE enabled. This mode is not intend for live websites as it might add to loading time and store unnecessary data on your server', 'wordpress-tracko') ?>.
					</p>
					<p>
						<a class="button-secondary" href="admin.php?page=wordpress-tracko&trackop=tools#debug-mode"><?php _e('Change this mode', 'wordpress-tracko') ?></a>
						<a class="button-secondary" href="admin.php?page=wordpress-tracko&trackop=watchdog"><?php _e('View TRACKO logs', 'wordpress-tracko') ?></a>
						<a class="button-secondary" href="troubleshooting-advanced.html" target="_blank"><?php _e('Read about the debug mode', 'wordpress-tracko') ?></a>
					</p>
				</div>
			<?php
		}
	?>
	<div id="tracko_admin_tab_content">
<?php
	// HOOKABLE:
	do_action( "tracko_admin_ui_header_end" );
}

// --------------------------------------------------------------------

/**
* Renders tracko admin pages footer
*/
function tracko_admin_ui_footer()
{
	// HOOKABLE:
	do_action( "tracko_admin_ui_footer_start" );

	GLOBAL $TRACKO_VERSION;
?>
	</div> <!-- ./tracko_admin_tab_content -->

<div class="clear"></div>

<?php
	tracko_admin_help_us_localize_note();

	// HOOKABLE:
	do_action( "tracko_admin_ui_footer_end" );

	if( get_option( 'tracko_settings_development_mode_enabled' ) )
	{
		tracko_display_dev_mode_debugging_area();
 	}
}

// --------------------------------------------------------------------

/**
* Renders tracko admin error page
*/
function tracko_admin_ui_error()
{
	// HOOKABLE:
	do_action( "tracko_admin_ui_error_start" );
?>
<div id="tracko_div_warn">
	<h3 style="margin:0px;"><?php _e('Oops! We ran into an issue.', 'wordpress-tracko') ?></h3>
	<hr />
	<p>
		<?php _e('Unknown or Disabled <b>Component</b>! Check the list of enabled components or the typed URL', 'wordpress-tracko') ?> .
	</p>
	<p>
		<?php _e("If you believe you've found a problem with <b>WordPress TRACKO</b>, be sure to let us know so we can fix it", 'wordpress-tracko') ?>.
	</p>

	<hr />
	<div>
		<a class="button-secondary" href="support.html" target="_blank"><?php _e( "Report as bug", 'wordpress-tracko' ) ?></a>
		<a class="button-primary" href="admin.php?page=wordpress-tracko&trackop=components" style="float:<?php if( is_rtl() ) echo 'left'; else echo 'right'; ?>"><?php _e( "Check enabled components", 'wordpress-tracko' ) ?></a>
	</div>
</div>
<?php
	// HOOKABLE:
	do_action( "tracko_admin_ui_error_end" );
}

// --------------------------------------------------------------------

/**
* Renders TRACKO #FAIL page
*/
function tracko_admin_ui_fail()
{
	// HOOKABLE:
	do_action( "tracko_admin_ui_fail_start" );
?>
<div class="tracko-container">
		<div style="background: none repeat scroll 0 0 #fff;border: 1px solid #e5e5e5;box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);padding:20px;">
			<h1><?php _e("TRACKO - FAIL!", 'wordpress-tracko') ?></h1>

			<hr />

			<p>
				<?php _e('Despite the efforts, put into <b>WordPress TRACKO</b> in terms of reliability, portability, and maintenance by the plugin <a href="http://profiles.wordpress.org/" target="_blank">author</a> and <a href="https://github.com/hybridauth/WordPress-Social-Login/graphs/contributors" target="_blank">contributors</a>', 'wordpress-tracko') ?>.
				<b style="color:red;"><?php _e('Your server failed the requirements check for this plugin', 'wordpress-tracko') ?>:</b>
			</p>

			<p>
				<?php _e('These requirements are usually met by default by most "modern" web hosting providers, however some complications may occur with <b>shared hosting</b> and, or <b>custom wordpress installations</b>', 'wordpress-tracko') ?>.
			</p>

			<p>
				<?php _e("The minimum server requirements are", 'wordpress-tracko') ?>:
			</p>

			<ul style="margin-left:60px;">
				<li><?php _e("PHP >= 5.2.0 installed", 'wordpress-tracko') ?></li>
				<li><?php _e("TRACKO Endpoint URLs reachable", 'wordpress-tracko') ?></li>
				<li><?php _e("PHP's default SESSION handling", 'wordpress-tracko') ?></li>
				<li><?php _e("PHP/CURL/SSL Extension enabled", 'wordpress-tracko') ?></li>
				<li><?php _e("PHP/JSON Extension enabled", 'wordpress-tracko') ?></li>
				<li><?php _e("PHP/REGISTER_GLOBALS Off", 'wordpress-tracko') ?></li>
				<li><?php _e("jQuery installed on WordPress backoffice", 'wordpress-tracko') ?></li>
			</ul>
		</div>

</div>
<style>.tracko-container .button-secondary { display:none; }</style>
<?php
	// HOOKABLE:
	do_action( "tracko_admin_ui_fail_end" );
}

// --------------------------------------------------------------------

/**
* Renders tracko admin welcome panel
*/
function tracko_admin_welcome_panel()
{
	if( isset( $_REQUEST["trackodwp"] ) && (int) $_REQUEST["trackodwp"] )
	{
		$trackodwp = (int) $_REQUEST["trackodwp"];
		update_option( "tracko_settings_welcome_panel_enabled", tracko_get_version() );
		return;
	}

	// if new user or tracko updated, then we display tracko welcome panel
	if( get_option( 'tracko_settings_welcome_panel_enabled' ) == tracko_get_version() )
	{
		return;
	}

	$trackop = "networks";
	if( isset( $_REQUEST["trackop"] ) )
	{
		$trackop = $_REQUEST["trackop"];
	}

}

// --------------------------------------------------------------------

/**
* Renders tracko localization note
*/
function tracko_admin_help_us_localize_note()
{
	return; // nothing, until I decide otherwise..

	$assets_url = TRACKO_PLUGIN_URL . 'assets/img/';

	?>
		<div id="l10n-footer">
			<br /><br />
			<img src="<?php echo $assets_url ?>flags.png">
			<a href="https://www.transifex.com/projects/p/wordpress-tracko/" target="_blank"><?php _e( "Help us translate TRACKO into your language", 'wordpress-tracko' ) ?></a>
		</div>
	<?php
}

// --------------------------------------------------------------------

/**
* Renders an editor in a page in the typical fashion used in Posts and Pages.
* wp_editor was implemented in wp 3.3. if not found we fallback to a regular textarea
*
* Utility.
*/
function tracko_render_wp_editor( $name, $content )
{
	if( ! function_exists( 'wp_editor' ) )
	{
		?>
			<textarea style="width:100%;height:100px;margin-top:6px;" name="<?php echo $name ?>"><?php echo htmlentities( $content ); ?></textarea>
		<?php
		return;
	}
	?>
	<div class="postbox">
		<div class="wp-editor-textarea" style="background-color: #FFFFFF;">
			<?php
				wp_editor(
					$content, $name,
					array( 'textarea_name' => $name, 'media_buttons' => true, 'tinymce' => array( 'theme_advanced_buttons1' => 'formatselect,forecolor,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink' ) )
				);
			?>
		</div>
	</div>
	<?php
}

// --------------------------------------------------------------------

/**
* Display TRACKO on settings as submenu
*/
function tracko_admin_menu()
{	
	$assets_url = TRACKO_PLUGIN_URL . 'assets/img/';
	add_menu_page( 'TRACKO', 'Tracko', 'manage_options', 'wordpress-tracko', 'tracko_admin_main', $assets_url. 'menu-icon.png', 58.9 );
	add_submenu_page( 'wordpress-tracko','Settings','Settings','manage_options','wordpress-tracko','tracko_admin_main',$assets_url. 'menu-icon.png',58.9);
	add_submenu_page( 'wordpress-tracko','About Tracko','About Tracko','manage_options','wordpress-about-tracko','tracko_admin_main_about',$assets_url. 'menu-icon.png',58.9);
	add_action( 'admin_init', 'tracko_register_setting' );
}

add_action('admin_menu', 'tracko_admin_menu' );

// --------------------------------------------------------------------

/**
* Enqueue TRACKO admin CSS file
*/
function tracko_add_admin_stylesheets($hooks)
{
	if( ! wp_style_is( 'tracko-admin', 'registered' ) )
	{
		wp_register_style( "tracko-admin", TRACKO_PLUGIN_URL . "assets/css/admin.css" );
		
	}
	
	wp_enqueue_style( "tracko-admin" );
	
	if($_GET['page']=='wordpress-tracko')
	{
		if (is_admin())
			wp_enqueue_media ();
	
		if( ! wp_style_is( 'tracko-admin-css1', 'registered' ) )
			wp_register_style( "tracko-admin-css1", TRACKO_PLUGIN_URL . "assets/form/css/template-blue.css" );
		wp_enqueue_style( "tracko-admin-css1" );
		
		if( ! wp_style_is( 'tracko-admin-css2', 'registered' ) )
			wp_register_style( "tracko-admin-css2", TRACKO_PLUGIN_URL . "assets/form/css/landing.css" );
		wp_enqueue_style( "tracko-admin-css2" );
		
		if( ! wp_style_is( 'tracko-admin-css3', 'registered' ) )
			wp_register_style( "tracko-admin-css3", TRACKO_PLUGIN_URL . "assets/form/css/style.css" );
		
		wp_enqueue_style( "tracko-admin-css3" );
	}
	
	if($_GET['page']=='wordpress-tracko-app-link' || $_GET['page']=='wordpress-about-tracko' || $_GET['page']=='wordpress-tracko-plugin-supported' || $_GET['page']=='wordpress-api-id' )
	{
		if( ! wp_style_is( 'tracko-admin-css1', 'registered' ) )
			wp_register_style( "tracko-admin-css1", TRACKO_PLUGIN_URL . "assets/form/css/template-blue.css" );
		
		wp_enqueue_style( "tracko-admin-css1" );
		
		if( ! wp_style_is( 'tracko-admin-css2', 'registered' ) )
			wp_register_style( "tracko-admin-css2", TRACKO_PLUGIN_URL . "assets/form/css/landing.css" );
		
		wp_enqueue_style( "tracko-admin-css2" );
		
		if( ! wp_style_is( 'tracko-admin-css3', 'registered' ) )
			wp_register_style( "tracko-admin-css3", TRACKO_PLUGIN_URL . "assets/form/css/style.css" );
		
		wp_enqueue_style( "tracko-admin-css3" );
		
	}
}
add_action( 'admin_enqueue_scripts', 'tracko_add_admin_stylesheets' );

/**
* Enqueue TRACKO admin JS file
*/
function tracko_add_admin_footer_script() {
	
	if($_GET['page']=='wordpress-tracko')
	{
		wp_enqueue_script( 'tracko-script', TRACKO_PLUGIN_URL . 'assets/js/tracko-script.js' );
	}
	
}
add_action('admin_footer', 'tracko_add_admin_footer_script');
