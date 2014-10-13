{title}Update Role{/title}
{add_bread_crumb}Update Role{/add_bread_crumb}

<div id="edit_role">
  {form action=$active_role->getEditUrl() method=post}
    {include file=get_view_path('_role_form', 'project_roles_admin', $smarty.const.SYSTEM_MODULE)}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>