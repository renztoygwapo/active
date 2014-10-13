{title}Change Color{/title}
{add_bread_crumb}Change Color{/add_bread_crumb}

<div id="change_color">
	{form action=$calendar_change_color_url}
	{wrap_fields}
		{wrap field=calendar_color}
		{select_calendar_color name='calendar[color]' value=$calendar_data.color label='Color'}
		{/wrap}
	{/wrap_fields}

	{wrap_buttons}
	{submit}Save{/submit}
	{/wrap_buttons}
	{/form}
</div>