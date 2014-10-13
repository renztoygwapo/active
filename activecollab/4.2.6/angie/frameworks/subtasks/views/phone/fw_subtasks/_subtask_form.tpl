{wrap field=body}
  {text_field name='subtask[body]' value=$subtask_data.body id="summary" label='Summary' required=true}
{/wrap}
  	
{wrap_editor field=assignee}
  {select_assignee name='subtask[assignee_id]' value=$subtask_data.assignee_id parent=$active_subtask user=$logged_user id="assignee" label='Assignee'}
{/wrap_editor}

{if $active_subtask->usePriority()}
  {wrap field=priority}
    {select_priority name='subtask[priority]' value=$subtask_data.priority id="priority" label='Priority'}
  {/wrap}
{/if}

{if $active_subtask->useLabels()}
  {wrap field=label}
    {select_label name='subtask[label_id]' value=$subtask_data.label_id type=get_class($active_subtask->label()->newLabel()) id="label" user=$logged_user can_create_new=false label='Label'}
  {/wrap}
{/if}

{wrap_editor field=due_on}
  {select_date name='subtask[due_on]' value=$subtask_data.due_on id="due_on" label='Due On'}
{/wrap_editor}

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
		App.Wireframe.DateBox.init();
	});
</script>