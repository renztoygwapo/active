{title}New Project Request{/title}
{add_bread_crumb}Create{/add_bread_crumb}

<div id="add_project_requqest">
  {form action=Router::assemble('project_requests_add') class='big_form' form_type = 'add'}
    {if $logged_user instanceof Client}
  	  {include file=get_view_path('_project_request_form_for_client', 'project_requests', $smarty.const.SYSTEM_MODULE)}
    {else}
      {include file=get_view_path('_project_request_form', 'project_requests', $smarty.const.SYSTEM_MODULE)}
    {/if}
  
  	{wrap_buttons}
  	  {submit}Create Project Request{/submit}
      {if $active_project_request->isNew() && !($logged_user instanceof Client)}
        <span><input type="checkbox" id="project_request_notify_client" name="project_request_notify_client"/> {label for='project_request_notify_client' after_text=""}Notify the client about this project request{/label}</span>
      {/if}
  	{/wrap_buttons}
  {/form}
</div>