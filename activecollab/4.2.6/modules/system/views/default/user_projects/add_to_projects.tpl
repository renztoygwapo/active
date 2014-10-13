{title}Add to Projects{/title}
{add_bread_crumb}Add to Projects{/add_bread_crumb}

<div id="add_user_to_projects">
  {form action=$active_user->getAddToProjectsUrl() method=post}
	  {wrap_fields}
	    <table>
	      <tr>
	        <td class="projects_list">
	          {wrap field=projects class="select_projects_add_permissions"}
              {label for=addUserToProjects}Select Projects{/label}
	            {add_user_projects_select name=projects user=$active_user required=true}
	          {/wrap}
	        </td>
	        <td class="people_permissions">
	          {wrap field=user_permissions}
	            {select_user_project_permissions name=project_permissions role_id=$default_project_role_id label='Permissions'}
	          {/wrap}
	        </td>
	      </tr>
	    </table>
	  {/wrap_fields}        
    {wrap_buttons}
      {submit}Add to Projects{/submit}
    {/wrap_buttons}
  {/form}
</div>
<script type="text/javascript">
  $('#add_user_to_projects').each(function() {
    var projects_list = $(this).find('td.projects_list li');
    if (projects_list && !projects_list.length) {
      var wrapper = $(this);
      var form  = wrapper.find('form');

      form.find('button').prop('disabled', true);
      form.find('input').prop('disabled', true);

      App.Wireframe.Flash.success('There are no more projects that this user can be added to');
      wrapper.attr('style', 'opacity: 0.6');
    } // if
  });
</script>