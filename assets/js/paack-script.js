/*jQuery(document).ready(function(){
    jQuery('.wp-paak-pop').on('click',function(e){
        e.preventDefault();
        console.log("se pulso el enlace");
    });
});
*/

jQuery(document).ready(function() {
	check_zip_code();
	jQuery('.wp-paak-pop').magnificPopup({
		type: 'inline',
		preloader: false,
		focus: '#name',
		closeBtnInside: true,
		midClick: true,
		removalDelay: 300,
		mainClass: 'my-mfp-zoom-in',
		callbacks: {
			beforeOpen: function() {
				if(jQuery(window).width() < 700) {
					this.st.focus = false;
				} else {
					this.st.focus = '#zip_code';
				}
			}
		}
	});
	jQuery('#billing_postcode').focusout(function(){
		check_zip_code();
	});
	jQuery('#button-zip-code').click(function(e){
		e.preventDefault();
		let zip_code = jQuery('#zip_code').attr('value');
		jQuery.ajax({
			url : paack.ajax_url,
			type : 'post',
			data : {
				action : 'is_zip_code',
				zip_code : zip_code
			},
			success : function( response ) {
				let availability = response.availability;
				if(availability){
					jQuery('#message_zip_code').removeClass('isa_warning');
					jQuery('#message_zip_code').addClass('isa_success');
					jQuery('#button_zip_code').removeClass('isa_hidden');
					jQuery('#table_options').removeClass('isa_hidden');
				}else{
					jQuery('#message_zip_code').removeClass('isa_success');
					jQuery('#message_zip_code').addClass('isa_warning');
				}
				jQuery('#message_zip_code span').text(response.message);
				jQuery('#message_zip_code').removeClass('isa_hidden');
			}
		});
	});

	jQuery('#button_zip_code').click(function(e){
		e.preventDefault();
		updateSend();
	});
});

function check_zip_code(){
	let zipCodeCheckout= jQuery('#billing_postcode').val();
	if(zipCodeCheckout!=undefined && zipCodeCheckout!=''){
		jQuery('#zip_code_field').removeClass('isa_hidden');
		jQuery.ajax({
			url : paack.ajax_url,
			type : 'post',
			data : {
				action : 'is_zip_code',
				zip_code : zipCodeCheckout
			},
			success : function( response ) {
				let availability = response.availability;
				if(availability){
					jQuery('#zip_code_field').removeClass('isa_warning');
					jQuery('#zip_code_field').addClass('isa_success');

					jQuery('#zip_code_field i').removeClass('fa-info');
					jQuery('#zip_code_field i').addClass('fa-check');
				}else{
					jQuery('#zip_code_field').removeClass('isa_success');
					jQuery('#zip_code_field').addClass('isa_warning');

					jQuery('#zip_code_field i').removeClass('fa-check');
					jQuery('#zip_code_field i').addClass('fa-info');

					jQuery('#zip_code_field span').text('Lo sentimos, tu codigo postal no permite envios de 2 horas.');
				}
				jQuery('#zip_code_field').removeClass('isa_hidden');
				jQuery('#zip_code_field span').removeClass('isa_hidden');
			}
		});
	}else{
		jQuery('#zip_code_field').addClass('isa_hidden');
	}
}

function updateSend(){
	
	let hour = jQuery("input[name=option_two_hour]:checked").val();
	jQuery('#paack-two-hour').attr('value',hour);
	console.log(hour);
	jQuery.magnificPopup.close();
}

