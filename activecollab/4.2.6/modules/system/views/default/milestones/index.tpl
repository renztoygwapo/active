{title}All Milestones{/title}
{add_bread_crumb}All{/add_bread_crumb}

{if $flyout}<div class="timeline_flyout_wrapper" style="height: 450px;">{/if}
<div id="milestones_diagram"></div>
{if $flyout}</div>{/if}

<script type="text/javascript">
  $('#milestones_diagram').each(function() {
    var milestone_wrapper = $(this);

    milestone_wrapper.timelineDiagram({
      project_id : {$active_project->getId()|json nofilter},
      day_width : {$day_width|json nofilter},
      data : {$milestones|json nofilter},
      work_days : App.Config.get('work_days'),
      days_off : App.Config.get('days_off'),
      skip_days_off : true,
      images : {$diagram_images|json nofilter},
      reschedule : function (milestone, start_date, end_date) { },
      select : function (milestone, start_ate, end_date) { }
    });
  });

  // Milestones reordered
  App.Wireframe.Events.bind('milestones_reordered.content', function (event, milestones) {
    App.Wireframe.Content.reload();
  });
</script>