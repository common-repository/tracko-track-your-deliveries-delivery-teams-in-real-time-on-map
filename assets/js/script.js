/*!
* TRACKO javaScript
*/
jQuery(function(){
	
	//copy text
	jQuery('#copyAppLink').live("click", function() {
		
		var textid =jQuery(this).data('copy');
		
		copyToClipboard(jQuery('#'+textid));
		jQuery('#'+textid).select();
	});
	
	jQuery( ".cmdSubmitStyle" ).live( "click", function() 
	{
		var data_menu = jQuery('input:checkbox[name=integrateToMenu]').is(':checked');
		var data_footer = jQuery('input:checkbox[name=integrateToFooter]').is(':checked');
		var data_style = jQuery('input[name=appLinkType]:checked').val();
		var data_style_align = jQuery('input[name=appLinkAlign]:checked').val();
		jQuery( ".cmdSubmitStyle" ).val('Saving...');
		//alert(data);
		jQuery.ajax({
			url	:ajaxurl,
			data:'style='+data_style+'&isfooter='+data_footer+'&ismenu='+data_menu+'&style_align='+data_style_align+'&action=save_tracko_app_style',
			type:'POST',
			dataType: 'json',
			success:function(data)
			{
				//alert(data);
				if(data.error !==1)
				{
					alert('Style has been saved successfully.');
				}
				else
				{
					alert('Unable to process your request.');
				}
				jQuery('#tracko_link_style input').prop('disabled', true);		
				var e ='<div class="md-ripple-container"><button class="editbutton" data-form="tracko_link_style" type="button"> Edit </button></div>';
				jQuery('#tracko_link_style #tracko_form_footer').html(e);
			}
		});
		return false;
	});
	
	jQuery( "#tracko_apikey_ids" ).submit(function() 
	{
		
		jQuery( ".cmdSubmitApiId" ).val('Saving...');
		var data = jQuery(this).serialize();
		//alert(data);
		jQuery.ajax({
			url	:ajaxurl,
			data:'data='+data+'&action=save_api_key_id',
			type:'POST',
			dataType: 'json',
			success:function(data)
			{
				//alert(data);
				if(data.error !==1)
				{
					alert('Style has been saved successfully.');
				}
				else
				{
					alert('Unable to process your request.');
				}
				jQuery('#tracko_apikey_ids input').prop('disabled', true);		
				var e ='<div class="md-ripple-container"><button class="editbutton" data-form="tracko_apikey_ids" type="button"> Edit </button></div>';
				jQuery('#tracko_apikey_ids #tracko_form_footer').html(e);
			}
		});
		return false;
	});
	
	jQuery( ".editbutton" ).live( "click", function() 
	{
		var formid =jQuery(this).data('form');
		
		jQuery('#'+formid+' input').prop('disabled', false);
		
		if(formid =='tracko_link_style')
		{
			var s ='<input type="submit" value="Update" class="cmdSubmitStyle" id="save-tracko-settings"/>';
			var c ='<input type="button" data-form="tracko_link_style" class="cancelbutton" value="Cancel">';
			
			jQuery('#'+formid+' #tracko_form_footer').html(s+c);
		}
		else if(formid =='tracko_apikey_ids')
		{
			var s ='<input type="submit" value="Submit" class="cmdSubmitApiId" id="save-tracko-apikey-ids"/>';
			var c ='<input type="button" data-form="tracko_link_style" class="cancelbutton" value="Cancel">';
			
			jQuery('#'+formid+' #tracko_form_footer').html(s+c);
		}
	});
	
	jQuery( ".cancelbutton" ).live( "click", function() 
	{
		var formid =jQuery(this).data('form');
		
		jQuery('#'+formid+' input').prop('disabled', true);		
		if(formid =='tracko_link_style')
		{
			var e ='<div class="md-ripple-container"><button class="editbutton" data-form="tracko_link_style" type="button"> Edit </button></div>';
			jQuery('#'+formid+' #tracko_form_footer').html(e);
		}
		
	});
	
	jQuery('#tracko_link_style input').prop('disabled', true);
	
});

function copyToClipboard(element) {
  var $temp = jQuery("<input>");
  jQuery("body").append($temp);
  $temp.val(jQuery(element).val()).select();
  document.execCommand("copy");
  $temp.remove();
}
