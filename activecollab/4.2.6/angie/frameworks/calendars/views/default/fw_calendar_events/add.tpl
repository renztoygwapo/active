{title}New Event{/title}
{add_bread_crumb}New Event{/add_bread_crumb}

<div id="add_calendar_event">
	{form action=Router::assemble('events_add')}
	{wrap_fields}
		{include file=get_view_path('_calendar_event_form', 'fw_calendar_events', $smarty.const.CALENDARS_FRAMEWORK)}
	{/wrap_fields}

	{wrap_buttons}
	{submit}Add Event{/submit}
	{/wrap_buttons}
	{/form}
</div>

<script type="text/javascript">
	$('div#calendar_event_form').each(function() {
		var wrapper = $(this);
		var url_add = '{assemble route=calendar_events_add calendar_id="--CALENDAR-ID--"}';
		var selection = wrapper.find('select.select_calendar');
		var calendar_id = selection.val() ? selection.val() : 0;
		var url = url_add.replace('--CALENDAR-ID--', calendar_id);
		selection.parents('form:first').attr('action', url);

		wrapper.on('change', 'select.select_calendar', function(event) {
			var calendar_id = $(this).val();
			var url = url_add.replace('--CALENDAR-ID--', calendar_id);
			$(this).parents('form:first').attr('action', url);
		});
	});
</script>

