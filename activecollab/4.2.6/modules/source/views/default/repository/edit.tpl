{title}Edit repository{/title}
{add_bread_crumb}Edit repository{/add_bread_crumb}
{use_widget name="repository_form" module="source"}

<div id="repository_edit">
  {form action=$project_object_repository->getEditurl() method=post ask_on_leave=yes autofocus=yes}
    {include file=get_view_path('_repository_form', 'repository', 'source')}  
  {/form}
</div>

<script type="text/javascript">
  App.widgets.RepositoryForm.init('repository_edit');
</script>