{title}Edit Event{/title}
{add_bread_crumb}Edit Event{/add_bread_crumb}

<div id="edit_calendar_event">
	{bubble_options object=$active_calendar_event user=$logged_user}
	{form action=$active_calendar_event->getEditUrl()}
	{wrap_fields}
		{include file=get_view_path('_calendar_event_form', 'fw_calendar_events', $smarty.const.CALENDARS_FRAMEWORK)}
	{/wrap_fields}

	{wrap_buttons}
		{submit}Save{/submit}
	{/wrap_buttons}
	{/form}
</div>