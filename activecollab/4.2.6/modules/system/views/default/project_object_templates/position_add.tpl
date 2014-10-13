{form action=$add_object_url method=post enctype="multipart/form-data" ask_on_leave=yes autofocus=yes id='position_template_form'}
	{include file=get_view_path('_position_template_form', 'project_object_templates', $smarty.const.SYSTEM_MODULE)}

	{wrap_buttons}
		{submit}Add Position{/submit}
	{/wrap_buttons}
{/form}