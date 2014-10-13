{title}Edit Milestone{/title}
{add_bread_crumb}Edit Milestone{/add_bread_crumb}
{use_widget name="object_schedule" module="schedule"}

<div id="edit_milestone_from_calendar">
	{form action=$active_milestone->getEditUrl()}
	{wrap_fields}

	{wrap field=milestone_name}
		{text_field name='milestone[name]' value=$milestone_data.name label='Name' required=true}
	{/wrap}

	{wrap field=milestone_scheduled}
		{object_schedule object=$active_milestone user=$logged_user}
	{/wrap}

	{/wrap_fields}

	{wrap_buttons}
	{submit}Save{/submit}
	{if $active_milestone->state()->canTrash($logged_user)}
		{button href=$active_milestone->state()->getTrashUrl() success_event="milestone_deleted" async=true confirm="Are you sure you want move this milestone to trash?"}Move To Trash{/button}
	{/if}
	{/wrap_buttons}
	{/form}
</div>
<script type="text/javascript">
	$('#edit_milestone_from_calendar').each(function() {
		var wrapper = $(this);
		var form = wrapper.find('form:first');
		var url = form.attr('action');

		url = App.extendUrl(url, { on_calendar: 1 });

		form.attr('action', url);
	});
</script>