{title}Add Repository{/title}
{add_bread_crumb}Add Repository{/add_bread_crumb}
{use_widget name="repository_form" module="source"}
  
<div id="repository_create">
	{form action=$repository_add_url method=post ask_on_leave=yes autofocus=yes}
	 {include file=get_view_path('_repository_form', 'repository', 'source')}
	{/form}
</div>

<script type="text/javascript">
  App.widgets.RepositoryForm.init('repository_create');
</script>