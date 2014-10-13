{title}New Calendar{/title}
{add_bread_crumb}New Calendar{/add_bread_crumb}

<div id="add_calendar">
  {form action=Router::assemble('calendars_add')}
    {wrap_fields}
      {include file=get_view_path('_calendar_form', 'fw_calendars', $smarty.const.CALENDARS_FRAMEWORK)}
    {/wrap_fields}

    {wrap_buttons}
      {submit}Add Calendar{/submit}
    {/wrap_buttons}
  {/form}
</div>