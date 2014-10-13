<div class="day_overview">
  {if is_foreachable($widget_data.due_today)}
    {if !$widget_data.is_overview_in_past}
      <!-- Project-->
      {foreach $widget_data.due_today as $due_today}
      <table class="common older" cellspacing="0">
        <tr>
          <th class="project" colspan="4">{project_link project=$due_today['project']}</th>
        </tr>
        {if is_foreachable($due_today.objects_active)}
          <!-- Active objects -->
          {foreach $due_today.objects_active as $active_object}
          <tr>
            <td class="due_object_properties">
              <span>
                {render_priority mode='image' priority_id=$active_object->getPriority()}
                {if $active_object instanceof ILabel}
                  {object_label short=true object=$active_object}
                {/if}
              </span>
            </td>
            <td class="object_name">
              {object_link object=$active_object quick_view=true}
              {if $widget_data.selected_user->getId() !== $active_object->getAssigneeId()}
                ({lang}Delegated to{/lang} {user_link user=$active_object->assignees()->getAssignee()})
              {/if}
            </td>
            <td class="object_type">
                <span class="object_type inverse object_type_{$active_object->getBaseTypeName()}">{$active_object->getVerboseType()}</span>
            </td>
            <td class="due">
              {if !$widget_data.is_overview_in_future}{due_on object=$active_object}{/if}
            </td>
          </tr>
          {/foreach}
        {/if}
        <!-- Completed objects -->
        {if is_foreachable($due_today.objects_completed)}
          <tr>
            <td class="object_name" colspan="3"> Completed:
          {foreach $due_today.objects_completed as $key => $completed_object}
            {object_link object=$completed_object quick_view=true}{if isset($due_today.objects_completed[$key+1])}, {/if}
          {/foreach}
            </td>
          </tr>
        {/if}
      </table>
      {/foreach}
    {else}
      <table class="common older" cellspacing="0">
        <tr>
          <th class="project" colspan="5">{lang}Late{/lang}</th>
        </tr>
      {if is_foreachable($widget_data.due_today.objects_active)}
        {foreach $widget_data.due_today.objects_active as $active_object}
        <tr>
          <td class="due_object_properties">
            <span>
              {render_priority mode='image' priority_id=$active_object->getPriority()}
              {if $active_object instanceof ILabel}
                {object_label short=true object=$active_object}
              {/if}
            </span>
          </td>
          <td class="object_name">
            {object_link object=$active_object quick_view=true} {lang}in{/lang} {project_link project=$widget_data.projects[$active_object->getProjectId()]}
            {if $widget_data.selected_user->getId() !== $active_object->getAssigneeId()}
              ({lang}Delegated to{/lang} {user_link user=$active_object->assignees()->getAssignee()})
            {/if}
          </td>
          <td class="object_type">
              <span class="object_type inverse object_type_{$active_object->getBaseTypeName()}">{$active_object->getVerboseType()}</span>
          </td>
          <td class="due">
            {if !$widget_data.is_overview_in_future}{due_on object=$active_object}{/if}
          </td>
        </tr>
        {/foreach}
      {else}
        <tr><td>{lang}No tasks are due on this day{/lang}</td></tr>
      {/if}
      </table>
      {if is_foreachable($widget_data.due_today.objects_completed)}
      <table class="common older" cellspacing="0">
        <tr>
          <th class="project" colspan="2">{lang}Completed{/lang}</th>
        </tr>
        {foreach $widget_data.due_today.objects_completed as $completed_object}
          <tr>
            <td class="object_type">
              <span class="object_type inverse object_type_{$completed_object->getBaseTypeName()}">{$completed_object->getVerboseType()}</span>
            </td>
            <td class="object_name">
              {object_link object=$completed_object quick_view=true} in {project_link project=$widget_data.projects[$completed_object->getProjectid()]}
            </td>
          </tr>
        {/foreach}
      </table>
      {/if}
    {/if}
  {else}
    <p>{lang}No tasks are due on this day{/lang}</p>
  {/if}

  {if !$widget_data.is_overview_in_future && $widget_data.timetracking_available}
  <table class="common day_overview_tracking" cellspacing="0">
    <tr>
      <th>{lang}Time Records{/lang}</th>
      <th>{lang}Expenses{/lang}</th>
    </tr>
    <tr>
      <td>{$widget_data.total_time} {lang}hours{/lang}</td>
      <td>
      {if is_foreachable($widget_data.total_expenses)}
        {foreach $widget_data.total_expenses as $currency_id => $total_expense}
        <b>{$total_expense} {$widget_data.currencies_map[$currency_id]['code']}{if !$total_expense@last},{/if}</b>
        {/foreach}
      {else}
        {lang}No expenses have been logged{/lang}
      {/if}
      </td>
    </tr>
  </table>
  {/if}
</div>