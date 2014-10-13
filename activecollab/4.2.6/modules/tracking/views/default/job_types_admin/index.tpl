{title}Job Types & Hourly Rates{/title}
{add_bread_crumb}All{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

{if $request->get('flyout')}
  <script type="text/javascript">
    App.widgets.FlyoutDialog.front().addButton('add_job_type_form', {$wireframe->actions->get('add_job_type_form')|json nofilter});
  </script>
{/if}

<div id="job_types"></div>

<script type="text/javascript">
  var flyout_id = {$request->get('flyout')|json nofilter};

  $('#job_types').pagedObjectsList({
    'load_more_url' : '{assemble route=job_types_admin}', 
    'items' : {$job_types|json nofilter},
    'items_per_load' : {$job_types_per_page}, 
    'total_items' : {$total_job_types}, 
    'list_items_are' : 'tr', 
    'list_item_attributes' : { 'class' : 'job_type' },
    'class' : 'admin_list',
    'columns' : {
      'is_default' : '', 
      'name' : App.lang('Job Type'), 
      'default_hourly_rate' : App.lang('Default Hourly Rate'), 
      'options' : '' 
    },
    'empty_message' : App.lang('There are no job types defined'),
    'listen' : {
      'create' : 'job_type_created',
      'update' : 'job_type_updated job_type_archived job_type_unarchived',
      'delete' : 'job_type_deleted'
    },
    'listen_scope' : flyout_id ? flyout_id : 'content',
    'on_add_item' : function(item) {
      var job_type = $(this);
      
      job_type.append(
       	'<td class="is_default"></td>' + 
        '<td class="name"></td>' + 
        '<td class="default_hourly_rate"></td>' + 
        '<td class="options"></td>'
      );

      var radio = $('<input name="set_default_job_type" type="radio" value="' + item['id'] + '" />');

      if(item['is_active']) {
        radio.click(function() {
          if(!job_type.is('tr.is_default')) {
            if(confirm(App.lang('Are you sure that you want to set this job type as default job type?'))) {
              var cell = radio.parent();

              $('#job_types td.is_default input[type=radio]').hide();

              cell.append('<img src="' + App.Wireframe.Utils.indicatorUrl() + '">');

              $.ajax({
                'url' : item['urls']['set_as_default'],
                'type' : 'post',
                'data' : { 'submitted' : 'submitted' },
                'success' : function(response) {
                  cell.find('img').remove();
                  radio[0].checked = true;

                  $('#job_types td.is_default input[type=radio]').show();
                  $('#job_types tr.is_default').find('td.options a.delete_job_type').show();
                  $('#job_types tr.is_default').removeClass('is_default');

                  job_type.addClass('is_default').highlightFade();
                  job_type.find('td.options a.delete_job_type').hide();

                  App.Wireframe.Flash.success('Default job type has been changed successfully');
                },
                'error' : function(response) {
                  cell.find('img').remove();
                  $('#job_types td.is_default input[type=radio]').show();

                  App.Wireframe.Flash.error('Failed to set selected job type as default');
                }
              });
            } // if
          } // if

          return false;
        }).appendTo(job_type.find('td.is_default'));
      } // if

      if(!item['is_active']) {
        job_type.addClass('archived');
      } // if

      if(item['is_default']) {
        job_type.addClass('is_default');
        radio[0].checked = true;
      } // if
      
      job_type.find('td.name').html('<a href="' + item['urls']['view'] + '">' + App.clean(item['name']) + '</a>');
      job_type.find('td.default_hourly_rate').html(App.numberFormat(item['default_hourly_rate']));

      var options_cell = job_type.find('td.options');

      // Edit
      options_cell.append('<a href="' + item['urls']['edit'] + '" class="edit_job_type" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Edit') + '" /></a>').find('a.edit_job_type').flyoutForm({
        'success_event' : 'job_type_updated',
        'width' : 550
      });

      // Archiving
      if(item['is_active']) {
        options_cell.append('<a href="' + item['urls']['archive'] + '" class="archive_job_type" title="' + App.lang('Archive Job Type') + '"><img src="{image_url name="icons/12x12/archive.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Archive') + '" /></a>').find('a.archive_job_type').flyoutForm({
          'success_event' : 'job_type_archived',
          'success_message' : App.lang('Job type has been archived successfully'),
          'width' : 400
        });
      } else {
        options_cell.append('<a href="' + item['urls']['unarchive'] + '" class="unarchive_job_type" title="' + App.lang('Unarchive Job Type') + '"><img src="{image_url name="icons/12x12/unarchive.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Unarchive') + '" /></a>').find('a.unarchive_job_type').asyncLink({
          'confirmation' : App.lang('Are you sure that you want to unarchive this job type?'),
          'success_event' : 'job_type_unarchived',
          'success_message' : App.lang('Job type has been unarchived successfully')
        });
      } // if

      // Delete
      if(item['permissions']['can_delete']) {
        options_cell.append('<a href="' + item['urls']['delete'] + '" class="delete_job_type" title="' + App.lang('Remove Job Type') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Delete') + '" /></a>').find('a.delete_job_type').asyncLink({
          'confirmation' : App.lang('Are you sure that you want to permanently delete this job type?'),
          'success_event' : 'job_type_deleted', 
          'success_message' : App.lang('Job type has been deleted successfully')
        });
      } // if
    }
  });
</script>