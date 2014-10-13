<div class="my_tasks_wrapper" id="{$id}" data-task-complete-url="{$urls.task_complete_url}" data-task-reopen-url="{$urls.task_reopen_url}" data-subtask-complete-url="{$urls.subtask_complete_url}" data-subtask-reopen-url="{$urls.subtask_complete_url}" data-can-use-tracking="{if AngieApplication::isModuleLoaded('tracking')}1{else}0{/if}" {if isset($urls.task_tracking_url)}data-task-tracking-url="{$urls.task_tracking_url}"{/if} data-refresh-url="{$urls.refresh}" data-user-id="{$user_id}" data-auto-show-per-group="15" data-refresh-counter="0">

  <div class="my_late_tasks" {if empty($late_assignments)}style="display: none"{/if}>
    <div class="my_late_tasks_inner_wrapper">
      <h2>{lang}Late or Due Today{/lang}</h2>
      <ul>
      {if $late_assignments}
        {foreach $late_assignments as $assignment}
          {if ($assignment.due_on && $assignment.due_on < time()) && ($assignment.assignee_id == $user_id || (is_array($assignment.other_assignees) && in_array($user_id, $assignment.other_assignees)))}
            <li class="assignment task"><span class="object_type object_type_task">{lang task_id=$assignment.task_id}Task #:task_id{/lang}</span> <a href="{$assignment.permalink}" class="quick_view_item">{$assignment.name}</a> &middot; {due_on date=$assignment.due_on}</li>
          {/if}

          {if $assignment.subtasks}
            {foreach $assignment.subtasks as $subtask}
              <li class="assignment subtask"><span class="object_type object_type_subtask">{lang}Subtask{/lang}</span> <a href="{$subtask.permalink}" class="quick_view_item">{$subtask.body}</a> {lang}in{/lang} <span class="object_type object_type_task">{lang task_id=$assignment.task_id}Task #:task_id{/lang}</span> <a href="{$assignment.permalink}" class="quick_view_item">{$assignment.name}</a> &middot; {due_on date=$subtask.due_on}</li>
            {/foreach}
          {/if}
        {/foreach}
      {/if}
      </ul>
    </div>
  </div>

  <div class="my_tasks">
    <div class="my_tasks_inner_wrapper">
    {if $assignments}
      {foreach $assignments as $assignment_group_name => $assignment_group}
        <table data-group-name="{$assignment_group_name}" data-showing-more="0" class="common assignment_group" cellspacing="0">
          <thead>
            <tr>
              <th class="group_name" colspan="2">
              {if $assignment_group.url}
                <a href="{$assignment_group.url}" class="quick_view_item">{$assignment_group.label}</a>
              {else}
                {$assignment_group.label}
              {/if}
              </th>
              <th class="right"><a href="#" class="toggle_group">{lang}Hide{/lang}</a></th>
            </tr>
          </thead>

          <tbody>
          {foreach $assignment_group.assignments as $assignment}
            {assign_var name=assignment_url_replacements}{$assignment.project_id},{$assignment.task_id}{/assign_var}

            {if $assignment.assignee_id == $user_id || (is_array($assignment.other_assignees) && in_array($user_id, $assignment.other_assignees))}
              {assign var=label_id value=$assignment.label_id}

              <tr class="assignment task" data-task-id="{$assignment.id}">
                <td class="label right">
                  {if $assignment.priority}
                    {render_priority mode='image' priority_id=$assignment.priority}
                  {/if}

                  {if $label_id && $labels.$label_id}
                    {render_label label=$labels.$label_id}
                  {/if}
                </td>
                <td class="name">
                  <span class="my_tasks_name_element checkbox"><img src="{image_url name='icons/12x12/checkbox-unchecked.png' module=$smarty.const.COMPLETE_FRAMEWORK}" title="{lang}Click to Complete{/lang}" data-is-completed="0" data-complete-url="{replace search='--PROJECT-SLUG--,--TASK-ID--' replacement=$assignment_url_replacements in=$urls.task_complete_url explode=','}" data-reopen-url="{replace search='--PROJECT-SLUG--,--TASK-ID--' replacement=$assignment_url_replacements in=$urls.task_reopen_url explode=','}"></span>
                  <span class="my_tasks_name_element object_type object_type_task">{lang task_id=$assignment.task_id}Task #:task_id{/lang}</span>
                {if $assignment.assignee_id != $user_id}
                  <span class="my_tasks_name_element someone_else_is_responsible" title="{lang}You are assigned to this task{/lang}. {lang name=$assignment.assignee}:name is responsible{/lang}">{$assignment.assignee}</span>
                {/if}
                  <span class="my_tasks_name_element assignment_name"><a href="{$assignment.permalink}" class="quick_view_item">{$assignment.name}</a></span>
                </td>
                <td class="options right">
                  {if $assignment.due_on instanceof DateValue}
                    {due_on date=$assignment.due_on id='due_date_for_assignment_'|cat:$assignment.id}
                  {/if}

                {if AngieApplication::isModuleLoaded('tracking')}
                  <span class="object_tracking" id="{$id}_object_time_for_{$assignment.id}" data-estimated-time="{$assignment.estimated_time}" data-object-time="{$assignment.tracked_time}" data-object-expenses="0" data-show-label="0"><a href="{replace search='--PROJECT-SLUG--,--TASK-ID--' replacement=$assignment_url_replacements in=$urls.task_tracking_url explode=','}"><img src="{image_url name='icons/12x12/object-time-inactive.png' module=$smarty.const.TRACKING_MODULE interface=$smarty.const.AngieApplication::INTERFACE_DEFAULT}"></a></span>
                {/if}
                </td>
              </tr>
            {/if}

            {if $assignment.subtasks}
              {foreach $assignment.subtasks as $subtask}
                {assign var=label_id value=$subtask.label_id}
                {assign_var name=subtask_url_replacements}{$assignment.project_id},{$assignment.task_id},{$subtask.id}{/assign_var}

                <tr class="assignment subtask" data-task-id="{$subtask.parent_id}" data-subtask-id="{$subtask.id}">
                  <td class="label right">
                    {if $subtask.priority}
                      {render_priority mode='image' priority_id=$subtask.priority}
                    {/if}

                    {if $label_id && $labels.$label_id}
                      {render_label label=$labels.$label_id}
                    {/if}
                  </td>
                  <td class="name">
                    <span class="my_tasks_name_element checkbox"><img src="{image_url name='icons/12x12/checkbox-unchecked.png' module=$smarty.const.COMPLETE_FRAMEWORK}" title="{lang}Click to Complete{/lang}" data-is-completed="0" data-complete-url="{replace search='--PROJECT-SLUG--,--TASK-ID--,--SUBTASK-ID--' replacement=$subtask_url_replacements in=$urls.subtask_complete_url explode=','}" data-reopen-url="{replace search='--PROJECT-SLUG--,--TASK-ID--,--SUBTASK-ID--' replacement=$subtask_url_replacements in=$urls.subtask_reopen_url explode=','}"></span>
                    <span class="my_tasks_name_element object_type object_type_subtask">{lang}Subtask{/lang}</span>
                    <span class="my_tasks_name_element assignment_name"><a href="{$subtask.permalink}" class="quick_view_item">{$subtask.body}</a> {lang}in{/lang} <span class="object_type object_type_task">{lang task_id=$assignment.task_id}Task #:task_id{/lang}</span> <a href="{$assignment.permalink}" class="quick_view_item">{$assignment.name}</a></span>
                  </td>
                  <td class="options right">
                  {if $subtask.due_on instanceof DateValue}
                    {due_on date=$subtask.due_on}
                  {/if}

                  {if AngieApplication::isModuleLoaded('tracking')}
                    <span class="object_tracking" id="{$id}_object_time_for_{$assignment.id}_and_{$subtask.id}" data-estimated-time="{$assignment.estimated_time}" data-object-time="{$assignment.tracked_time}" data-object-expenses="0" data-show-label="0"><a href="{replace search='--PROJECT-SLUG--,--TASK-ID--' replacement=$assignment_url_replacements in=$urls.task_tracking_url explode=','}"><img src="{image_url name='icons/12x12/object-time-inactive.png' module=$smarty.const.TRACKING_MODULE interface=$smarty.const.AngieApplication::INTERFACE_DEFAULT}"></a></span>
                  {/if}
                  </td>
                </tr>
              {/foreach}
            {/if}
          {/foreach}
          </tbody>
        </table>
      {/foreach}
    {/if}

      <p class="empty_page" {if $assignments}style="display: none"{/if}><span>{lang}There are no open tasks assigned to you{/lang}.</span><br><br>{lang}Use the <b>Discover Work</b> option below to find what you can work on{/lang}.</p>
      <p class="my_tasks_more center">{lang}History{/lang}: <a href="{assemble route=my_tasks_completed}">{lang}Recently Completed{/lang}</a> &middot; {lang}Discover more work{/lang}: <a href="{assemble route=my_tasks_unassigned}">{lang}Unassigned Tasks{/lang}</a></p>
    </div>
  </div>
</div>

<script type="text/javascript">
  $('#{$id}').myTasks();
</script>