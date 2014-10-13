{title}All Projects{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="projects_diagram"></div>

<script type="text/javascript">
	$('#projects_diagram').each(function() {
		var projects_wrapper = $(this);

		projects_wrapper.projectsTimelineDiagram({
			project_id : {$active_project->getId()|json nofilter},
			day_width : {$day_width|json nofilter},
			data : {$projects|json nofilter},
			work_days : App.Config.get('work_days'),
			days_off : App.Config.get('days_off'),
			skip_days_off : true,
			images : {$diagram_images|json nofilter},
			reschedule : function (project, start_date, end_date) { },
			// @petar proveriti da li je greskom napisano "start_ate"
			select : function (project, start_ate, end_date) { }
		});
	});

	// Milestones reordered
	App.Wireframe.Events.bind('projects_reordered.content', function (event, milestones) {
		App.Wireframe.Content.reload();
	});
</script>