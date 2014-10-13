{title}Edit File{/title}
{add_bread_crumb}Edit File{/add_bread_crumb}

<div id="edit_file">
  {form action=$active_asset->getEditUrl()}
    {wrap field=name}
      {text_field name="file[name]" value=$file_data.name label='File Name' id=file_name required=true}
    {/wrap}
    
    {wrap_editor field=body}
      {editor_field name="file[body]" label='Body' id=file_body}{$file_data.body nofilter}{/editor_field}
    {/wrap_editor}
    
    {wrap field=category_id}
      {select_asset_category name='file[category_id]' value=$file_data.category_id parent=$active_project user=$logged_user id=file_category label="Category"}
    {/wrap}
    
    {if $logged_user->canSeeMilestones($active_project)}
      {wrap field=milestone_id}
        {select_milestone name='file[milestone_id]' value=$file_data.milestone_id project=$active_project user=$logged_user id=file_milestone label="Milestone"}
      {/wrap}
    {/if}
    
    {if $logged_user->canSeePrivate()}
      {wrap field=visibility}
        {select_visibility name='file[visibility]' value=$file_data.visibility short_description=true id=file_visibility label="Visibility" object=$active_asset}
      {/wrap}
    {/if}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
	});
</script>