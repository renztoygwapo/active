<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars">
  <div class="main_form_column">
    {wrap field=name}
      {text_field name="task[name]" value=$task_data.name id=taskSummary class='title required validate_minlength 3' label="Title" required=true maxlength="150"}
    {/wrap}

    {wrap_editor field=body}
      {label}Description{/label}
      {editor_field name="task[body]" id=taskBody inline_attachments=$task_data.inline_attachments object=$active_task}{$task_data.body nofilter}{/editor_field}
    {/wrap_editor}
  </div>
  
  <div class="form_sidebar form_first_sidebar">
    {wrap field=category_id}
      {select_task_category name="task[category_id]" value=$task_data.category_id parent=$active_project user=$logged_user label='Category' success_event="category_created"}
    {/wrap}

    {if Milestones::canAccess($logged_user, $active_project)}
      {wrap field=milestone_id}
        {select_milestone name="task[milestone_id]" value=$task_data.milestone_id project=$active_project user=$logged_user label='Milestone'}
      {/wrap}
    {/if}
    
    {wrap field=priority}
      {select_priority name="task[priority]" value=$task_data.priority label='Priority'}
    {/wrap}
    
    {if $logged_user->canSeePrivate()}
      {wrap field=visibility}
        {select_visibility name="task[visibility]" value=$task_data.visibility short_description=true label='Visibility' object=$active_task}
      {/wrap}
    {else}
      <input type="hidden" name="task[visibility]" value="1" />
    {/if}
    
    {wrap field=due_on}
      {select_due_on name="task[due_on]" value=$task_data.due_on id=taskDueOn label='Due On'}
    {/wrap}
    
  {if AngieApplication::isModuleLoaded('tracking') && TrackingObjects::canAdd($logged_user, $active_project)}
    {wrap field=estimate}
      {label}Estimate{/label}
      {select_estimate name='task[estimate_value]' value=$task_data.estimate short=true} {lang}of{/lang} {select_job_type name='task[estimate_job_type_id]' value=$task_data.estimate_job_type_id user=$logged_user short=true}
    {/wrap}
    
    {if $active_task->isLoaded()}
      {wrap field=estimate_comment}
      	{text_field name='task[estimate_comment]' label='Estimate Update Comment'}
      {/wrap}
    {/if}
  {/if}
    
    {wrap field=label}
      {select_label name="task[label_id]" value=$task_data.label_id id="taskLabel" type='AssignmentLabel' user=$logged_user label='Label'}
    {/wrap}

    {wrap field=attachments}
      {select_attachments name="task[attachments]" object=$active_task user=$logged_user label='Attachments'}
    {/wrap}

    {custom_fields name='task' object=$active_task object_data=$task_data}
  </div>
  
  <div class="form_sidebar form_second_sidebar">
    {wrap field=assignees}
      {label for=taskAssignees}Assignees{/label}
      {select_assignees name="task" value=$task_data.assignee_id  exclude=$task_data.exclude_ids other_assignees=$task_data.other_assignees object=$active_task user=$logged_user choose_responsible=true choose_subscribers=$active_task->isNew()}
    {/wrap}
  </div>
</div>