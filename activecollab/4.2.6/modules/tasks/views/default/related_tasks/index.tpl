<div id="related_tasks" task_signature="{$active_project->getId()}-{$active_task->getTaskId()}">
  {if $can_manage_related_tasks}
  <form action="{$active_task->relatedTasks()->getAddTaskUrl()}" method="post" class="add_related_task">
  {/if}
    <table id="related_tasks_table" cellpadding="0" cellspacing="0" class="common">
      <thead>
        <tr>
          <th class="project">{lang}Project{/lang}</th>
          <th class="task_id">{lang}Task{/lang}</th>
          <th class="relation_note">{lang}Note{/lang} ({lang}optional{/lang})</th>
          <th class="options"></th>
        </tr>
      </thead>
      <tbody>
      {if $can_manage_related_tasks}
        <tr class="add_related_task highlighted">
          <td class="project">{select_project name='related_task[project_id]' value=$active_project->getId() user=$logged_user}</td>
          <td class="task_id"><span class="hash">#</span>{number_field name='related_task[task_id]' placeholder='ID'}</td>
          <td class="relation_note"><input type="text" placeholder="{lang}How is this task related?{/lang}"></td>
          <td class="options">{button type='submit' class='default'}Add{/button}</td>
        </tr>
      {/if}
      </tbody>
    </table>
  {if $can_manage_related_tasks}
  </form>
  {/if}
</div>

<script type="text/javascript">
  $('#related_tasks').each(function() {
    var wrapper = $(this);
    var tasks_table = wrapper.find('#related_tasks_table');

    /**
     * Refresh tasks table
     * @param task
     */
    var refresh_tasks_table = function(task) {
      tasks_table.find('tr.no_related_tasks').remove();
      tasks_table.find('tr.related_task').remove();

      if(App.isForeachable(task['related_tasks'])) {
        $.each(task['related_tasks'], function(index, related_task) {
          var row = $('<tr class="related_task" task_signature="' + related_task['project_id'] + '-' + related_task['task_id'] + '">' +
            '<td class="project"></td>' +
            '<td class="task_name" colspan="2"></td>' +
            '<td class="options"></td>' +
          '</tr>').appendTo(tasks_table.find('tbody'));

          if(related_task['project_id'] == {$active_project->getId()}) {
            row.addClass('same_project').find('td.project').text(App.lang('This Project'));
          } else {
            row.find('td.project').text(related_task['project_name']);
          } // if

          if(typeof(related_task['is_completed']) == 'object' && related_task['is_completed']) {
            var task_link = '<a href="' + App.clean(related_task['url']) + '" class="quick_view_item">#' + related_task['task_id'] + ': ' + '<del>' + App.clean(related_task['name']) + '</del></a>';
          } else {
            var task_link = '<a href="' + App.clean(related_task['url']) + '" class="quick_view_item">#' + related_task['task_id'] + ': ' + App.clean(related_task['name']) + '</a>';
          } // if

          row.find('td.task_name').append('<span class="task_link">' + task_link + '</span>');

          if(typeof(related_task['note']) == 'string' && related_task['note']) {
            row.find('td.task_name').append(' &mdash; <span class="relation_note">' + App.clean(related_task['note']) + '</span>');
          } // if

        {if $can_manage_related_tasks}
          row.find('td.options').append('<a href="' + {$remove_related_task_url|json nofilter}.replace('--TASK-ID--', related_task['id']) + '" class="remove_related_task"><img src="' + App.Wireframe.Utils.imageUrl('/icons/12x12/delete.png', 'environment') + '"></a>');
        {/if}
        });
      } else {
        tasks_table.find('tbody').append('<tr class="no_related_tasks"><td colspan="4">' + App.lang('There are no related tasks') + '</td></tr>');
      } // if
    }; // refresh_tasks_table

    refresh_tasks_table({$active_task|json:$logged_user:true nofilter});

    App.Wireframe.Events.bind('task_updated.flyout', function (event, task) {
      refresh_tasks_table(task);
    });

    wrapper.find('form.add_related_task').each(function() {
      var add_related_task_form = $(this);

      var project_id_select = add_related_task_form.find('table tr.add_related_task td.project select');
      var task_id_input = add_related_task_form.find('table tr.add_related_task td.task_id input');
      var relation_note_input = add_related_task_form.find('table tr.add_related_task td.relation_note input');

      add_related_task_form.submit(function() {
        var task_id = parseInt(jQuery.trim(task_id_input.val()));
        var project_id = parseInt(project_id_select.val());

        // Validate input values
        if(isNaN(project_id) || project_id < 1) {
          App.Wireframe.Flash.error('Please select project');
          return false;
        } // if

        if(isNaN(task_id) || task_id < 1) {
          App.Wireframe.Flash.error('Please insert task #');
          task_id_input.focus();

          return false;
        } // if

        // Check if we are trying to add self
        var task_signature = project_id + '-' + task_id;

        if(wrapper.attr('task_signature') == task_signature) {
          App.Wireframe.Flash.error("Can't add self");
          return false;
        } // if

        // Check if we already have selected task added
        var already_added_task = tasks_table.find('tr.related_task[task_signature=' + task_signature + ']');

        if(already_added_task.length > 0) {
          already_added_task.find('td').highlightFade();
          return false;
        } // if

        var button_cell = wrapper.find('tr.add_related_task td.options');

        project_id_select.prop('disabled', true);
        task_id_input.prop('disabled', true);
        relation_note_input.prop('disabled', true);
        button_cell.find('button').hide();
        button_cell.append('<img src="' + App.Wireframe.Utils.indicatorUrl() + '">');

        /**
         * Enable form elements
         *
         * @param boolean clear
         */
        var enable_form_elements = function(clear) {
          project_id_select.prop('disabled', false);
          task_id_input.prop('disabled', false);
          relation_note_input.prop('disabled', false);
          button_cell.find('img').remove();
          button_cell.find('button').show();

          if(clear) {
            task_id_input.val('').focus();
            relation_note_input.val('');
          } // if
        }; // enable_form_elements

        $.ajax({
          'url' : add_related_task_form.attr('action'),
          'type' : 'post',
          'data' : {
            'related_task_project_id' : project_id,
            'related_task_id' : task_id,
            'relation_note' : jQuery.trim(relation_note_input.val()),
            'submitted' : 'submitted'
          },
          'success' : function(task, e) {
            if(typeof(task) == 'object' && task && typeof(task['class']) == 'string' && task['class'] == 'Task') {
              App.Wireframe.Events.trigger('task_updated', task);
            } else {
              App.Wireframe.Flash.error('Failed to add related task. Please try again later');
            } // if

            enable_form_elements(true);
          },
          'error' : function(r, e) {
            App.Wireframe.Flash.error('Failed to add related task. Please try again later');

            enable_form_elements();
          }
        });

        return false;
      });

      // Remove task
      tasks_table.delegate('a.remove_related_task', 'click', function(event) {
        App.Delegates.asyncLinkClick.apply(this, [event, {
          'confirmation' : App.lang('Are you sure that you want to remove selected task from the list of related tasks?'),
          'success_event' : 'task_updated'
        }]);

        return false;
      });

      // Focus task # field
      task_id_input.focus();
    });
  });
</script>