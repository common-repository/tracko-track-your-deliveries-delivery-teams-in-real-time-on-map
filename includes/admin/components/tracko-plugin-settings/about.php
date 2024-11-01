<?php
/*!
* WordPress TRACKO
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit; 

function tracko_component_tracko_plugin_settings()
{
	
	tracko_admin_welcome_panel();
	
	$assets_setup_base_url = TRACKO_PLUGIN_URL . 'assets/img/';
	$options_woo_setting = get_option( 'tracko_woo_settings_data' );
	$options_woo_setting = maybe_unserialize( $options_woo_setting );
	
	$options_store_setting = get_option( 'tracko_store_settings_data' );
	$options_store_setting = maybe_unserialize( $options_store_setting );
	
	$options_app_setting= get_option( 'tracko_app_settings_data' );
	$options_app_setting = maybe_unserialize( $options_app_setting );
	
?>

<div class="devsite-wrapper">
	<div class="devsite-top-section-wrapper">
		<?php include('header.php'); ?>
	   
		<div class="devsite-main-content clearfix about-devsite" style="margin-top: 0px;">
			<section class="devsite-landing-row devsite-landing-row-1-up">
				<header class="devsite-landing-row-header">
					<div class="devsite-landing-row-header-text">
						<h2 id="firebase-by-platform">About TRACKO</h2>
					</div>
				</header>
			</section>
			
			<section class="devsite-landing-row devsite-landing-row-3-up devsite-landing-row-light-grey firebase-hp-rowgroup gmp-icons-container1 gmp-icons-container-grayscale">	
				<div class="devsite-landing-row-group">
					<div class="card1"></div>
					<div class="card">
						<div class="col-md-6 text col-sm-6">
							<div class="left_icon"><i class="fa fa-map" aria-hidden="true"></i></div>
							<div class="right_icon">
								<h4>All Tasks on map</h4>
								<p>Simple &amp; attractive interface to see the assigned task on map and listing.
									<br>
								</p>
							</div>
							<div class="left_icon"> <i class="fa fa-road" aria-hidden="true"></i>
							</div>
							<div class="right_icon">
								<h4></h4>
								<p>See road map already prepared for the agent as per his proximity to the tasks &amp; traffic situation around him.</p>
							</div>
							<div class="left_icon"><i class="fa fa-tasks" aria-hidden="true"></i>
							</div>
							<div class="right_icon">
								<h4>Task database</h4>
								<p>See task details with task description and customer details with to call from the app.</p>
							</div>
							<div class="left_icon"><i class="fa fa-map-marker" aria-hidden="true"></i>
							</div>
							<div class="right_icon">
								<h4>Navigate</h4>
								<p>Navigate to you next task to know the exact shortest route to save time and money</p>
							</div>
							<div class="left_icon"> <i class="fa fa-pencil"></i>
							</div>
							<div class="right_icon">
								<h4>Proof of delivery</h4>
								<p>Proof of delivery by taking signature from customer, which are updated instantly to the server.</p>
							</div>
							<div class="left_icon"> <i class="fa fa-file-text-o" aria-hidden="true"></i>
							</div>
							<div class="right_icon">
								<h4>Report in real time.</h4>
								<p>Report as the make by uploading photos, document from the app</p>
							</div>
							<div class="left_icon"> <i class="fa fa-connectdevelop" aria-hidden="true"></i>
							</div>
							<div class="right_icon">
								<h4>Stay connected</h4>
								<p>stay connected with your home base by sending instant message and making call from app.</p>
							</div>
					</div>
					</div>
				</div>
			</section>
			
		</div>
		
	</div>
</div>
</style>
<?php
}
tracko_component_tracko_plugin_settings();

// --------------------------------------------------------------------	
