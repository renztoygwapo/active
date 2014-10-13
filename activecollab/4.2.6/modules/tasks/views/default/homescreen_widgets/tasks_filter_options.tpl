<div id="homescreen_widget_{$widget->getId()}" class="task_filter_options_wrapper">
  {wrap field=task_filter_caption}
    {text_field name='homescreen_widget[caption]' value=$widget_data.caption label='Caption'}
  {/wrap}

  {wrap field=task_filter_include_subtasks}
    {yes_no name='homescreen_widget[include_subtasks]' value=$widget_data.include_subtasks label='Include Subtasks'}
  {/wrap}
  
  {wrap field=task_filter_assignee class='task_filter_options_assignee_filter'}
    {select name='homescreen_widget[assignee_filter]' label='Assigned To' class="picker"}
      <option value="unassigned" {if $widget_data.assignee_filter == 'unassigned'}selected="selected"{/if}>{lang}Not Assigned{/lang}</option>
      <option value="anybody" {if $widget_data.assignee_filter == 'anybody'}selected="selected"{/if}>{lang}Anybody{/lang}</option>
      <option value="logged_user" {if $widget_data.assignee_filter == 'logged_user'}selected="selected"{/if}>{lang}Logged User{/lang}</option>
      <option value="selected" {if $widget_data.assignee_filter == 'selected'}selected="selected"{/if}>{lang}Selected User{/lang}</option>
    {/select} {select_user name='homescreen_widget[user_id]' exclude_ids=$widget_data.exclude_ids value=$widget_data.user_id user=$user}
    
    <div class="task_filter_options_wrapper_responsible_only">
      {checkbox_field name='homescreen_widget[responsible_only]' value=1 checked=$widget_data.responsible_only label='Responsible Only'}
    </div>
  {/wrap}

  {wrap field=task_filter_projects class='task_filter_options_project_filter'}
    {select name='homescreen_widget[projects_filter]' label='Project' class='picker'}
      <option value="active" {if $widget_data.projects_filter == 'active'}selected="selected"{/if}>{lang}Active Projects{/lang}</option>
      <option value="completed" {if $widget_data.projects_filter == 'completed'}selected="selected"{/if}>{lang}Completed Projects{/lang}</option>
      <option value="selected" {if $widget_data.projects_filter == 'selected'}selected="selected"{/if}>{lang}Selected Project{/lang}</option>
    {/select} {select_project name='homescreen_widget[project_id]' value=$widget_data.project_id user=$user}
  {/wrap}
  
  {wrap field=task_filter_categories class='task_filter_options_category_names_filter'}
    {text_field name='homescreen_widget[category_names]' value=$widget_data.category_names label='Categories'}
  {/wrap}
  
  {wrap field=task_filter_labels class='task_filter_options_label_names_filter'}
  	{text_field name='homescreen_widget[label_names]' value=$widget_data.label_names label='Labels'}
  {/wrap}
  
  {wrap field=task_filter_milestones class='task_filter_options_milestone_names_filter'}
    {text_field name='homescreen_widget[milestone_names]' value=$widget_data.milestone_names label='Milestones'}
  {/wrap}
  
  {wrap field=task_filter_group_by}
    {select_group_assignments_by name='homescreen_widget[group_by]' value=$widget_data.group_by label='Group By'}
  {/wrap}
</div>

<script type="text/javascript">
  $('#homescreen_widget_{$widget->getId()}').each(function() {
    var wrapper = $(this);

    // Assingee filter
    var assignee_filter = wrapper.find('div.task_filter_options_assignee_filter select.picker').val();

    if(assignee_filter != 'selected') {
      wrapper.find('div.task_filter_options_assignee_filter select.select_user').hide();
    } // if
    
    if(assignee_filter == 'unassigned' || assignee_filter == 'anybody') {
      wrapper.find('div.task_filter_options_wrapper_responsible_only').hide();
    } // if

    wrapper.find('div.task_filter_options_assignee_filter select.picker').change(function() {
      var value = $(this).val();

      if(value == 'selected') {
        wrapper.find('div.task_filter_options_assignee_filter select.select_user').show();
      } else {
        wrapper.find('div.task_filter_options_assignee_filter select.select_user').hide();
      } // if

      if(value == 'unassigned' || value == 'anybody') {
        wrapper.find('div.task_filter_options_wrapper_responsible_only').hide().find('input[type=checkbox]').prop('checked', false);
      } else {
        wrapper.find('div.task_filter_options_wrapper_responsible_only').show();
      } // if
    });

    // Project filter
    var project_filter = wrapper.find('div.task_filter_options_project_filter select.picker').val();

    if(project_filter != 'selected') {
      wrapper.find('div.task_filter_options_project_filter select.select_project').hide();
    } // if

    wrapper.find('div.task_filter_options_project_filter select.picker').change(function() {
      if($(this).val() == 'selected') {
        wrapper.find('div.task_filter_options_project_filter select.select_project').show();
      } else {
        wrapper.find('div.task_filter_options_project_filter select.select_project').hide();
      } // if
    });
  });
</script>