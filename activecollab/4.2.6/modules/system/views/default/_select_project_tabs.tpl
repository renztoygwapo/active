{use_widget name="select_project_tabs" module="system"}

<div id="{$_select_project_tabs_id}" class="select_project_tabs">
  <table>
    <tr>
      <td>
        <p>{lang}Enabled Tabs{/lang}:</p>
        <ul id="{$_select_project_tabs_id}_selected">
        {foreach $_select_project_tabs_value as $value}
          {if $value == '-'}
            <li class="separator sortable">
              <input type="checkbox" name="{$_select_project_tabs_name}[]" value="-" class="inline" checked="checked" /> ------------------------
            </li>
          {else}
            <li class="tab sortable">
              <input type="checkbox" name="{$_select_project_tabs_name}[]" value="{$value}" class="inline" checked="checked" /> {$_select_project_tabs[$value]}
            </li>
          {/if}
        {/foreach}
        </ul>
        
        <p><a href="#" class="button_add" id="{$_select_project_tabs_id}_add_separator">{lang}Add Separator{/lang}</a></p>
      </td>
      <td>
        <div id="{$_select_project_tabs_id}_available_wrapper">
          <p>{lang}Disabled Tabs{/lang}:</p>
          <ul id="{$_select_project_tabs_id}_available">
          {foreach $_select_project_tabs as $project_tab_name => $project_tab_text}
            {if !in_array($project_tab_name, $_select_project_tabs_value)}
              <li class="tab sortable">
                <input type="checkbox" name="{$_select_project_tabs_name}[]" value="{$project_tab_name}" class="inline" /> {$project_tab_text}
              </li>
            {/if}
          {/foreach}
          </ul>
        </div>
      </td>
    </tr>
  </table>
  
  {if !AngieApplication::isOndemand() && $logged_user->isAdministrator()}
  <p class="aid">{lang modules_url=Router::assemble('modules_admin')}Note: Missing a feature? Please check <a href=":modules_url">the list of installed modules</a>{/lang}.</p>
  {/if}
</div>

<script type="text/javascript">
  App.widgets.SelectProjectTabs.init({$_select_project_tabs_id|json nofilter}, {$_select_project_tabs_name|json nofilter});
</script>