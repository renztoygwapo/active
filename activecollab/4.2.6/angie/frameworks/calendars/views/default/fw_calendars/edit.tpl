{title}Update Calendar{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

<div id="edit_calendar">
	{bubble_options object=$active_calendar user=$logged_user}
  {form action=$active_calendar->getEditUrl()}
    {wrap_fields}
      {include file=get_view_path('_calendar_form', 'fw_calendars', $smarty.const.CALENDARS_FRAMEWORK)}
    {/wrap_fields}

    {wrap_buttons}
      {submit}Save{/submit}
    {/wrap_buttons}
  {/form}
</div>