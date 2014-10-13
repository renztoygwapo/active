{title}Edit Project{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

<div id="edit_project">
  {form action=$active_project->getEditUrl() method=post class=big_form}
    {include file=get_view_path('_project_form', 'project', 'system')}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>