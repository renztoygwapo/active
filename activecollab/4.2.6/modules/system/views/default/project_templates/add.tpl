{title}New Template{/title}
{add_bread_crumb}New Template{/add_bread_crumb}

{form action=$add_template_url method=post enctype="multipart/form-data" ask_on_leave=yes autofocus=yes id='project_template_form'}
	{include file=get_view_path('_template_form', 'project_templates', $smarty.const.SYSTEM_MODULE)}

	{wrap_buttons}
		{submit}Add Template{/submit}
	{/wrap_buttons}
{/form}