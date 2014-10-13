{title}Projects Data Cleanup{/title}
{add_bread_crumb}Projects Data Cleanup{/add_bread_crumb}

{use_widget name="paged_objects_list" module="environment"}

<div id="projects_data_cleanup">
  <table id="projects_data_cleanup_sections" cellspacing="0" cellpadding="0">
    <tr class="projects_data_cleanup_section">
      <td class="section_content">
        <h2>{lang}Permanently Delete Projects{/lang}</h2>
        <div id="permanently_delete_projects"></div>
      </td>
    </tr>
  </table>
</div>

<script type="text/javascript">
  $('#permanently_delete_projects').pagedObjectsList({
    'load_more_url' : '{assemble route=admin_projects_data_cleanup}',
    'items' : {$projects|json nofilter},
    'items_per_load' : {$projects_per_page|json nofilter},
    'total_items' : {$total_projects|json nofilter},
    'list_items_are' : 'tr',
    'list_item_attributes' : { 'class' : 'project' },
    'class' : 'admin_list',
    'columns' : {
      'name' : App.lang('Project'),
      'status' : App.lang('Status'),
      'disk_usage' : App.lang('Disk Usage'),
      'options' : ''
    },
    'empty_message' : App.lang('There are no archived or deleted projects'),
    'listen' : 'project',
    'on_add_item' : function(item) {
      var project = $(this);

      project.append(
        '<td class="name"></td>' +
        '<td class="status"></td>' +
        '<td class="disk_usage"></td>' +
        '<td class="options"></td>'
      );

      var name;
      var state;

      if(item['state'] == {$smarty.const.STATE_DELETED}) {
        name = App.clean(item['name']);
        state = App.lang('Deleted');
      } else {
        name = '<a href="' + item['urls']['view'] + '" class="quick_view_item" quick_view_url="' + item['urls']['view'] + '">' + App.clean(item['name']) + '</a>';
        state = App.lang('Archived');
      } // if

      project.find('td.name').html(name);
      project.find('td.status').html(state);
      project.find('td.disk_usage').html(App.formatFileSize(item['disk_usage']));

      var options_cell = project.find('td.options');

      // Export project
      if({AngieApplication::isModuleLoaded('project_exporter')|json} && item['state'] != {$smarty.const.STATE_DELETED}) {
        options_cell.append('<a href="' + item['urls']['export'] + '" class="export_project" title="' + App.lang('Export') + '"><img src="{image_url name="icons/12x12/export-project.png" module=$smarty.const.PROJECT_EXPORTER_MODULE}" alt="' + App.lang('Project Exporter') + '" /></a>').find('a.export_project').flyoutForm({
          'success_event' : 'project_exported',
          'width' : 'narrow'
        });
      } // if

      // Permanently delete
      options_cell.append('<a href="' + item['urls']['permanently_delete'] + '" class="permanently_delete_project" title="' + App.lang('Permanently Delete') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Permanently Delete') + '" /></a>').find('a.permanently_delete_project').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this project?'),
        'success_event' : 'project_deleted',
        'success_message' : App.lang('Project has been permanently deleted')
      });
    }
  });
</script>