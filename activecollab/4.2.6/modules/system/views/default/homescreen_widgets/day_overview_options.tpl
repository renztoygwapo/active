<div id="day_overview_settings">
  {wrap field=day_overview_filter_day}
    <div class="filter">
      {label for=filter_day}Due date on{/label}
      <select name="homescreen_widget[filter_day]" class="filter_day">
      {foreach $widget_data.select_options.day as $key=>$value}
        <option value="{$key}" {if $widget_data.filter_day eq $key}selected="selected"{/if}>{$value}</option>
      {/foreach}
      </select>
    </div>
    <div class="filter_options">
      {select_date name="homescreen_widget[selected_date]" value=$widget_data.selected_date label="Date"}
    </div>
  {/wrap}
  
  {wrap field=day_overview_filter_user}
    <div class="filter">
      {label for=filter_user}Assigned to / Delegated by{/label}
      <select name="homescreen_widget[filter_user]" class="filter_user">
      {foreach $widget_data.select_options.user as $key=>$value}
        <option value="{$key}" {if $widget_data.filter_user eq $key}selected="selected"{/if}>{$value}</option>
      {/foreach}
      </select>
    </div>
    <div class="filter_options">
      {label}Select user{/label}
      {select_user value=$widget_data.selected_user_id class="filter_users" exclude_ids=$widget_data.exclude_ids user=$widget_data.user name="homescreen_widget[selected_user_id]"}
    </div>
  {/wrap}
  
  {wrap field=day_overview_filter_projects}
    <div class="filter">
      {label for=filter_projects}In projects{/label}
      <select name="homescreen_widget[filter_projects]" id="select_projects" class="filter_projects">
      {foreach $widget_data.select_options.projects as $key=>$value}
        <option value="{$key}" {if $widget_data.filter_projects eq $key}selected="selected"{/if}>{$value}</option>
      {/foreach}
    </select>
    </div>
    <div class="filter_options">
      {label}Select projects{/label}
      <div class="filter_options_projects_list">
        {if is_foreachable($widget_data.projects)}
        <table class="common">
        {foreach $widget_data.projects as $project_id => $project_name}
          <tr>
            <td class="checkbox"><input type="checkbox" {if in_array($project_id, $widget_data.selected_project_ids)}checked="checked"{/if} class="selected_project" name="homescreen_widget[selected_project_ids][]" value="{$project_id}"/></td>
            <td class="name"><abbr title="{$project_name}">{$project_name|excerpt:40}</abbr></td>
          </tr>
        {/foreach}
        </table>
      </div>
      {else}
        {lang}No projects to choose from{/lang}
      {/if}
    </div>
  {/wrap}
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('button.default').click(function() {
      if ($('#select_projects option:selected').val() == 'selected_projects' && $('input.selected_project:checked').length == 0) {
       App.Wireframe.Flash.error(App.lang('Please select at least one project'));
        return false;
      }
      return true;
    });

    // these options have additional criteria and need another options block displayed
    var show_filter_options_for = [
        "selected_date",
        "selected_user",
        "selected_projects"
    ];
    
    // examine selected option and show/hide additional options
    $(".filter select").change(function() {      
      var additional_options = $(this).parent().parent().find(".filter_options");
      var selected_option = $("option:selected", this).val();
      
      if (jQuery.inArray(selected_option, show_filter_options_for) != -1) {
        additional_options.show();
      } else {
        additional_options.hide();
      }
    });
    
    // upon loading a saved filter, show additonal options if neccessary
    $(".filter select").each(function() {
      var additional_options = $(this).parent().parent().find(".filter_options");
      var selected_option = $("option:selected", this).val();
      
      if (jQuery.inArray(selected_option, show_filter_options_for) != -1) {
        additional_options.show();
      }
    });
    
  });
</script>