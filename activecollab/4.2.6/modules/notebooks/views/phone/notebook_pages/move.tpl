{title}Move to Notebook{/title}
{add_bread_crumb}Move to Notebook{/add_bread_crumb}

<div id="move_to_notebook">
  {form action=$active_notebook_page->getMoveUrl() method=post}
    {wrap field=project_id}
      {select_notebook project=$active_project user=$logged_user name="notebook_id" label='Select Destination Notebook'}
    {/wrap}
    
    {wrap_buttons}
      {submit}Move{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
	});
</script>