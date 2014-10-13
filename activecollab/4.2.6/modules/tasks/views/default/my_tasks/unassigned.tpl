{title}Unassigned Tasks{/title}
{add_bread_crumb}Discover Work{/add_bread_crumb}

<div id="unassigned_tasks">
  {if $assignments}
    <div id="unassigned_tasks_inner_wrapper">
    {foreach $assignments as $assignments_group}
      {if is_foreachable($assignments_group.assignments)}
        <table class="common" cellspacing="0">
          <thead>
            <tr>
              <th colspan="2">{$assignments_group.label}</th>
              <th class="due_on">{lang}Due Date{/lang}</th>
              <th class="age">{lang}Age{/lang}</th>
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
                <span class="object_type object_type_task">{lang task_id=$assignment.task_id}Task #:task_id{/lang}</span> <a href="{$assignment.permalink}" class="quick_view_item">{$assignment.name}</a>
              </td>
              <td class="due_on">{if $assignment.due_on}{due_on date=$assignment.due_on}{/if}</td>
              <td class="age">
                {if $assignment.age == 1}
                  {lang}One Day{/lang}
                {else}
                  {lang num=$assignment.age}:num Days{/lang}
                {/if}
              </td>
            </tr>
          {/foreach}
          </tbody>
        </table>
      {/if}
    {/foreach}
    </div>
  {else}
    <p class="empty_page">{lang}There are no unassigned tasks{/lang}</p>
  {/if}
</div>