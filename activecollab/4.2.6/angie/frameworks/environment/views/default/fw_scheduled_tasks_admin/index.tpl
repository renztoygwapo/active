{title}Scheduled Tasks{/title}
{add_bread_crumb}Scheduled Tasks{/add_bread_crumb}

<div id="scheduled_tasks_admin" class="wireframe_content_wrapper settings_panel">
  <div class="settings_panel_header">
    <table class="settings_panel_header_cell_wrapper">
      <tr>
        <td class="settings_panel_header_cell">
          <h2>{lang}Scheduled Tasks{/lang}</h2>
			    <div class="properties">
          {foreach $scheduled_tasks as $scheduled_task_name => $scheduled_task}
            <div class="property">
              <div class="label">{$scheduled_task.text}</div>
              <div class="data">
                {if $scheduled_task.last_activity instanceof DateTimeValue}
                  {$scheduled_task.last_activity|datetime}
                {else}
                  {lang}Never executed{/lang}
                {/if}
              </div>
            </div>
          {/foreach}
			    </div>
        </td>
      </tr>
    </table>
  </div>
  
  <div class="settings_panel_body">
    {empty_slate name=scheduled_tasks module=$smarty.const.ENVIRONMENT_FRAMEWORK scheduled_tasks=$scheduled_tasks}
  </div>
</div>