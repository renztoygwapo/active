{title}Estimated vs Tracked Time{/title}
{add_bread_crumb}Estimated vs Tracked Time Report{/add_bread_crumb}
{use_widget name="assignments_list" module="system"}

<div id="estimated_vs_tracked_time_report_result"></div>

<script type="text/javascript">
  $('#estimated_vs_tracked_time_report_result').assignmentsList({
    'assignments' : {$assignments|map nofilter},
    'labels' : {$labels|json nofilter},
    'project_slugs' : {$project_slugs|json nofilter},
    'task_url' : {$task_url|json nofilter},
    'job_types' : {$job_types|json nofilter},
    'additional_column_1' : {$smarty.const.AssignmentFilter::ADDITIONAL_COLUMN_ESTIMATED_TIME|json nofilter},
    'additional_column_2' : {$smarty.const.AssignmentFilter::ADDITIONAL_COLUMN_TRACKED_TIME|json nofilter},
    'show_additional_column_label_in_header' : true,
    'show_no_assignments_message' : true,
    'no_assignments_message' : App.lang("There are no tasks in selected project"),
    'on_group_ready' : function(all_settings) {
      var records_table = $(this).find('table.assignments');

      if(records_table.length) {
        var estimated_time_total = 0;
        var tracked_time_total = 0;

        records_table.find('tr.assignment.task').each(function() {
          var row = $(this);

          var estimated_time = App.parseNumeric(row.find('td.additional_column_1').attr('estimated_time'));
          var tracked_time = App.parseNumeric(row.find('td.additional_column_2').attr('tracked_time'));

          if(!isNaN(estimated_time)) {
            estimated_time_total += estimated_time;
          } // if

          if(!isNaN(tracked_time)) {
            tracked_time_total += tracked_time;
          } // if
        });

        records_table.append('<tfoot>' +
          '<tr>' +
            '<td class="right" colspan="2">' + App.lang('Total') + ':</td>' +
            '<td class="center">' + App.hoursFormat(estimated_time_total) + '</td>' +
            '<td class="center">' + App.hoursFormat(tracked_time_total) + '</td>' +
          '</tr>' +
        '</tfoot>');
      } // if
    }
  });
</script>