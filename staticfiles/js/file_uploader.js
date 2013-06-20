jQuery(document).ready(function($){
	
	//metabox
	var metabox = $('div#Price_and_Plan');
	var interval = ['rush', '5day', '2week'];
	var plans = ['premium', 'advanced', 'proof'];
	var overlay = '<div class="overlay">';
	var word_count;
	
	//when the search button is pressed
	$('#document_revision_search').unbind('click'); //this is to prevent mutiple instances
	$('#document_revision_search').bind('click', function(){
		var country = $('select[document_revision="country"]').val();
		var file_type = $('input[document_revision="file_type"]:checked').attr('value');
		var show_me = $('input[document_revision="show_me"]:checked').attr('value');
		word_count = $('input[document_revision="word_count"]').val();
		
		if(word_count.length == 0){
			alert('Word Count might not be empty');
			$('input[document_revision="word_count"]').css({'border':'1px solid red'});
			return false;
		}
		else{
			
			$('#Document_uploader').append(overlay);
			change_price('rush');
			
			//show the uploader metabox
			$('div#Price_and_Plan').show();
			$('div.overlay').remove();
		}
		
			
		//dynamically changing the prices
		$('input[name="interval"]').unbind('click');
		$('input[name="interval"]').bind('click', function(){
			var type = $(this).attr('value');
			change_price(type);
		});
		
		//final step for payment if select button is clicked
		$('input[plan_button="final-step-for-payment"]').unbind('click');
		$('input[plan_button="final-step-for-payment"]').bind('click', function(){
			
			$('div#Price_and_Plan').append(overlay);
			
			var plan = $(this).attr('plan');
			var post_id = $(this).attr('post_id');
			var price = $(this).parent().siblings().filter('td[plan="'+plan+'"]').html();
					
			
			//ajax requesting
			$.ajax({
				type: 'post',
				url: WpDocRevision.ajax_url,
				cache: false,
				timeout: 10000,

				data :{
					action: 'Paypal_authentication',
					country: country,
					post_id: post_id,
					file_type: file_type,
					show_me: show_me,
					plan: plan,
					price: price,
					word_count: word_count
				},

				success: function(result){
					if(result == 'false'){
						alert('Something Weired happens'); return false;
					}
					else{
						
						window.location.href = result;
						//$('#footer-left').html(result);
						//return false;
					}
				},

				error: function(jqXHR, textStatus, errorThrown){
					jQuery('#site-generator').html(textStatus);
					alert(textStatus);
					$('div.overlay').remove();
					return false;
				}

			});


						
		});	
	
	});
	
	
	// function to change the prices
	var change_price = function(type){
		for(i=0; i<plans.length; i++){
			var price = WpDocRevision.plan_price.price[plans[i]][type];
			metabox.find('td[plan="'+plans[i]+'"]').html(price * word_count);
		}
	}
	
});