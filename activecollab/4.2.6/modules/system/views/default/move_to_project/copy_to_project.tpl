{title}Copy to Project{/title}
{add_bread_crumb}Copy to Project{/add_bread_crumb}

<div id="copy_to_project">
  {form action=$active_object->getCopyUrl() method=post}
    <div class="fields_wrapper">
      <p>{lang type=$active_object->getVerboseType(true) name=$active_object->getName() project=$active_project->getName()}You are about to copy :type "<b>:name</b>" from "<b>:project</b>" project. Please select destination project{/lang}:</p>
      
	    {wrap field=project_id}
	      {select_project name=copy_to_project_id value=$active_project->getId() user=$logged_user class="copy_to_project" show_all=true required=true label='Copy to Project'}
	    {/wrap}

      {checkbox name=redirect_to_target_project checked=$redirect_to_target_project label="Redirect to selected project after copying"}
    </div>
    
    {wrap_buttons}
      {submit}Copy to Project{/submit}
    {/wrap_buttons}
  {/form}
</div>
<script type="text/javascript">
  $('#copy_to_project').each(function() {
    var wrapper = $(this);
    var select_project = wrapper.find('select[name=copy_to_project_id]');
    var input_redirect_wrapper = wrapper.find('div.checkbox_wrapper');
    var current_project = select_project.val();

    input_redirect_wrapper.hide();
    select_project.change(function() {
      if (current_project !== $(this).val()) {
        input_redirect_wrapper.show();
      } else {
        input_redirect_wrapper.hide();
      } // if
    });
  });
</script>