<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars">
  <div class="main_form_column">
    {wrap field=name}
      {text_field name='notebook[name]' value=$notebook_data.name id=notebookName class='title required validate_minlength 3' required=true label="Title" maxlength="150"}
    {/wrap}
    
    {wrap_editor field=body}
      {label}Description{/label}
      {editor_field name='notebook[body]' id=notebookBody class='validate_callback tiny_value_present' inline_attachments=$page_data.inline_attachments auto_expand=no object=$active_notebook}{$notebook_data.body nofilter}{/editor_field}
    {/wrap_editor}
  </div>
  
  <div class="form_sidebar form_first_sidebar">
    {if $logged_user->canSeeMilestones($active_project)}
      {wrap field=milestone_id}
        {label for=notebookMilestone}Milestone{/label}
        {select_milestone name='notebook[milestone_id]' value=$notebook_data.milestone_id id=notebookMilestone project=$active_project user=$logged_user}
      {/wrap}
    {/if}
    
    {if $logged_user->canSeePrivate()}
      {wrap field=visibility}
        {label for=notebookVisibility}Visibility{/label}
        {select_visibility name='notebook[visibility]' value=$notebook_data.visibility short_description=true object=$active_notebook}
      {/wrap}
    {else}
      <input type="hidden" name="notebook[visibility]" value="1">
    {/if}
    
    {wrap field=attachments}
      {select_attachments name="notebook[attachments]" object=$active_notebook user=$logged_user label='Attachments'}
    {/wrap}
  </div>
  
  <div class="form_sidebar form_second_sidebar">
    {if $active_notebook->isNew()}  
      {wrap field=notify_users}
        {select_subscribers name=notify_users exclude=$notebook_data.exclude_ids object=$active_notebook user=$logged_user label='Notify People'}
      {/wrap}
    {/if}
  </div>
</div>