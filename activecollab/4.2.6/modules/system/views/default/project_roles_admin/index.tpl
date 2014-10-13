{title}All Project Roles{/title}
{add_bread_crumb}All Project Roles{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="project_roles_admin"></div>

<script type="text/javascript">
  $('#project_roles_admin').pagedObjectsList({
    'load_more_url' : '{assemble route=admin_project_roles}', 
    'items' : {$project_roles|json nofilter},
    'items_per_load' : {$project_roles_per_page}, 
    'total_items' : {$total_project_roles}, 
    'list_items_are' : 'tr', 
    'list_item_attributes' : { 'class' : 'project_role' }, 
    'columns' : {
      'is_default' : '', 
      'name' : App.lang('Role Name'), 
      'options' : '' 
    }, 
    'sort_by' : 'name', 
    'empty_message' : App.lang('There are no project roles defined'), 
    'listen' : 'project_role', 
    'on_add_item' : function(item) {
      var project_role = $(this);
      
      project_role.append(
       	'<td class="is_default"></td>' + 
        '<td class="name"></td>' + 
        '<td class="options"></td>'
      );
  
      var radio = $('<input name="set_default_project_role" type="radio" value="' + item['id'] + '" />').click(function() {
        if(!project_role.is('tr.is_default')) {
          if(confirm(App.lang('Are you sure that you want to set this project role as default project role?'))) {
            var cell = radio.parent();
            
            $('#project_roles_admin td.is_default input[type=radio]').hide();
  
            cell.append('<img src="' + App.Wireframe.Utils.indicatorUrl() + '">');
  
            $.ajax({
              'url' : item['urls']['set_as_default'],
              'type' : 'post', 
              'data' : { 'submitted' : 'submitted' }, 
              'success' : function(response) {
                cell.find('img').remove();
                radio[0].checked = true;
                $('#project_roles_admin td.is_default input[type=radio]').show();
                $('#project_roles_admin tr.is_default').find('td.options a.delete_project_role').show();
                $('#project_roles_admin tr.is_default').removeClass('is_default');
  
                project_role.addClass('is_default').highlightFade();
                project_role.find('td.options a.delete_project_role').hide();
              }, 
              'error' : function(response) {
                cell.find('img').remove();
                $('#project_roles_admin td.is_default input[type=radio]').show();
  
                App.Wireframe.Flash.error('Failed to set selected project role as default');
              } 
            });
          } // if
        } // if
  
        return false;
      }).appendTo(project_role.find('td.is_default'));
  
      if(item['is_default']) {
        project_role.addClass('is_default');
        radio[0].checked = true;
      } // if
      
      project_role.find('td.name').text(item['name']);

      var project_role_options = project_role.find('td.options');
      if (item['permissions']['can_edit']) {
        project_role_options.append('<a href="' + item['urls']['edit'] + '" class="edit_project_role" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Edit') + '" /></a>');
      } // if

      if (item['permissions']['can_delete'] && !item['is_default']) {
        project_role_options.append('<a href="' + item['urls']['delete'] + '" class="delete_project_role" title="' + App.lang('Remove Project Role') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Delete') + '" /></a>');
      } // if
      
      project_role.find('td.options a.edit_project_role').flyoutForm({
        'success_event' : 'project_role_updated'
      });
      
      project_role.find('td.options a.delete_project_role').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this project role?'), 
        'success_event' : 'project_role_deleted', 
        'success_message' : App.lang('Project role has been deleted successfully')
      });
    }
  });
</script>