{title}Task Segments{/title}
{add_bread_crumb}All Task Segments{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="task_segments">
  <div id="task_segments_list"></div>
  <div id="task_segment_totals">{lang}Total{/lang}: {$all_tasks} &middot; {lang}Open{/lang} {$all_open_tasks} &middot; {lang}Completed{/lang}: {$all_completed_tasks}</div>
  <div id="task_segment_empty_slate">{empty_slate name=task_segments module=$smarty.const.TASKS_MODULE}</div>
</div>

<script type="text/javascript">
  $('#task_segments_list').each(function() {
    var all_tasks = {$all_tasks|json nofilter};
    var all_open_tasks = {$all_open_tasks|json nofilter};
    var all_completed_tasks = {$all_completed_tasks|json nofilter};

    $(this).pagedObjectsList({
      'load_more_url' : '{assemble route=task_segments}',
      'items' : {$task_segments|json nofilter},
      'items_per_load' : {$task_segments_per_page},
      'total_items' : {$total_task_segments},
      'list_items_are' : 'tr',
      'list_item_attributes' : { 'class' : 'task_segment' },
      'columns' : {
        'name' : App.lang('Segment Name'),
        'total_tasks' : App.lang('Total Tasks'),
        'open_tasks' : App.lang('Open Tasks'),
        'completed_tasks' : App.lang('Completed Tasks'),
        'options' : ''
      },
      'sort_by' : 'name',
      'empty_message' : App.lang('There are no task segments defined'),
      'listen' : 'task_segment',
      'on_add_item' : function(item) {
        var task_segment = $(this);

        task_segment.append(
          '<td class="name"></td>' +
            '<td class="total_tasks tasks_count"></td>' +
            '<td class="open_tasks tasks_count"></td>' +
            '<td class="completed_tasks tasks_count"></td>' +
            '<td class="options"></td>'
        );

        task_segment.find('td.name').text(item['name']);
        task_segment.find('td.total_tasks').html(App.clean(item['total_tasks']) + ' <span class="percent_of_all" title="' + App.lang('% of All Tasks') + '">(' + App.clean(App.percentOfTotal(item['total_tasks'], all_tasks)) + ')</span>');
        task_segment.find('td.open_tasks').html(App.clean(item['open_tasks']) + ' <span class="percent_of_all" title="' + App.lang('% of All Open Tasks') + '">(' + App.clean(App.percentOfTotal(item['open_tasks'], all_open_tasks)) + ')</span>');
        task_segment.find('td.completed_tasks').html(App.clean(item['completed_tasks']) + ' <span class="percent_of_all" title="' + App.lang('% of All Completed Tasks') + '">(' + App.clean(App.percentOfTotal(item['completed_tasks'], all_completed_tasks)) + ')</span>');

        task_segment.find('td.options')
          .append('<a href="' + item['urls']['edit'] + '" class="edit_task_segment" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Edit') + '" /></a>')
        ;

        if(item['permissions']['can_delete']) {
          task_segment.find('td.options').append('<a href="' + item['urls']['delete'] + '" class="delete_task_segment" title="' + App.lang('Remove Task Segment') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Delete') + '" /></a>');
        } // if

        task_segment.find('td.options a.edit_task_segment').flyoutForm({
          'success_event' : 'task_segment_updated',
          'width' : 650
        });

        task_segment.find('td.options a.delete_task_segment').asyncLink({
          'confirmation' : App.lang('Are you sure?'),
          'success_event' : 'task_segment_deleted',
          'success_message' : App.lang('Object has been deleted successfully')
        });
      }
    });
  });
</script>