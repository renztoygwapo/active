{title}Export vCard{/title}
{add_bread_crumb}Export vCard{/add_bread_crumb}

<div class="export_vcard">
	{form action=$active_company->getExportVcardUrl() method=post}
		<p>{lang company_name=$active_company->getName()}Export ":company_name" company details to vCard{/lang}:</p>
		
		{wrap field=vcard id=companyVCard}
	    {checkbox name="vcard" checked=$vcard_data label="Include users"}
	  {/wrap}
	
	  {wrap_buttons}
	    {submit}Export vCard{/submit}
	  {/wrap_buttons}
	{/form}
</div>

<script type="text/javascript">
	var wrapper = $('.export_vcard');
	var form = wrapper.find('form');
	var checkbox = form.find('input[type="checkbox"]');
	var submit_button = form.find('button');
	
	var include_users = 0;
	
	submit_button.click(function() {
		App.widgets.FlyoutDialog.front().close();
		
		if(checkbox.is(':checked') == true) {
			include_users = 1;
		} // if
		
		var export_vcard_url = App.extendUrl(form.attr('action'), { 'include_users' : include_users, 'submitted' : 'submitted' });
		
    window.open(export_vcard_url);
    return false;
  });
</script>