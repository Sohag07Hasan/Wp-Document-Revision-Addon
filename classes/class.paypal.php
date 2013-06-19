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
	}
	
	
	/**
	 * start the payemtn procedure
	 * */
	function start_payment_process(){
		$keys = array('file_type', 'country', 'show_me', 'word_count', 'plan', 'price');
		$post_id = $_POST['post_id'];
		$this->posted = $_POST;
		
		if(!empty($post_id)){
			foreach($keys as $key){
				update_post_meta($post_id, '_' . $key, $_POST[$key]);
			}
			
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
			'item_name_1'			=> $this->posted['file_type'] . ' 1',
			'quantity_1'			=> 1,
			'amount_1'				=> number_format($this->posted['price'], 2, '.', ''),
			'return'				=> sprintf(admin_url('wp-admin/post.php?post=%s&action=edit&payment_status=%s'), $this->posted['post_id'], 'paid')
				
		);
		
		return $paypal_args;
				
	}	
	
}