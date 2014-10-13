{title}Your Assignments Completed in the Last 30 Days{/title}
{add_bread_crumb}Recently Completed{/add_bread_crumb}

<div id="my_recently_completed_tasks">
  {if $assignments}
    <div id="my_recently_completed_tasks_inner_wrapper">
    {foreach $assignments as $assignments_group}
      {if is_foreachable($assignments_group.assignments)}
        <table class="common" cellspacing="0">
          <thead>
            <tr>
              <th colspan="4">{$assignments_group.label}</th>
            </tr>
          </thead>
          <tbody>
          {foreach $assignments_group.assignments as $assignment}
            {assign var=label_id value=$assignment.label_id}
            <tr class="assignment task">
              <td class="label right">
                {if $label_id && $labels.$label_id}
                  {render_label label=$labels.$label_id}
                {/if}
              </td>
              <td class="name">
              {if $assignment.type == 'Task'}
                <span class="object_type object_type_task">{lang task_id=$assignment.task_id}Task #:task_id{/lang}</span> <a href="{$assignment.permalink}" class="quick_view_item">{$assignment.name}</a>
              {else}
                <span class="object_type object_type_subtask">{lang}Subtask{/lang}</span> <a href="{$assignment.permalink}" class="quick_view_item">{$assignment.body}</a> {lang}in{/lang} <span class="object_type object_type_task">{lang task_id=$assignment.parent.task_id}Task #:task_id{/lang}</span> <a href="{$assignment.parent.permalink}" class="quick_view_item">{$assignment.parent.name}</a>
              {/if}
              </td>
              <td class="time right">{$assignment.completed_on|time}</td>
              <td class="project right">{$assignment.project|excerpt:25}</td>
            </tr>
          {/foreach}
          </tbody>
        </table>
      {/if}
    {/foreach}
    </div>
  {else}
    <p class="empty_page">{lang}There are no tasks assigned to you{/lang}</p>
  {/if}
</div>