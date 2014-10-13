{title}New GIT Repository{/title}
{add_bread_crumb}Add Repository{/add_bread_crumb}

<div id="add_git_repository">
  {form action=Router::assemble('admin_source_git_repositories_add') method=post}
    {include file=get_view_path('_repository_form', 'git_source_admin', $smarty.const.SOURCE_MODULE)}
  {/form}
</div>