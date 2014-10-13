{use_widget name="form" module="environment"}

<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div id="upload_file_document">
  {form action=$upload_url enctype="multipart/form-data" class='big_form'}
    <div class="big_form_wrapper two_form_sidebars">
    	<div class="main_form_column">
    	  {wrap field=file}
          {label for=uploadDocument required=yes}File{/label}
          <input type="file" value="" name="file"/>
          <p class="details">{max_file_size_warning}</p>
        {/wrap}
    	</div>
    	
    	<div class="form_sidebar form_first_sidebar">
      	{if $logged_user->canSeePrivate()}
          {assign_var name=normal_caption}{lang}Normal &mdash; <span class="details">Visible to anyone who has access to Documents section</span>{/lang}{/assign_var}
        
          {wrap field=visibility}
        	  {label for=fileVisibility}Visibility{/label}
        	  {select_visibility name='file[visibility]' value=$file_data.visibility normal_caption=$normal_caption object=$active_document}
          {/wrap}
        {else}
          <input type="hidden" name="file[visibility]" value="1" />
        {/if}
        
        {wrap field=category_id}
      		{label for=fileCategory}Category{/label}
      		{select_document_category id=fileCategory name='file[category_id]' value=$file_data.category_id can_see_private=$active_document->canView($logged_user) user=$logged_user success_event="category_created"}
      	{/wrap}
    	</div>
    	
    	<div class="form_sidebar form_second_sidebar">
    		{if $active_document->isNew()}
    			{wrap field=notify_users}
          {select_subscribers name="notify_users" exclude=$file_data.exclude_ids object=$active_document user=$logged_user label='Notify People'}
			    {/wrap}
    	  {/if}
      </div>
    </div>
    
    {wrap_buttons}
      {submit}Upload File{/submit}
    {/wrap_buttons}
  {/form}
</div>