<?php
/*
 * plugin name: Document Revistions with Paypal 
 * */

define("WPDOCUMENTREVISIONS_DIR", dirname(__FILE__));
define("WPDOCUMENTREVISIONS_URI", plugins_url('/', __FILE__));


include WPDOCUMENTREVISIONS_DIR . '/classes/class.revisions-addon.php';
WpDocumentRevisionsAddon::init();

include WPDOCUMENTREVISIONS_DIR . '/classes/class.paypal.php';
$paypal = new WpDocumentRevisionPaypal();