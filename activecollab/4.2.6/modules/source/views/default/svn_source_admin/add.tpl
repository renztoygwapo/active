{title}New SVN Repository{/title}
{add_bread_crumb}Add Repository{/add_bread_crumb}

<div id="add_svn_repository">
  {form action=Router::assemble('admin_source_svn_repositories_add') method=post}
    {include file=get_view_path('_repository_form', 'svn_source_admin', $smarty.const.SOURCE_MODULE)}
  {/form}
</div>