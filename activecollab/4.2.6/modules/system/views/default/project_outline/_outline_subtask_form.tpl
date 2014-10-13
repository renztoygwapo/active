{form action=$form_url method="POST"}
  <div class="outline_form_title">
    {if !$edit_mode}
      <h3>{lang}New Subtask{/lang}</h3>
    {else}
      <h3>{lang}Save Changes{/lang}</h3>
    {/if}
    <a href="#" class="outline_close_form">{lang}Close{/lang}</a>
  </div>

  <div class="outline_form">
    <div class="form_inner">
        {wrap field=name}
          {label for=subtaskSummary class='base_outline_label'}Title{/label}
          <div class="outline_field">
            {text_field name="subtask[body]" value=$subtask_data.body id=subtaskSummary class='title required'}
          </div>
        {/wrap}
                      
        {wrap field=assignee}
          {label for=subtaskAssignee class='base_outline_label'}Assignee{/label}
          <div class="outline_field">
            {if !$edit_mode}
              {select_assignee name='subtask[assignee_id]' value=$subtask_data.assignee_id parent=$subtask_parent->subtasks()->newSubtask() user=$logged_user id="subtaskAssignee"}
            {else}
              {select_assignee name='subtask[assignee_id]' value=$subtask_data.assignee_id parent=$subtask user=$logged_user id="subtaskAssignee"}
            {/if}
          </div>
        {/wrap}
        
        {wrap field=priority}
          {label for=subtaskPriority class='base_outline_label'}Priority{/label}
          <div class="outline_field">
            {select_priority name="subtask[priority]" value=$subtask_data.priority id=subtaskPriority}
          </div>
        {/wrap}

        {wrap field=label}
          {label for=subtaskLabel class='base_outline_label'}Label{/label}
          <div class="outline_field">
            {select_label name='subtask[label_id]' value=$subtask_data.label_id type='AssignmentLabel' id="subtaskLabel" user=$logged_user}
          </div>
        {/wrap}
        
        {wrap field=due_on}
          {label for=subtaskDueOn class='base_outline_label'}Due on{/label}
          <div class="outline_field">
            {select_due_on name="subtask[due_on]" value=$subtask_data.due_on id=subtaskDueOn}
          </div>
        {/wrap}
    </div>
    
    {wrap_buttons}
      {submit}Add Subtask{/submit} <span>{lang}or{/lang}</span> <a href="#" class="outline_close_form">{lang}Cancel{/lang}</a>
    {/wrap_buttons}
  </div>
{/form}