{title}Estimated vs Tracked Time{/title}
{add_bread_crumb}Estimated vs Tracked Time Report{/add_bread_crumb}

<div id="estimated_vs_tracked_time_report" class="filter_criteria project_tasks_picker_for_reports">
{if $projects_exist}
  <form action="{assemble route=estiamted_vs_tracked_time_report_run}" method="get">
    <!-- Project Picker -->

    <div class="criteria_head">
      <div class="criteria_head_inner">
        <div class="criteria_project_picker">
          {lang}Project{/lang}
          {select_project name='task_report[project_id]' user=$logged_user show_all=true class=long}
        </div>
        <div class="criteria_group_by">
          {lang}Group by{/lang}
          {select_group_assignments_by name='task_report[group_by]' value=$group_by exclude="{$smarty.const.AssignmentFilter::GROUP_BY_PROJECT},{$smarty.const.AssignmentFilter::GROUP_BY_PROJECT_CLIENT}"}
        </div>
        <div class="criteria_run">{button type="submit" class="default"}Run{/button}</div>
      </div>
    </div>
  </form>
{else}
  <p class="empty_page"><span class="inner">{lang}You are not assigned to any project{/lang}.</span></p>
{/if}

  <div class="filter_results"></div>
</div>

<script type="text/javascript">
  $('#estimated_vs_tracked_time_report').each(function() {
    var wrapper = $(this);

    wrapper.find('form').each(function() {
      var form = $(this);

      form.find('button').click(function() {
        var data = {
          'project_id' : form.find('.criteria_project_picker option:selected').val(),
          'group_by' : form.find('.criteria_group_by option:selected').val(),
          'async' : 1
        };

        $.get(form.attr('action'), data, function(response) {
          wrapper.find('div.filter_results').html(response);
        });

        return false;
      });
    });
  });
</script>