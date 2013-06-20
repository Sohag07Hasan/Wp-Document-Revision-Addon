<?php
/**
 * Handle paypal payments
 * */
class WpDocumentRevisionPaypal{
	
	var $options = array();
	var $posted = array();
			
	/**
	 * constructor
	 * */
	function __construct(){
		
		$paypal = WpDocumentRevisionsAddon::get_paypal_credentials();
		
		$this->options['live_url'] = 'https://www.paypal.com/cgi-bin/webscr';
		$this->options['test_url'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		$this->options['notify_url']   = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_Paypal', home_url( '/' ) ) );
		$this->options['description'] = 'Wp Revisions Payment';
		$this->options['email'] = $paypal['email'];
		$this->options['receiver_email'] = $paypal['email'];
		$this->options['test_mode'] = $paypal['sandbox'] == '1';
		$this->options['invoice_prefix'] = $paypal['prefix'];
		
		
		//actions to handle the payment
		add_action('wp_ajax_Paypal_authentication', array(&$this, 'start_payment_process'));
		add_action('wp_ajax_nopriv_Paypal_authentication', array(&$this, 'start_payment_process'));
		
		//IPN for paypal
		add_action('init', array(&$this, 'check_ipn_response'), 0);
		add_action('valid-paypal-standard-ipn-request', array(&$this, 'successful_request'));
	}
	
	
	/**
	 * start the payemtn procedure
	 * */
	function start_payment_process(){
			
		$this->posted = $_POST;
		
		
		if(!empty($this->posted['post_id'])){
						
			$result = $this->process_payment();
			if($result['result'] == 'success'){
				echo $result['redirect'];
			}
		}
		else{
			echo 'false';
		}
		
		exit;
	}
	
	
	/**
	 * process the payment
	 * */
	function process_payment(){
		$paypal_args = $this->get_paypal_args();
		$paypal_args = http_build_query($paypal_args);
		
		if($this->options['test_mode'] === true){
			$paypal_adr = $this->options['test_url'] . '?test_ipn=1&';
		}
		else{
			$paypal_adr = $this->options['live_url'] . '?';
		}
		
		return array(
				'result' 	=> 'success',
				'redirect'	=> $paypal_adr . $paypal_args
			);
		
	}
	
	
	/**
	 * return every probable parameter
	 * */
	function get_paypal_args(){
		
		$keys = WpDocumentRevisionsAddon::$document_keys;
		$custom = array();
		
		foreach($keys as $key){
			$custom[$key] = $this->posted[$key];
			$_SESSION['document_revision'][$key] = $this->posted[$key];
		}
		
		$paypal_args = array(

			'cmd' 					=> '_cart',
			'business' 				=> $this->options['email'],
			'no_note' 				=> 1,
			'currency_code' 		=> 'USD',
			'charset' 				=> 'UTF-8',
			'rm' 					=> is_ssl() ? 2 : 1,
			'upload' 				=> 1,
			'notify_url'			=> $this->options['notify_url'],
			'email'					=> $this->options['email'],
			'country'				=> $this->posted['country'],
			'no_shipping'			=> 1,
			'item_name_1'			=> WpDocumentRevisionsAddon::$file_types[$this->posted['file_type']] . ' Document',
			'quantity_1'			=> 1,
			'amount_1'				=> number_format($this->posted['price'], 2, '.', ''),
			'return'				=> sprintf(admin_url('post.php?post=%s&action=edit&payment_status=%s'), $this->posted['post_id'], 'paid'),
			'custom'                => serialize($custom),
				
		);
		
		return $paypal_args;
				
	}

	
	/**
	 * Reveive the paypal payment IPN and save in database
	 * */
	function check_ipn_response(){
				
		if ( ! empty( $_GET['wc-api'] ) && $_GET['wc-api'] == 'WC_Gateway_Paypal' ){
			@ob_clean();
			
			$_SESSION['get'] = $_GET;
			
			if ( ! empty( $_POST ) && $this->check_ipn_request_is_valid() ) {
			
				header( 'HTTP/1.1 200 OK' );
			
				do_action( "valid-paypal-standard-ipn-request", $_POST );
			
			} else {
			
				wp_die( "PayPal IPN Request Failure" );
			
			}
		}
	}
	
	
	
	/**
	 * Response the ipn
	 * */
	function check_ipn_request_is_valid(){
		$received_values = array( 'cmd' => '_notify-validate' );
		$received_values += stripslashes_deep( $_POST );
		
		// Send back post vars to paypal
		$params = array(
				'body' 			=> $received_values,
				'sslverify' 	=> false,
				'timeout' 		=> 60,
				'user-agent'	=> 'DocumentRevision'
		);
		
		// Get url
		if ( $this->options['test_mode'] == '1' )
			$paypal_adr = $this->options['test_url'];
		else
			$paypal_adr = $this->options['live_url'];
		
		// Post back to get a response
		$response = wp_remote_post( $paypal_adr, $params );
				
		// check to see if the request was valid
		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && ( strcmp( $response['body'], "VERIFIED" ) == 0 ) ) {
			return true;
		}
		else{
			return false;
		}		
		
	}
	
	
	/**
	 * Successfull request
	 * */
	function successful_request($posted){
		$posted = stripslashes_deep( $posted );
		$custom = maybe_unserialize( $posted['custom'] );
		$keys = WpDocumentRevisionsAddon::$document_keys;
		
		$paypal_keys = 
		
		$post_id = (int) $custom['post_id'];
		
		$post = get_post($post_id);
		
		if($post){
			
			//document related keys
			foreach($keys as $key){
				update_post_meta($post->ID, '_' . $key, $custom[$key]);
			}
			
			// Lowercase returned variables
			$posted['payment_status'] 	= strtolower( $posted['payment_status'] );
			$posted['txn_type'] 		= strtolower( $posted['txn_type'] );
			
			//sandbox fix
			if ( $posted['test_ipn'] == 1 && $posted['payment_status'] == 'pending' )
				$posted['payment_status'] = 'completed';
			
			//save the paypal credentials
			foreach(WpDocumentRevisionsAddon::$paypal_keys as $key){
				update_post_meta($post->ID, '_' . $key, $posted[$key]);
			}
			
			update_post_meta($post->ID, '_payment_status', 'paid');
		}
	}
	
}