<?php 
	if($_POST['paypal-credentials-save'] == 'Y'){
		update_option('document_revision_paypal', $_POST['paypal']);
	}
	
	$paypal = self::get_paypal_credentials();
	
?>

<div class="wrap">
	<?php 
		if($_POST['paypal-credentials-save'] == 'Y'){
			echo '<div class="updated"><p>Saved<p></div>';
		}
	?>
	<form action="" method="post">
		<input type="hidden" name="paypal-credentials-save" value="Y" />
		
		<h3>PayPal standard</h3>
		<table class="form-table">
			<tr>
				<th scope="row"> <label for="paypalemail"> PayPal Email</label> </th>
				<td> <input size="35" placeholder="you@youremail.com" id="paypalemail" type="text" name="paypal[email]" value="<?php echo $paypal['email']; ?>" /> </td>
			</tr>
			
			<tr>
				<th scope="row"> <label for="invoiceprefix"> Invoice Prefix</label> </th>
				<td> <input size="35" placeholder="Document Revision" id="invoiceprefix" type="text" name="paypal[prefix]" value="<?php echo $paypal['prefix']; ?>" /> </td>
			</tr>
			
			<tr>
				<th scope="row"> <label for="sandbox"> Use Sandbox </label> </th>
				<td> <input <?php checked('1', $paypal['sandbox']); ?> id="sandbox" type="checkbox" name="paypal[sandbox]" value="1" /> </td>
			</tr>
			
		</table>
				
		<p> <input type="submit" value="Save" class="button button-primary" /> </p>
	
	</form>
	
</div>