{title}Edit Project{/title}
{add_bread_crumb}Edit Project{/add_bread_crumb}

<div id="update_project">
  {form action=$active_project->getEditUrl()}
    {include file=get_view_path('_project_form', 'project', $smarty.const.SYSTEM_MODULE)}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>