{title}Calendars{/title}
{add_bread_crumb}Index{/add_bread_crumb}
{use_widget name="calendar" module="calendars"}

<div id="calendars"></div>

<script type="text/javascript">
  $('#calendars').Calendar({$calendar_data|json nofilter});
</script>