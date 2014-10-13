<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars">
  <div class="main_form_column">
	  {wrap field=name}
	    {text_field name="document[name]" value=$document_data.name id=documentTitle class='title required' label="Title" required=true maxlength="150"}
	  {/wrap}
    
	  {if $active_document->isNew() || $active_document->getType() == 'text'}
		  {wrap_editor field=body}
        {label}Description{/label}
		    {editor_field name="document[body]" id=documentBody inline_attachments=$document_data.inline_attachments object=$active_document}{$document_data.body nofilter}{/editor_field}
		  {/wrap_editor}
	  {/if}
  </div>
  
  <div class="form_sidebar form_first_sidebar">
		{wrap field=category_id}
			{select_document_category name="document[category_id]" value=$document_data.category_id id=documentCategory user=$logged_user success_event="category_created" label="Category"}
		{/wrap}
    
		{if $logged_user->canSeePrivate()}
	    {assign_var name=normal_caption}{lang}Normal &mdash; <span class="details">Visible to everyone who has access to Documents section</span>{/lang}{/assign_var}
	    {assign_var name=private_caption}{lang owner_company=$owner_company->getName()}Private &mdash; <span class="details">Visible only to members of :owner_company company</span>{/lang}{/assign_var}
		  {wrap field=visibility}
			  {label for=documentVisibility}Visibility{/label}
			  {select_visibility name="document[visibility]" value=$document_data.visibility normal_caption=$normal_caption object=$active_document}
		  {/wrap}
		{else}
		  <input type="hidden" name="file[visibility]" value="1" />
		{/if}

  </div>
  
  <div class="form_sidebar form_second_sidebar">
		{if $active_document->isNew()}
			{wrap field=notify_users}
        {select_subscribers name="notify_users" exclude=$document_data.exclude_ids object=$active_document user=$logged_user label='Notify People'}
	    {/wrap}
	  {/if}
  </div>
</div>