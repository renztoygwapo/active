{wrap field=name}
  {text_field name="task[name]" value=$task_data.name id=taskSummary required=true label='Summary'}
{/wrap}

{wrap field=body}
  {editor_field name="task[body]" label='Description'}{$task_data.body nofilter}{/editor_field}
{/wrap}

{wrap field=category_id}
  {select_task_category name="task[category_id]" value=$task_data.category_id parent=$active_project user=$logged_user label='Category'}
{/wrap}

{if $logged_user->canSeeMilestones($active_project)}
  {wrap field=milestone_id}
    {select_milestone name="task[milestone_id]" value=$task_data.milestone_id project=$active_project user=$logged_user label='Milestone'}
  {/wrap}
{/if}

{wrap field=priority}
  {select_priority name="task[priority]" value=$task_data.priority label='Priority'}
{/wrap}

{if $logged_user->canSeePrivate()}
  {wrap field=visibility}
    {select_visibility name="task[visibility]" value=$task_data.visibility label='Visibility' object=$active_task}
  {/wrap}
{else}
  <input type="hidden" name="task[visibility]" value="1" />
{/if}

{wrap field=due_on}
  {select_due_on name="task[due_on]" value=$task_data.due_on label='Due On'}
{/wrap}

{wrap field=label}
  {select_label name="task[label_id]" value=$task_data.label_id id="taskLabel" type='AssignmentLabel' user=$logged_user label='Label'}
{/wrap}

{wrap field=assignees}
	{select_assignees name='task' value=$task_data.assignee_id exclude=$task_data.exclude_ids other_assignees=$task_data.other_assignees object=$active_task user=$logged_user choose_responsible=true choose_subscribers=$active_task->isNew() interface=AngieApplication::INTERFACE_PHONE}
{/wrap}

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
		App.Wireframe.DateBox.init();
	});
</script>