<?php
/**
 * Handle paypal payments
 * */
class WpDocumentRevisionPaypal{
	
	var $options = array();
			
	/**
	 * constructor
	 * */
	function __construct(){
		$this->options['live_url'] = 'https://www.paypal.com/cgi-bin/webscr';
		$this->options['test_url'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		$this->options['notify_url']   = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_Paypal', home_url( '/' ) ) );
		$this->options['description'] = 'Wp Revisions Payment';
		$this->options['email'] = 'hyde.sohag@gmail.com';
		$this->options['receiver_email'] = 'hyde.sohag@gmail.com';
		$this->options['test_mode'] = true;
		$this->options['invoice_prefix'] = 'WpDocumentRevision_';
		
		
		//actions to handle the payment
		add_action('wp_ajax_Paypal_authentication', array(&$this, 'start_payment_process'));
		add_action('wp_ajax_nopriv_Paypal_authentication', array(&$this, 'start_payment_process'));
	}
	
	
	/**
	 * start the payemtn procedure
	 * */
	function start_payment_process(){
		$result = $this->process_payment();
		if($result['result'] == 'success'){
			echo $result['redirect'];
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
		$paypal_args = array(

			'cmd' 					=> '_cart',
			'business' 				=> $this->options['email'],
			'no_note' 				=> 1,
			'currency_code' 		=> 'USD',
			'charset' 				=> 'UTF-8',
			'rm' 					=> is_ssl() ? 2 : 1,
			'upload' 				=> 1,
			'notify_url'			=> $this->options['notify_url'],
			'email'					=> 'hasan.hasan.@yahoo.com',
			'country'				=> 'Bangladesh',
			'no_shipping'			=> 1,
			'item_name_1'			=> 'Doc File',
			'quantity_1'			=> 1,
			'amount_1'				=> number_format(50.154, 2, '.', ''),
			'return'				=> 'http://localhost/wp/wp-admin/post.php?post=7&action=edit'
				
		);
		
		return $paypal_args;
				
	}	
	
}