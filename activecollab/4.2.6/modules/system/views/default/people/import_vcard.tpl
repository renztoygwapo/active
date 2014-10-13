{title}Upload vCard File{/title}
{add_bread_crumb}Upload vCard File{/add_bread_crumb}

{form action=$import_vcard_url method='post' enctype='multipart/form-data'}
	<div class="form_left_col">
	  {wrap field=vcard}
	    <!-- {label for=uploadFile}vCard{/label} -->
	    <input type="file" value="" name="vcard" id="uploadFile" />
	  {/wrap}
	  
	  <input type="hidden" name="wizard_step" value="{$next_step}" />
	</div>
	
	{wrap_buttons}
	  {submit}Upload vCard File{/submit}
	{/wrap_buttons}
{/form}