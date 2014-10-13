{title}Convert to a Template{/title}
{add_bread_crumb}Convert to a Template{/add_bread_crumb}

<div id="convert_to_a_template">
	{form action=$active_project->getConvertToATemplateUrl()}
		{wrap_fields}
			{wrap field=name}
				{text_field name="project_template[name]" value=$project_template_data.name class="title required validate_minlength 3" label="Name" required=true maxlength="150"}
			{/wrap}

			{wrap field=milestones}
				{label for=milestones}Milestone List{/label}
				{project_milestones_list project=$active_project user=$logged_user}
			{/wrap}

			{wrap field=users}
				{label for=users}Positions{/label}
				{project_users_positions_list name="project_template[positions]" project=$active_project}
			{/wrap}
		{/wrap_fields}

		{wrap_buttons}
			{submit}Convert{/submit}
		{/wrap_buttons}
	{/form}
</div>

<script type="text/javascript">
	$('#convert_to_a_template').each(function() {
		var wrapper = $(this);

		var date_format = "{$logged_user->getDateFormat()}";

		wrapper.on('change', '.recalculate_days', function() {
			var start_date = new Date($(this).val());
			var milestone_dates = wrapper.find('span.milestone_date');
			$.each(milestone_dates, function() {
				var days_between = parseInt($(this).attr('data-days-between'));
				var new_start_date = start_date.clone().addDays(days_between);
				$(this).text(new_start_date.toString('yyyy/MM/dd'));
			});
		});
	});

	// what happens when template is created
	App.Wireframe.Events.bind('template_created.content', function(event, template) {
		if (template['class'] == 'ProjectTemplate') {
			App.Wireframe.Content.setFromUrl(template['urls']['view']);
		} // if
	});
</script>