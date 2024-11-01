/**
 * Form Script
 */

$(document).ready(function($){
	
	if(showFormText!="")
	{
		$(".inside").hide();
		$("#aftersubmission").show();
		$("#formtext").hide();
	}else
	{
		$(".inside").show();
		$("#aftersubmission").hide();
		$("#formtext").show();
	}

	$("#save-tracko-mobile-app").click(function(e){
		
		var username=$('#user-api').val();
		var emailid=$('#email-api').val();
		var website_url=$('#website-api').val();
		if(!username||!emailid||!website_url)
		{
			return;
		}else
		{
			 
		}
		e.preventDefault();
		$(this).toggleClass('btn-plus');
		$(".inside").slideToggle();
		if($(this).attr("value")=="Show Details")
		{
			$(this).val("Hide Details");

		}else
		{
			$(this).val("Show Details");

		}
	});

	$("#save-tracko-mobile-app_show").click(function(e){
		
		 var username=$('#user-api').val();
		 var emailid=$('#email-api').val();
		// var website_url=$('#website-api').val();
		var app_url="http://manage.thetrackotore.com/auth/set-password?username="+username+'&email_id='+emailid+'&site_url='+auto_site_url;
		window.open(app_url);
	});
   
	$('#tracko_setup_form').submit(function(e) {
		
		/*
		 var username=$('#user-api').val();
		 var emailid=$('#email-api').val();
		 var website_url=$('#website-api').val();
		 if(!username||!emailid||!website_url)
		 {
			alert("Please Enter all the Details");
			return;
		 }else
		 {
			 
		 }
		*/
		var email = $('#txtemail').val();
		var website=$('#txtwebsite').val();
		var username=$('#txtusername').val();
		
		// Checking Empty Fields
		if ($.trim(email).length == 0 || website=="" || username=="") {
			alert('All fields are mandatory');
			e.preventDefault();
		}
		
		if (validateEmail(email)) {
			alert('Nice!! your Email is valid, now you can continue..');
		}


		var data = $('#tracko_setup_form').serialize();
		$.ajax({
			url         :	ajaxurl,
			data        :	data + '&action=save_tracko_data',
			type        :	'POST',
			async: false,
			beforeSend: function(){
				$('#save-tracko-settings').val('Sending...');
				$('#save-tracko-settings').attr('disabled', 'disabled');
			},
			success     : function(data){
				$('#save-tracko-settings').val('Submit');
				$('#save-tracko-settings').removeAttr('disabled');
				try {
					var returned = jQuery.parseJSON(data);
					if(returned.results == 1){
						alert('Thank You! Click on GETMOBILE APP Button.');
						
						//hide and display elements
						e.preventDefault();
						$(this).toggleClass('btn-plus');
						$(".inside").slideToggle();
						$("#formtext").hide();
						$("#aftersubmission").show();
							$("#save-tracko-mobile-app").val("Show Basic Details");
						
						//
					}else if(returned.results == 2)
					{
						alert('Email Id is already registered.');
						return;
					}else if(returned.results == 3)
					{
						alert('Enter valid Email Id');
						return;
					}							
					else {
						alert(returned.error);
					}
				} catch (e) {
					alert('Not able to send the data.');
				}
				return true;
			}
		});
	});
	
});

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

function validateEmail(sEmail) 
{
	var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
	if (filter.test(sEmail)) {
		return true;
	}
	else {
		return false;
	}
}
