{title}Update Project Request{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

<div id="update_project_request">
  {form action=$active_project_request->getEditUrl() class='big_form' form_type = 'edit'}
  	{include file=get_view_path('_project_request_form', 'project_requests', 'system')}
  
  	{wrap_buttons}
  	  {submit}Save Changes{/submit}
  	{/wrap_buttons}
  {/form}
</div>