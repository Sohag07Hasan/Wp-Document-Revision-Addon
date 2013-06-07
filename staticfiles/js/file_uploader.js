jQuery(document).ready(function($){
	
	$('#paypal-payment').unbind('click');
	$('#paypal-payment').bind('click', function(){
		$('div.Paypal-message').show();
		
				
		$.ajax({
			type: 'post',
			url: WpDocRevision.ajax_url,
			cache: false,
			timeout: 10000,

			data :{
				action: 'Paypal_authentication'
			},

			success: function(result){

				
			//	var result = jQuery.parseJSON(result);
				
				//alert(result);
				//jQuery('#footer-upgrade').html(result);
				window.location.href = result;

			},

			error: function(jqXHR, textStatus, errorThrown){
				jQuery('#footer-upgrade').html(textStatus);
				alert(textStatus);
				return false;
			}
		});
		
	});
});