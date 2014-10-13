{title}Move to Project{/title}
{add_bread_crumb}Move to Project{/add_bread_crumb}

<div id="move_to_project">
  {form action=$active_object->getMoveUrl() method=post}
    <div class="fields_wrapper">
      <p>{lang type=$active_object->getVerboseType(true) name=$active_object->getName() project=$active_project->getName()}You are about to move :type "<b>:name</b>" from "<b>:project</b>" project. Please select a destination project{/lang}:</p>
      
	    {wrap field=project_id}
	      {select_project name=move_to_project_id user=$logged_user class="move_to_project" exclude=$active_project->getId() show_all=true required=true label='Move to Project'}
	    {/wrap}

      {checkbox name=redirect_to_target_project checked=$redirect_to_target_project label="Redirect to selected project after moving"}
      {checkbox name="additional_params[save_anonymous_subscribers]" checked=true label="Retain anonymous subscribers"}
    </div>
    
    {wrap_buttons}
      {submit}Move to Project{/submit}
    {/wrap_buttons}
  {/form}
</div>