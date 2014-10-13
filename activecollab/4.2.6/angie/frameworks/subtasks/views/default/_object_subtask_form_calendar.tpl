{title}Edit Subtask{/title}
{add_bread_crumb}Edit Subtask{/add_bread_crumb}

<div id="edit_subtask_from_calendar">
	{form action=$active_subtask->getEditUrl()}
	{wrap_fields}

		{wrap field=subtask_body}
			{text_field name='subtask[body]' value=$subtask_data.body label='Name' required=true}
		{/wrap}

		{wrap field=subtask_assignee_id}
			{label for="($subtasks_id)_select_assignee"}Assignee{/label}
			{select_assignee name='subtask[assignee_id]' value=$subtask_data.assignee_id parent=$active_subtask user=$logged_user id="{$subtasks_id}_select_assignee"}
		{/wrap}

		{if $active_subtask->usePriority()}
			{wrap field=subtask_priority}
				{label for="($subtasks_id)_task_priority"}Priority{/label} 
				{select_priority name='subtask[priority]' value=$subtask_data.priority id="{$subtasks_id}_task_priority"}
			{/wrap}
		{/if}

		{if $active_subtask->useLabels()}
			{wrap field=subtask_priority}
				{label for="($subtasks_id)_label"}Label{/label}
				{select_label name='subtask[label_id]' value=$subtask_data.label_id type=get_class($active_subtask->label()->newLabel()) id="{$subtasks_id}_label" user=$logged_user can_create_new=false}
			{/wrap}
		{/if}

		{wrap field=subtask_due_on}
			{label for="($subtasks_id)_due_on"}Due On{/label}
			{select_due_on name='subtask[due_on]' value=$subtask_data.due_on id="{$subtasks_id}_due_on"}
		{/wrap}

	{/wrap_fields}

	{wrap_buttons}
		{submit}Save{/submit}
		{if $active_subtask->state()->canTrash($logged_user)}
			{button href=$active_subtask->state()->getTrashUrl() success_event="subtask_deleted" async=true confirm="Are you sure you want move this subtask to trash?"}Move To Trash{/button}
		{/if}
	{/wrap_buttons}
	{/form}
</div>
<script type="text/javascript">
	$('#edit_subtask_from_calendar').each(function() {
		var wrapper = $(this);
		var form = wrapper.find('form:first');
		var url = form.attr('action');

		url = App.extendUrl(url, { on_calendar: 1 });

		form.attr('action', url);
	});
</script>