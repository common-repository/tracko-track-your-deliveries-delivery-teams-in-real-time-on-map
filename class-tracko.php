<?php
/**
 * TRACKO
 * @package TRACKO
 * @author  TRACKO
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class tracko_main_class{
	

	/**
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = '1.0.0';
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	/**
     * Unique identifier for plugin.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    public $plugin_slug = 'tracko';
    public $api_endpoint_base = 'wp-tracko-notify/api';
	public $allowed_post_types = array('orders');
	
	/**
	 * Initialize the plugin by setting localization, filters.
	 *
	 * @since     1.0.0
	 */

	public $platform_ = 'web';
	public $user_platform = ''; // Default
	function __construct() {
		
		// Database variables
		global $wpdb;
		$this->db 					= &$wpdb;
	    add_action( 'admin_init', array( &$this, 'tracko_register_settings' ) );	
		add_action( 'woocommerce_thankyou', array( &$this, 'tracko_notify_abt_new_order' ), 11, 1 );
		add_action( 'woocommerce_cancelled_order', array( &$this, 'tracko_notify_abt_new_order' ), 11, 1 );
		add_action( 'woocommerce_new_order', array( &$this, 'tracko_notify_new_order_event' ), 10, 1 );
		
		add_action( 'wp_ajax_save_tracko_wo_data', array( &$this, 'tracko_save_tracko_data' ) );	
				
		add_action( 'init', array( &$this, 'tracko_set_checkout_page_cookie' ) );
		add_action( 'tracko_admin_ui_footer_end', array( &$this, 'tracko_add_support_link' ) );
		
		add_action('init', array(&$this,'tracko_add_api_endpoint'));
        add_action('template_redirect', array(&$this,'tracko_handle_api_endpoints'));
		
	}
	
	
	/**
	 * Function to register activation actions
	 * 
	 * @since 1.0.0
	 */
	function tracko_plugin_activate(){
			
		//Check for WooCommerce Installment
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) and current_user_can( 'activate_plugins' ) ) {
			// Stop activation redirect and show error
			wp_die('Sorry, but this plugin requires the Woocommerce to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
		}
		update_option('tracko_plugin_activate', true);
		
		$data = array(
			'site_url' => get_bloginfo('url'),
			'plugin_version' =>'1.0.0',
		); 
					
		$args = array(
			'body' => $data,
			'timeout' => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'cookies' => array()
		);
		$response = wp_remote_post( 'http://tracko.link/tracko-plugin/activation.php', $args );
	}	
   
 	/**
	 * Function to register deactivation actions
	 * 
	 * @since 1.0.0
	 */
	function tracko_plugin_deactivate_plugin(){ 
	
		delete_option('tracko_plugin_activate');
		delete_option('tracko_settings');
	}
	
	function tracko_add_support_link(){
		echo '<p style="float: right;">For more Details or Queries regarding TRACKO Plugin, Contact Us <a href="mailto:info@tracko.link">info@tracko.link</a></p>';
	}
	
	/**
	 * Function to register the plugin settings options
	 * 
	 * @since 1.0.0
	 */
	public function tracko_register_settings() {
		register_setting('tracko_register_settings', 'tracko_settings' );
	}	
	
	/**
	 * Function to get end-point of API
	 * 
	 * @since 1.0.0
	 */
	function tracko_getApiUrl(){
		if(file_exists(plugin_dir_path( __FILE__ ).'config.txt')){
			$response = file_get_contents(plugin_dir_path( __FILE__ ).'config.txt');
			$response = json_decode($response);
			if(!empty($response)){
				return $response->api_endpoint;
			}
		} 
	}
	/**
	 * Function to get userkey
	 * 
	 * @since 1.0.0
	 */
	public function tracko_getUserKey(){
		$sq_options = get_option('tracko_settings');
		$user_key = $sq_options['user_key'];
		return $user_key;
	}

	/**
	 * Function to check if plugin is enabled
	 * 
	 * @since 1.0.0
	 */
     public function tracko_isEnabled(){
		$sq_options = get_option('tracko_settings');
		$enable = $sq_options['enable'];
		return $enable;
	}	
	
	
	function tracko_set_checkout_page_cookie(){
		
		if(isset($_REQUEST['device_type'])){
		
			$device_type = (!empty($_REQUEST['device_type'])) ? $_REQUEST['device_type']: '';
			if(!empty($device_type)){
				$tm = intval( 3600 * 24 );
				setcookie("TRACKODEVICE", $device_type, time()+$tm, "/");
			}
		}
	}
	
	/*
	*
	Send New Order info to plugin
	*
	*/
	function tracko_notify_new_order_event( $order_id=1 ) 
	{
		 $user_record_data = get_option('tracko_woo_user_status');
		
		$order_data=array();
		$order_data['order_id']=$order_id;
		$order = wc_get_order( $order_id );
		$order_data['order_data']='';
		if($order)
		{
		$order_data['order_data'] = $order->get_data();
		
		}
		
		
		
		$data = array(
			'site_url' => get_bloginfo('url'),
			'order_id' => $order_id,
			'user_id' => $user_record_data->data->id,
			'order_data' => $order_data,
		); 
					
		$args = array(
			'body' => $data,
			'timeout' => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'cookies' => array()
		);
		$response = wp_remote_post( 'http://tracko.link/tracko-plugin/neworder.php', $args );
	}
	
	/*
	*
	Nofify new order
	*
	*/
	function tracko_notify_abt_new_order( $order_id  )
	{
		if(!empty($order_id))
		{
			global $current_user;
			get_currentuserinfo();
			$user_email_id=$current_user->user_email;
			global $woocommerce;
			$order = new WC_Order( $order_id );
			$order_status = $order->get_status();
			$user_platform = (isset($_COOKIE['TRACKODEVICE'])) ? $_COOKIE['TRACKODEVICE'] : '';   
			if(!empty($user_platform))
			{
			?>
				<script type="text/javascript">
				//<![CDATA[
				var orderid = <?php echo $order_id; ?>;    
				var orderstatus = '<?php echo $order_status; ?>';      
				function sendResponse_IOS(_key, _val) {
				var iframe = document.createElement("IFRAME"); 
				iframe.setAttribute("src", _key + ":##sendToApp##" + _val); 
				document.documentElement.appendChild(iframe); 
				iframe.parentNode.removeChild(iframe); 
				iframe = null; 
				}
				function sendResponse_ANDROID(resposne)
				{
				Android.showToast(""+resposne);
				} 
				//]]>
				</script>
			<?php
				if($user_platform == 'android')
				{
					//echo 'ggoogog';
				?>
					<script type="text/javascript">    
					var email_id = '<?php echo $user_email_id; ?>';
					Android.showToast("["+orderid + "]Purchase " + orderstatus+" emailid:"+email_id);    
					</script>
				<?php 
					exit();
				}
				if( $user_platform == 'ios' )
				{
				?>
					<script type="text/javascript">    
					sendToApp( "purchase","orderid:"+orderid + ",orderstatus:" + orderstatus);
					function sendToApp(_key, _val) 
					{
						var iframe = document.createElement("IFRAME"); 
						iframe.setAttribute("src", _key + ":##sendToApp##" + _val); 
						document.documentElement.appendChild(iframe); 
						iframe.parentNode.removeChild(iframe); 
						iframe = null; 
					}    
					</script>
				<?php 		
					exit();
					if(!strcmp($platform_,'mobile'))
					{
						exit();
					}
				}
			}
		}
	}
	
	/*
	*
	New User registration
	*
	*/
	function tracko_save_tracko_data()
	{
		
		
		
		$response_array =  array('results' => 0,'error' => 'Data not send.1');
                
		if ( !current_user_can( 'manage_options' ))
		{
          	$response_array =  array( 'results' => 0, 'error' => 'Security error.3');
			$res = json_encode($response_array);
			die($res);
		}

		if(isset($_POST))
		{
			 $nonce = $_POST['_wpnonce'];								
			if ( ! wp_verify_nonce( $nonce, 'tracko_wo_setup_form' ) ) 
			{
				$response_array =  array( 'results' => 0,'error' => 'Security error.2');				
				$res = json_encode($response_array);
				die($res);
			}
			
			$data = $_POST; 
			$insertdata = array();
			$insertdata['site_url'] =  get_bloginfo('url');
			$insertdata['first_name'] =  (isset($_POST['txtusername'])) ? sanitize_text_field($_POST['txtusername']) : '';
			$insertdata['last_name'] =  (isset($_POST['txtlastname'])) ? sanitize_text_field($_POST['txtlastname']) : '';
			$insertdata['email_id'] =  (isset($_POST['txtemail'])) ? sanitize_email($_POST['txtemail']) : '';
			$insertdata['company_name'] =  (isset($_POST['txtcompanyname'])) ? sanitize_text_field($_POST['txtcompanyname']) : '';
			$insertdata['phone_number'] =  (isset($_POST['txtphone'])) ? sanitize_text_field($_POST['txtphone']) : '';
			$insertdata['password'] =  (isset($_POST['txtpassword'])) ? sanitize_text_field($_POST['txtpassword']) : '';
			
			$args = array(
				'body' => $insertdata,
				'timeout' => '5',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'cookies' => array()
			);
			 
			$response = wp_remote_post( 'http://tracko.link/tracko-plugin/registeration.php', $args );
			$response =json_decode($response['body']);
			
			if($response->result!='success')
			{
				$response_array =  array('results' => 'failed','error' => $response->message);
			} 
			else 
			{
				if(!is_serialized( $insertdata ) )
				{
					$insertdata = maybe_serialize( $insertdata );
					update_option( 'tracko_woo_settings_data', $insertdata );
					update_option('tracko_woo_user_status',$response);
					$response_array =  array('results' => 'success','error' => $response->message);
				}
				else
				{
					//update_option( 'tracko_woo_settings_data', $insertdata );
					$response_array =  array('results' => 'failed','error' => $response->message);
				}
			}
		}
		
		$res =json_encode($response_array);
		die($res);
	}
	
	function escapeJsonString($value) 
	{
     
		$escapers =     array("\\",     "/",   "\"",  "\n",  "\r",  "\t", "\x08", "\x0c");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t",  "\\f",  "\\b");
		$result = str_replace($escapers, $replacements, $value);
		return $result;
    }

	
	 function tracko_get_woo_version()
	 {
		 global $woocommerce;
		 $meta_data= array(
            'woo_version' => $woocommerce->version,
			'ssl_enabled'    	 => ( 'yes' === get_option( 'woocommerce_force_ssl_checkout' ) ),
            'permalinks_enabled' => ( '' !== get_option( 'permalink_structure' ) ),
			'tm_version'=>'1.1.0',
        );
		return $meta_data;
	 }
	 
	 function tracko_get_metadata()
	 {
		 global $woocommerce;
         $cart_url = $woocommerce->cart->get_cart_url();
		 $checkout_url = $woocommerce->cart->get_checkout_url();
		 $meta_data= array(
            'tz'			 => wc_timezone_string(),
            'c'       	 => get_woocommerce_currency(),
            'c_f'    => get_woocommerce_currency_symbol(),
            't_i'   	 => ( 'yes' === get_option( 'woocommerce_prices_include_tax' ) ),
            'w_u'    	 => get_option( 'woocommerce_weight_unit' ),
            'd_u' 	 => get_option( 'woocommerce_dimension_unit' ),
			'd_s' =>get_option('woocommerce_price_decimal_sep'),
			't_s' =>get_option('woocommerce_price_thousand_sep'),
			'p_d'=>absint(get_option('woocommerce_price_num_decimals', 2)),
			'c_p'=>get_option( 'woocommerce_currency_pos'),
			'cart_url'=> $cart_url,
			'checkout_url'=>$checkout_url,
			'hide_out_of_stock'=>get_option( 'woocommerce_hide_out_of_stock_items' )
        );
		return $meta_data;
	 }
	 
	
    private function tracko_get_ids($args,$atts){
        $r = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
        $product_info = array();
        if ($r->have_posts()) {
						while ($r->have_posts()) : $r->the_post(); global $product; 
						
						    $product_info[]=  $this->tracko_get_product_short_info($product,1);
							
						endwhile;
						 } 
		
						
		
		
		
        return $product_info;
    }
	public function tracko_get_shipping_methods()
	{	
          global $woocommerce;
		 WC()->customer->calculated_shipping( true );
		 $this->shipping_calculated = true;
		 do_action( 'woocommerce_calculated_shipping' );
		 $woocommerce->cart->calculate_shipping();
            $packages = WC()->shipping()->get_packages();
		   
		
        $return = array();
        if($woocommerce->cart->needs_shipping() ){
            $return['show_shipping'] = 1;
            $woocommerce->cart->calculate_shipping();
            $packages = WC()->shipping()->get_packages();
		   
            foreach ( $packages as $i => $package ) {
                $chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
                $return['shipping'][] = array('methods'=>$this->tracko_getMethodsInArray($package['rates']),
                    'chosen'=>$chosen_method,'index'=>$i
                );
            }
        }else{
            $return['show_shipping'] = 0;
            $return['shipping'] = array();
        }
        if(empty($return['shipping']) || is_null($return['shipping']) || !is_array($return['shipping'])) {
            $return['show_shipping'] = 0;
            $return['shipping'] = array();
        }
       return $return;		
    }
    private function tracko_getMethodsInArray($methods){
        $return = array();
        foreach($methods as $method){
            $return[]=array(
                'id'=>$method->id,
                'label'=>$method->label,
                'cost'=>$method->cost,
                'taxes'=>$method->taxes,
                'method_id'=>$method->method_id,
            );
        }
        return $return;
    }
	
	function cart()
	{
		global $woocommerce;
		return $woocommerce->cart;
	}
	
	public function tracko_get_cart_meta($data)
	{
	    $this->cart()->calculate_shipping();
        global $woocommerce;
        $return = array(
            "count"=>$this->cart()->get_cart_contents_count(),
            "shipping_fee" =>!empty($this->cart()->shipping_total)?$this->cart()->shipping_total:0,
            "tax"=>$this->cart()->get_cart_tax(),
			"total_tax"=>WC()->cart->tax_total,
			"shipping_tax"=> WC()->cart->shipping_tax_total,
            "fees"=>$this->cart()->get_fees(),
            "currency" =>get_woocommerce_currency(),
            "currency_symbol"=>get_woocommerce_currency_symbol(),
            "total"=>$this->cart()->get_cart_subtotal(true),
            "cart_total"=>$this->cart()->cart_contents_total,
            "order_total"=>$woocommerce->cart->get_cart_total(),
            "price_format"=>get_woocommerce_price_format(),
            'timezone'			 => wc_timezone_string(),
            'tax_included'   	 => ( 'yes' === get_option( 'woocommerce_prices_include_tax' ) ),
            'weight_unit'    	 => get_option( 'woocommerce_weight_unit' ),
            'dimension_unit' 	 => get_option( 'woocommerce_dimension_unit' ),
            "can_proceed"   => true,
            "error_message"   => "",
        );
		

        return $return;
    }
	function tracko_api_plugin_activate_status_action(){
		
		$response = array(
					'status' => 'success',
					'error' => '',
					'message' => 'Plugin is active.'
				);
				
		return $response;
	}
	
	public function tracko_get_cart_api() 
	{
		global $woocommerce;
		$cart = array_filter( (array)$woocommerce->cart->cart_contents );
		$return =array();
		foreach($cart as $key=>$item){
		$item["key"] = $key;
		$variation = array();
		if(isset($item["variation"]) && is_array($item["variation"])){
		foreach($item["variation"] as $id=>$variation_value){
		$variation[] = array(
		"id" => str_replace('attribute_', '', $id),
		"name"   =>  wc_attribute_label(str_replace('attribute_', '', $id)),
		"value_id"  => $variation_value,
		"value"  => trim(esc_html(apply_filters('woocommerce_variation_option_name', $variation_value)))
		);
		}
		}
		$item["variation"] = $variation;
		$item = array_merge($item,$this->tracko_get_product_short_info($item["data"],0));
		unset($item["data"]);
		$return[] = $item;
		}
		return $return;
	}
	
	
	
	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	function setAddress()
	{
		global $woocommerce;
		$woocommerce->customer->set_shipping_postcode( 12345 );
		$woocommerce->customer->set_postcode( 12345 );

		//get it
		$woocommerce->customer->get_shipping_postcode();    
		$woocommerce->customer->get_postcode();
	}
	
	
	/*
	**Custom API**
	* Create our json endpoint by adding new rewrite rules to WordPress
	*/
    function tracko_add_api_endpoint()
    {
        global $wp_rewrite;
        $post_type_tag = $this->api_endpoint_base . '_type';
        $post_id_tag   = $this->api_endpoint_base . '_id';
        add_rewrite_tag("%{$post_type_tag}%", '([^&]+)');
        add_rewrite_tag("%{$post_id_tag}%", '([0-9]+)');
        add_rewrite_rule($this->api_endpoint_base . '/([^&]+)/([0-9]+)/?', 'index.php?' . $post_type_tag . '=$matches[1]&' . $post_id_tag . '=$matches[2]', 'top');
        add_rewrite_rule($this->api_endpoint_base . '/([^&]+)/?', 'index.php?' . $post_type_tag . '=$matches[1]', 'top');
        $wp_rewrite->flush_rules(false);
    }
    /**
     * Handle the request of an endpoint
     */
    function tracko_handle_api_endpoints()
    {
		global $wp_query;
        // get the query args and sanitize them for confidence
        $type = sanitize_text_field($wp_query->get($this->api_endpoint_base . '_type'));
        $id   = intval($wp_query->get($this->api_endpoint_base . '_id'));
        // only allowed post_types
        if (!in_array($type, $this->allowed_post_types)) {
            return;
        }
        switch ($type) {
            case "orders":
                $data = $this->tracko_api_orders($_POST);
                break;
		}
        // data is built. print as json and stop
        if (isset($data) && !empty($data)) 
		{
            echo json_encode($data);
            exit();
        } 
		else 
		{
            $data = array(
                'status' => 'failed',
                'error' => 1,
                'message' => 'No data received.'
            );
            echo json_encode($data);
            exit();
        }
        echo '';
        exit;
	}
	
	function tracko_api_orders($postData = array())
    {
		$response=array();
		if(isset($postData['order_ids']) && !empty($postData['order_ids']))
		{
			$order_ids =json_decode($postData['order_ids']);
			if(!empty($order_ids))
			{
				foreach($order_ids as $order_id )
				{
					$order_data=array();
					$order_data['order_id']=$order_id;
					$order = wc_get_order( $order_id );
					
					$order_data['order_data']='';
					if($order)
					{
						$order_data['order_data'] = $order->get_data();
						$order_data['order_data']['product_data']='';
						foreach($order->get_items() as $item){
							$item_data =array(
								'product_id'=>$item['product_id'],
								'product_name'=>$item['name'],
								'product_description'=>get_post($item['product_id'])->post_content
							);
							$order_data['order_data']['product_data'][] = $item_data;
						}
					}
				
					$response[] =$order_data;
				
				}
				return $response;
			}
		}
		
        $response = array('status' => 'failed','error' => 1,'message' => 'No data received.');
        return $response;
    }
}