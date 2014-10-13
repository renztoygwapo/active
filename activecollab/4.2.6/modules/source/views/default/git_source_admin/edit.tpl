{title}Update Repository{/title}
{add_bread_crumb}Update Repository{/add_bread_crumb}

<div id="update_git_repository">
  {form action=$active_repository->getEditUrl() method=post}
    {assign var=form_mode value='edit'}
    {include file=get_view_path('_repository_form', 'git_source_admin', $smarty.const.SOURCE_MODULE)}
  {/form}
</div>