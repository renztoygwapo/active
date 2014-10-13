{wrap_fields}
	{wrap field=name}
		{text_field name='position[name]' value=$object_data.name id=positionName class='title required validate_minlength 3' required=true label="Name" maxlength="150"}
	{/wrap}

	{wrap field=assignee}
		{select_position_template_user name="position[user_id]" value=$object_data.user_id user=$logged_user template=$active_template object=$active_object label='Default Assignee'}
	{/wrap}

	{wrap field=user_permissions}
		{select_user_project_permissions name="position[project_template_permissions]" role_id=$object_data.project_template_permissions.role_id permissions=$object_data.project_template_permissions.permissions label='Permissions for this position'}
	{/wrap}
{/wrap_fields}