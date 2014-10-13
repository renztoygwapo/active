{title}New Role{/title}
{add_bread_crumb}New Role{/add_bread_crumb}

<div id="add_role">
  {form action=Router::assemble('admin_project_roles_add') method=post}
    {include file=get_view_path('_role_form', 'project_roles_admin', $smarty.const.SYSTEM_MODULE)}
    
    {wrap_buttons}
      {submit}Add Role{/submit}
    {/wrap_buttons}
  {/form}
</div>