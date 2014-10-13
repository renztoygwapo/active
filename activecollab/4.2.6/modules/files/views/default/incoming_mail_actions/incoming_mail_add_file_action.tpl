<div id="tasks_working_box" class="working_box">
  {wrap field=project_id}
    {label for=project required=yes}In Project{/label}
    {select_project name="filter[action_parameters][project_id]" value=$filter_data.action_parameters.project_id user=$logged_user show_all=true class="project_box required"}
  {/wrap}
  
  <div class="render_project_settings"></div>
  
  {wrap field=allow_for_everyone}
    {label}Allow for{/label}
    {radio_field name="filter[action_parameters][allow_for_everyone]" id="allow_for_everyone" label="Everyone" value=IncomingMailFilter::ALLOW_FOR_EVERYONE checked="checked"}
    <br/>
    {radio_field name="filter[action_parameters][allow_for_everyone]" id="allow_for_registered" label="Registered users with proper permissions" value=IncomingMailFilter::ALLOW_FOR_PEOPLE_WHO_CAN pre_selected_value=$filter_data.action_parameters.allow_for_everyone}
  {/wrap}
  
  {wrap field=create_as}
  	{label}Create file as{/label}
  	{radio_field name="filter[action_parameters][create_as]" class="create_as" id="create_as_sender" label="Sender" value=IncomingMailFilter::CREATE_AS_SENDER checked="checked"}
	<br/>
	{radio_field name="filter[action_parameters][create_as]" class="create_as" id="create_as_specific" label="Create as:" value=IncomingMailFilter::CREATE_AS_SPECIFIC_USER pre_selected_value=$filter_data.action_parameters.create_as}
	{select_user name="filter[action_parameters][create_as_user]" user=$logged_user value=$filter_data.action_parameters.create_as_user}
    <br/>
  {/wrap}
  
  {wrap field=notify_sender}
    {label for=notify_sender}Send notification email{/label}
  	{checkbox name="filter[action_parameters][notify_sender]" id="notify_sender" label="Subscribe and notify sender and all cc recipients" object_name='file' value=1 checked=$filter_data.action_parameters.notify_sender}
  {/wrap}

</div>
