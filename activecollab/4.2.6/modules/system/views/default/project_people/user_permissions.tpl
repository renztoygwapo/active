{title name=$active_user->getDisplayName()}:name's Permissions{/title}
{add_bread_crumb}Permissions{/add_bread_crumb}

<div id="user_permissions">
  {form action=$active_project->getUserPermissionsUrl($active_user)}
    {wrap_fields}
    	{select_user_project_permissions name=project_permissions role_id=$role_id permissions=$permissions label='Permissions on this Project'}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>