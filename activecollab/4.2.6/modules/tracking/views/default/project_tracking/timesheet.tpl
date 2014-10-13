{title}Timesheet{/title}
{add_bread_crumb}Timesheet{/add_bread_crumb}
{use_widget name="timesheet" module="tracking"}

<div id="project_timesheet">
  {$timesheet->render() nofilter}
</div>

<script type="text/javascript">
  $('#project_timesheet').timesheet();
</script>