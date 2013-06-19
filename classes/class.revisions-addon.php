<?php
/**
 * Document rivisions addon class
 * */

class WpDocumentRevisionsAddon{
	
	static $capabilities = array('document_rivision_addon_applied');
	
	static $metabox_object;
	
	static $plans = array(
				array('premium', 'Premium Editing'),
			    array('advanced', 'Advanced Editing'),
				array('proof', 'Proofreading')
			);
	
	static $intervals = array(
				array('rush', 'Rush'),
				array('5day', '5 Day'),
				array('2week', '2 Week')
			);
	
	static $file_types = array('Word', 'TeX', 'PDF', 'PPT', 'Excel', 'Images');
	
	
	/**
	 * static constructor
	 * @holds every hooks
	 * */
	static function init(){
		add_filter('document_revisions_cpt', array(get_class(), 'filtering_documents_metaboxes'), 10, 1);
		add_action('admin_enqueue_scripts', array(get_class(), 'admin_enqueue_scripts'));
		
		add_action('admin_menu', array(get_class(), 'admin_menu'));
		
		//actions to handle the payment
		add_action('wp_ajax_DocRevision_Pric_Plan', array(get_class(), 'manipulate_price_and_plan_metabx'));
		add_action('wp_ajax_nopriv_DocRevision_Pric_Plan', array(get_class(), 'manipulate_price_and_plan_metabx'));
		
		//add_action('init', array(get_class(), 'manipulate_price_and_plan_metabx'));
	}
	
	
	
	/**
	 * specific scripts for admin side
	 * */
	static function admin_enqueue_scripts(){
		wp_register_style('wprevision_file_uploader_css', WPDOCUMENTREVISIONS_URI . 'staticfiles/css/file_uploader.css');
		wp_enqueue_style('wprevision_file_uploader_css');
		
		wp_register_script('wprevision_file_uploader_js', WPDOCUMENTREVISIONS_URI . 'staticfiles/js/file_uploader.js', array('jquery'));
		wp_enqueue_script('wprevision_file_uploader_js');
		wp_localize_script('wprevision_file_uploader_js', 'WpDocRevision', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'plan_price' => self::get_plans_prices()
		));
	}
	
	
	
	/**
	 * new submit meta boxes
	 * */
	static function new_submit_metabox(){
		include self::get_file_directory('templates/metaboxes/new_submit_metabox.php');
	}
	
	/**
	 * plan and price metabox
	 * */
	static function plan_price_metabox(){
		include self::get_file_directory('templates/metaboxes/plan_price_metabox.php');
	}
	
	
	/**
	 * get the file directory
	 * */
	static function get_file_directory($file){
		return WPDOCUMENTREVISIONS_DIR . '/' . $file;
	}
	
	
	/**
	 * metabox to handle file uploader
	 * */
	static function file_uploader_metabox($post){
		include self::get_file_directory('templates/metaboxes/file_uploader_metabox.php');
	}
	
	
	/**
	 * Filter the document rivisions metaboxes
	 * */
	function filtering_documents_metaboxes($args){		
		self::$metabox_object = $args['register_meta_box_cb'][0];
		
		$args['register_meta_box_cb'] = array(get_class(), 'meta_cb');
		return $args;
	}
	
	
	/**
	 * Callback to manage metaboxes on edit page
	 * @ since 0.5
	 */
	static function meta_cb() {
	
		
		//remove unused meta boxes
		//remove_meta_box('submitdiv', 'document', 'side');
		remove_meta_box( 'revisionsdiv', 'document', 'normal' );
		remove_meta_box( 'postexcerpt', 'document', 'normal' );
		remove_meta_box( 'tagsdiv-workflow_state', 'document', 'side' );
		
		remove_meta_box('submitdiv', 'document', 'side', 'core');
		
		
		//add our meta boxes
		add_meta_box( 'revision-summary', __('Revision Summary', 'wp-document-revisions'), array(&self::$metabox_object, 'revision_summary_cb'), 'document', 'normal', 'default' );
		add_meta_box( 'document', __('Document', 'wp-document-revisions'), array(&self::$metabox_object, 'document_metabox'), 'document', 'normal', 'high' );
		
		//revised
		add_meta_box('Document_uploader', 'Instant Search for Quotes', array(get_class(), 'file_uploader_metabox'), 'document', 'normal', 'core');
		add_meta_box('Price_and_Plan', 'Price and Plan', array(get_class(), 'plan_price_metabox'), 'document', 'normal', 'core');
		
		if ( $post->post_content != '' )
			add_meta_box( 'revision-log', 'Revision Log', array( &self::$metabox_object, 'revision_metabox'), 'document', 'normal', 'low' );
		
			
		if ( taxonomy_exists( 'workflow_state' ) && current_user_can('remove_users')){
			add_meta_box( 'workflow-state', __('Workflow State', 'wp-document-revisions'), array( &self::$metabox_object, 'workflow_state_metabox_cb'), 'document', 'side', 'default' );
		}
				
		
		//move author div to make room for ours
		remove_meta_box( 'authordiv', 'document', 'normal' );

		//only add author div if user can give someone else ownership
		if ( current_user_can( 'edit_others_documents' ) )
			add_meta_box( 'authordiv', __('Owner', 'wp-document-revisions'), array( &self::$metabox_object, 'post_author_meta_box' ), 'document', 'side', 'low' );

		//lock notice
		add_action( 'admin_notices', array( &self::$metabox_object, 'lock_notice' ) );

		do_action( 'document_edit' );
		
	}
	
	
	
	/**
	 * submenus for document addons
	 * */
	static function admin_menu(){
		add_submenu_page('edit.php?post_type=document', 'Paypal Credentials', 'Paypal', 'manage_options', 'papal-for-documents', array(get_class(), 'configure_payapl'));
		add_submenu_page('edit.php?post_type=document', 'Document Plans', 'Plan & Price', 'manage_options', 'plan-nd-price-for-documents', array(get_class(), 'plan_and_price'));		
	}
	
	
	
	/**
	 * configuring paypal
	 * */
	static function configure_payapl(){
		include self::get_file_directory('templates/admin/paypal.php');		
	}
	
	
	
	/**
	 * allow admin to set the plan and Price
	 * */
	static function plan_and_price(){
		include self::get_file_directory('templates/admin/plan-nd-price.php');
	}
	
	
	
	/**
	 * return the paypal caredetials
	 * */
	static function get_paypal_credentials(){
		return get_option('document_revision_paypal');
	}
	
	
	
	/**
	 * get the plan and prices
	 * */
	static function get_plans_prices(){
		return get_option('document_revision_plan_price');
	}
	
	
	/**
	  Manipulation of ajax for showing and hide metabox price and plan with appropriate prefille data
	 * */
	static function manipulate_price_and_plan_metabx(){
		
		
		$_SESSION['country'] = $_POST['country'];
		$_SESSION['file_type'] = $_POST['file_type'];
		$_SESSION['show_me'] = $_POST['show_me'];
		$_SESSION['word_count'] = $_POST['word_count'];
		
		$plan_price = self::get_plans_prices();
		
		var_dump($plan_price);
	}
}
