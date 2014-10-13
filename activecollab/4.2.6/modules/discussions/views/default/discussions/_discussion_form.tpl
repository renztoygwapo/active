<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper {if $active_discussion->isNew()}two_form_sidebars{else}one_form_sidebar{/if}">
  <div class="main_form_column">
    {wrap field=name}
      {text_field name="discussion[name]" value=$discussion_data.name id=discussionSummary class='title required validate_minlength 3' required=true label="Title" maxlength="150"}
    {/wrap}
    
    {wrap_editor field=body}
      {editor_field name="discussion[body]" id=discussionBody class="validate_callback tiny_value_present" inline_attachments=$discussion_data.inline_attachments label='Description' required=true object=$active_discussion}{$discussion_data.body nofilter}{/editor_field}
    {/wrap_editor}
  </div>

  <div class="form_sidebar {if $active_discussion->isNew()}form_first_sidebar{else}form_second_sidebar{/if}">
    {wrap field=category_id}
      {label for=discussionCategory}Category{/label}
      {select_discussion_category name="discussion[category_id]" value=$discussion_data.category_id id=discussionCategory parent=$active_project user=$logged_user optional=true success_event="category_created"}
    {/wrap}
    
  {if $logged_user->canSeeMilestones($active_project)}
    {wrap field=milestone_id}
      {label for=discussionMilestone}Milestone{/label}
      {select_milestone name="discussion[milestone_id]" value=$discussion_data.milestone_id project=$active_project user=$logged_user}
    {/wrap}
  {/if}
    
    {if $logged_user->canSeePrivate()}
      {wrap field=visibility}
        {label for=discussionVisibility}Visibility{/label}
        {select_visibility name="discussion[visibility]" value=$discussion_data.visibility short_description=true object=$active_discussion}
      {/wrap}
    {else}
      <input type="hidden" name="discussion[visibility]" value="1" />
    {/if}
    
    {wrap field=attachments}
      {select_attachments name="discussion[attachments]" object=$active_discussion user=$logged_user label='Attachments'}
    {/wrap}
  </div>
  
  {if $active_discussion->isNew()}
  <div class="form_sidebar form_second_sidebar">
      {wrap field=notify_users}
        {select_subscribers name="notify_users" exclude=$discussion_data.exclude_ids object=$active_discussion user=$logged_user label='Notify People'}
        <div class="clear"></div>
      {/wrap}
  </div>
  {/if}
</div>