<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars">
  <div class="main_form_column">
    {wrap field=name}
      {text_field name='milestone[name]' value=$milestone_data.name id=milestoneName class='title required validate_minlength 3' required=true label="Name" maxlength="150"}
    {/wrap}
        
    {wrap_editor field=body}
      {label}Description{/label}
      {editor_field name='milestone[body]' id=milestoneBody inline_attachments=$milestone_data.inline_attachments object=$active_milestone}{$milestone_data.body nofilter}{/editor_field}
    {/wrap_editor}
  </div>
  
  <div class="form_sidebar form_first_sidebar">
    {if $active_milestone->isNew()}
      {wrap field=date_range}
        {label}Start and Due Date{/label}
        {select_milestone_dates name=milestone start_on=$milestone_data.start_on due_on=$milestone_data.due_on}
      {/wrap}
    {/if}
    
    {wrap field=priority}
      {select_priority name='milestone[priority]' value=$milestone_data.priority label='Priority'}
    {/wrap}
  </div>

  <div class="form_sidebar form_second_sidebar">
    {wrap field=assignees}
      {label for=milestoneAssignees}Assignees{/label}
      {select_assignees name='milestone' exclude=$milestone_data.exclude_ids value=$milestone_data.assignee_id other_assignees=$milestone_data.other_assignees object=$active_milestone user=$logged_user choose_responsible=true choose_subscribers=$active_milestone->isNew()}
    {/wrap}
  </div>
</div>