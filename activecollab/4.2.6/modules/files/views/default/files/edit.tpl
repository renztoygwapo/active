{form action=$active_asset->getEditUrl() class='big_form'}
	<script type="text/javascript">
    App.widgets.FlyoutDialog.front().setAutoSize(false);
	</script>
	
	<div class="big_form_wrapper two_form_sidebars">
    <div class="main_form_column">
      {wrap field=name}
        {text_field name="file[name]" value=$file_data.name required=true class=title label="File Name"}
      {/wrap}
      
      {wrap_editor field=body}
        {label}Description{/label}
        {editor_field name="file[body]" id=taskBody inline_attachments=$task_data.inline_attachments object=$active_asset}{$file_data.body nofilter}{/editor_field}
      {/wrap_editor}
    </div>
    
    <div class="form_sidebar form_first_sidebar">
      {wrap field=category_id}
        {select_asset_category name='file[category_id]' value=$file_data.category_id parent=$active_project user=$logged_user label="Category" success_event="category_created"}
      {/wrap}
      
    {if $logged_user->canSeeMilestones($active_project)}
      {wrap field=milestone_id}
        {select_milestone name='file[milestone_id]' value=$file_data.milestone_id project=$active_project user=$logged_user label="Milestone"}
      {/wrap}
    {/if}
      
    {if $logged_user->canSeePrivate()}
      {wrap field=visibility}
        {select_visibility name='file[visibility]' value=$file_data.visibility short_description=true label="Visibility" object=$active_asset}
      {/wrap}
    {/if}
    </div>
    
    <div class="form_sidebar form_second_sidebar">
    
    </div>
  </div>
  
  {wrap_buttons}
    {submit}Save Changes{/submit}
  {/wrap_buttons}
{/form}