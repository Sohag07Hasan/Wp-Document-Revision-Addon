<?php
/**
 * Document rivisions addon class
 * */

class WpDocumentRevisionsAddon{
	
	static $capabilities = array('document_rivision_addon_applied');
	
	static $metabox_object;
	
	
	/**
	 * static constructor
	 * @holds every hooks
	 * */
	static function init(){
		add_filter('document_revisions_cpt', array(get_class(), 'filtering_documents_metaboxes'), 10, 1);
		add_action('admin_enqueue_scripts', array(get_class(), 'admin_enqueue_scripts'));
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
			'ajax_url' => admin_url('admin-ajax.php')
		));
	}
	
	
	
	/**
	 * new submit meta boxes
	 * */
	static function new_submit_metabox(){
		include self::get_file_directory('templates/metaboxes/new_submit_metabox.php');
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
	
		global $post;

		//remove unused meta boxes
		//remove_meta_box('submitdiv', 'document', 'side');
		remove_meta_box( 'revisionsdiv', 'document', 'normal' );
		remove_meta_box( 'postexcerpt', 'document', 'normal' );
		remove_meta_box( 'tagsdiv-workflow_state', 'document', 'side' );

		//add our meta boxes
		add_meta_box( 'revision-summary', __('Revision Summary', 'wp-document-revisions'), array(&self::$metabox_object, 'revision_summary_cb'), 'document', 'normal', 'default' );
		add_meta_box( 'document', __('Document', 'wp-document-revisions'), array(&self::$metabox_object, 'document_metabox'), 'document', 'normal', 'high' );
		
		add_meta_box('Document_uploader', 'Instant Search for Quotes', array(get_class(), 'file_uploader_metabox'), 'document', 'normal', 'core');
		
		if ( $post->post_content != '' )
			add_meta_box( 'revision-log', 'Revision Log', array( &self::$metabox_object, 'revision_metabox'), 'document', 'normal', 'low' );
		
			
		if ( taxonomy_exists( 'workflow_state' ) )
			add_meta_box( 'workflow-state', __('Workflow State', 'wp-document-revisions'), array( &self::$metabox_object, 'workflow_state_metabox_cb'), 'document', 'side', 'default' );
		
		
		//move author div to make room for ours
		remove_meta_box( 'authordiv', 'document', 'normal' );

		//only add author div if user can give someone else ownership
		if ( current_user_can( 'edit_others_documents' ) )
			add_meta_box( 'authordiv', __('Owner', 'wp-document-revisions'), array( &self::$metabox_object, 'post_author_meta_box' ), 'document', 'side', 'low' );

		//lock notice
		add_action( 'admin_notices', array( &self::$metabox_object, 'lock_notice' ) );

		do_action( 'document_edit' );
		
	}
	
}
