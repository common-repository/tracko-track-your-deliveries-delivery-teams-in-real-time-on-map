/*!
* TRACKO javaScript
*/
//jQuery(function(){
	
jQuery(document).ready(function($){
		
	jQuery( ".cmdSubmitWoData" ).live( "click", function() 
	{
		var username = jQuery.trim(jQuery('#txtusername').val());
		var email=jQuery.trim(jQuery('#txtemail').val());
		var phone=jQuery.trim(jQuery('#txtphone').val());
		var siteurl=jQuery.trim(jQuery('#txtsiteurl').val());
		
		
		// Checking Empty Fields
		if (email.length == 0 || email =="" || phone=="" || username=="") 
		{
			alert('All fields are mandatory');
			return false;
		}
		
		if (validateEmail(email) !==true)
		{
			alert('Bad!! your Email is not valid..');
			jQuery('#txtemail').focus();
			return false;
		}
		
		var data = jQuery('#tracko_wo_setup_form').serialize();
		var formid ='#tracko_wo_setup_form';
		
		jQuery.ajax({
			url	:ajaxurl,
			data:data + '&action=save_tracko_wo_data',
			type:'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('.cmdSubmitWoData').val('Wait...');
				jQuery('.cmdSubmitWoData').attr('disabled', 'disabled');
				jQuery(".cmdSubmitWoData").css("background","#ddd");
			},
			success:function(data)
			{
				//alert(data.error);
				if(data.results =='success')
				{
					alert('Thank You! Details submitted successfully.');
					jQuery(".cmdSubmitWoData").css("background","#ddd");
					var visit ="window.open('http://tracko.link?url="+siteurl+"&email="+email+"','_blank')";
					var e ='<div class="md-ripple-container"><button class="visit" onclick="'+visit+'" title="Tracko" type="button"> Visit </button> <button class="editbutton" data-form="tracko_wo_setup_form" type="button"> Edit </button></div>';
					jQuery(formid+' #tracko_form_footer').html(e);
					jQuery(formid+' input').prop('disabled', true);
					jQuery(formid+' label').css('top', '-18px');
					jQuery(formid+' label').css('font-size', '12px');
				}
				else
				{
					alert(data.error);
					jQuery('.cmdSubmitWoData').val('SUBMIT');
					jQuery(".cmdSubmitWoData").css("background","#fff");
					jQuery('.cmdSubmitWoData').removeAttr('disabled');
				}
			}
		});
		return false;
	});
	
	//tooltip 
	jQuery("#tooltipbox_sec,#tooltipbox_key").click(function() {
		jQuery('html, body').animate({
			scrollTop: jQuery("#apphelp").offset().top
		}, 2000);
	});

	jQuery('#insert-my-media').click(function()
	{
		if (this.window === undefined) {
			this.window = wp.media({
					title: 'Insert a media',
					library: {type: 'image'},
					multiple: false,
					button: {text: 'Insert'}
				});

			var self = this; // Needed to retrieve our variable in the anonymous function below
			this.window.on('select', function() {
					var first = self.window.state().get('selection').first().toJSON();
					//alert(JSON.stringify(first));
					jQuery("#txtlogourl").val(first.url);
					jQuery("#blah").attr("src", first.url);
				});
		}

		this.window.open();
		return false;
	});
	
	
	jQuery( ".editbutton" ).live( "click", function() 
	{
		var formid =jQuery(this).data('form');
		
		jQuery('#'+formid+' input').prop('disabled', false);
		jQuery('#'+formid+' label').css('top', '0');
		jQuery('#'+formid+' label').css('font-size', '15px');
		
		var email=jQuery.trim(jQuery('#txtemail').val());
		var siteurl=jQuery.trim(jQuery('#txtsiteurl').val());
		var visit ="window.open('http://tracko.link?url="+siteurl+"&email="+email+"','_blank')";
		
		if(formid =='tracko_wo_setup_form')
		{
			var s ='<input type="submit" value="Update" class="cmdSubmitWoData" id="save-tracko-settings"/>';
			var c ='<input type="button" data-form="tracko_wo_setup_form" class="cancelbutton" value="Cancel">';
			var v ='<input type="button" class="visit" onclick="'+visit+'" title="Tracko" value="Visit">'
			
			jQuery('#'+formid+' #tracko_form_footer').html(s+c+v);
		}
		else if(formid =='tracko_store_form')
		{
			var s ='<input type="submit" value="Update" class="cmdSubmitStoreData" id="save-tracko-settings"/>';
			var c ='<input type="button" data-form="tracko_store_form" class="cancelbutton" value="Cancel">';
			
			jQuery('#'+formid+' #tracko_form_footer').html(s+c);
			jQuery('#'+formid+' select').removeAttr('disabled');
		}
		else if(formid =='tracko_app_form')
		{
			var s ='<input type="submit" value="Update" class="cmdSubmitAppData" id="save-tracko-settings"/>';
			var c ='<input type="button" data-form="tracko_app_form" class="cancelbutton" value="Cancel">';
			
			jQuery('#'+formid+' #tracko_form_footer').html(s+c);
		}
	});
	
	
	jQuery( ".cancelbutton" ).live( "click", function() 
	{
		var formid =jQuery(this).data('form');
		
		jQuery('#'+formid+' input').prop('disabled', true);
	    jQuery('#'+formid+' label').css('top', '-18px');
		jQuery('#'+formid+' label').css('font-size', '12px');
		
		var email=jQuery.trim(jQuery('#txtemail').val());
		var siteurl=jQuery.trim(jQuery('#txtsiteurl').val());
		var visit ="window.open('http://tracko.link?url="+siteurl+"&email="+email+"','_blank')";
		
		if(formid =='tracko_wo_setup_form')
		{
			var e ='<div class="md-ripple-container"><button class="visit" onclick="'+visit+'" title="Tracko" type="button"> Visit </button> <button class="editbutton" data-form="tracko_wo_setup_form" type="button"> Edit </button></div>';
			jQuery('#'+formid+' #tracko_form_footer').html(e);
		}
		else if(formid =='tracko_store_form')
		{
			var e ='<div class="md-ripple-container"><button class="editbutton" data-form="tracko_store_form" type="button"> Edit </button></div>';
			jQuery('#'+formid+' #tracko_form_footer').html(e);
			jQuery('#'+formid+' select').attr('disabled','disabled');
		}
		else if(formid =='tracko_app_form')
		{
			var e ='<button class="editbutton" data-form="tracko_app_form" type="button"> Edit </button>';
			jQuery('#'+formid+' #tracko_form_footer').html(e);
		}
	});

});

function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            jQuery('#layoutSetting #blah').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}


function toggleproviderkeys(idp)
{
	if(typeof jQuery=="undefined")
	{
		alert( "Error: TRACKO require jQuery to be installed on your wordpress in order to work!" );

		return;
	}

	if(jQuery('#tracko_settings_' + idp + '_enabled').val()==1)
	{
		jQuery('.tracko_tr_settings_' + idp).show();
	}
	else
	{
		jQuery('.tracko_tr_settings_' + idp).hide();
		jQuery('.tracko_div_settings_help_' + idp).hide();
	}

	return false;
}

function toggleproviderhelp(idp)
{
	if(typeof jQuery=="undefined")
	{
		alert( "Error: TRACKO require jQuery to be installed on your wordpress in order to work!" );

		return false;
	}

	jQuery('.tracko_div_settings_help_' + idp).toggle();

	return false;
}

function validateEmail(email) {
	var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	return expr.test(email);
};

function validateUrl(url) {
    return /^(http?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}
