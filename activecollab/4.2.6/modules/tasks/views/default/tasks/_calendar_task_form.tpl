{title}Edit Task{/title}
{add_bread_crumb}Edit Task{/add_bread_crumb}

<div id="edit_task_from_calendar">
	{form action=$active_task->getEditUrl()}
	{wrap_fields}

	{wrap field=milestone_name}
	{text_field name='task[name]' value=$task_data.name label='Name' required=true}
	{/wrap}

	{wrap field=category_id}
	{select_task_category name="task[category_id]" value=$task_data.category_id parent=$active_project user=$logged_user label='Category' success_event="category_created"}
	{/wrap}

	{if Milestones::canAccess($logged_user, $active_project)}
		{wrap field=milestone_id}
		{select_milestone name="task[milestone_id]" value=$task_data.milestone_id project=$active_project user=$logged_user label='Milestone'}
		{/wrap}
	{/if}

	{wrap field=priority}
	{select_priority name='task[priority]' value=$task_data.priority label='Priority'}
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

	{wrap field=label}
	{select_label name="task[label_id]" value=$task_data.label_id id="taskLabel" type='AssignmentLabel' user=$logged_user label='Label'}
	{/wrap}

	{/wrap_fields}

	{wrap_buttons}
	{submit}Save{/submit}
	{if $active_task->state()->canTrash($logged_user)}
		{button href=$active_task->state()->getTrashUrl() success_event="task_deleted" async=true confirm="Are you sure you want move this task to trash?"}Move To Trash{/button}
	{/if}
	{/wrap_buttons}
	{/form}
</div>
<script type="text/javascript">
	$('#edit_task_from_calendar').each(function() {
		var wrapper = $(this);
		var form = wrapper.find('form:first');
		var url = form.attr('action');

		url = App.extendUrl(url, { on_calendar: 1 });

		form.attr('action', url);
	});
</script>