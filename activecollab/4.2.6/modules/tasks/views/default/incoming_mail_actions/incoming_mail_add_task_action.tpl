<div id="tasks_working_box" class="working_box">
  {wrap field=project_id}
    {label for=project required=yes}In Project{/label}
    {select_project name="filter[action_parameters][project_id]" value=$filter_data.action_parameters.project_id user=$logged_user show_all=true class="project_box required"}
  {/wrap}
  
  <div class="render_project_settings"></div>

  {wrap field=due_on}
    {label for="due_on"}Set Due On{/label}
    {select_incoming_mail_due_on name="filter[action_parameters][due_on]" id="due_on" value=$filter_data.action_parameters.due_on}
  {/wrap}

  {wrap field=allow_for_everyone}
    {label}Allow for{/label}
    {radio_field name="filter[action_parameters][allow_for_everyone]" id="allow_for_everyone" label="Everyone" value=IncomingMailFilter::ALLOW_FOR_EVERYONE checked="checked"}
    <br/>
    {radio_field name="filter[action_parameters][allow_for_everyone]" id="allow_for_registered" label="Registered users with proper permissions" value=IncomingMailFilter::ALLOW_FOR_PEOPLE_WHO_CAN pre_selected_value=$filter_data.action_parameters.allow_for_everyone}
	{/wrap}
  
  {wrap field=create_as}
  	{label}Create task as{/label}
  	{radio_field name="filter[action_parameters][create_as]" class="create_as" id="create_as_sender" label="Sender" value=IncomingMailFilter::CREATE_AS_SENDER checked="checked"}
    <br/>
    {radio_field name="filter[action_parameters][create_as]" class="create_as" id="create_as_specific" label="Create as:" value=IncomingMailFilter::CREATE_AS_SPECIFIC_USER pre_selected_value=$filter_data.action_parameters.create_as}
    {select_user name="filter[action_parameters][create_as_user]" user=$logged_user value=$filter_data.action_parameters.create_as_user}
      <br/>
  {/wrap}
  
  {wrap field=notify_sender}
    {label for=notify_sender}Send notification email and create public page{/label}
  	{checkbox name="filter[action_parameters][notify_sender]" id="notify_sender" label="Subscribe and notify sender and all cc recipients" object_name='task' value=1 checked=$filter_data.action_parameters.notify_sender}
  	{checkbox name="filter[action_parameters][create_public_page]" id="create_public_page" label="Create public page" value=1 checked=$filter_data.action_parameters.create_public_page disabled="disabled"}
  {/wrap}
  
  {wrap field=priority}
    {label for=priority required="yes"}Priority{/label}
    {radio_field name="filter[action_parameters][use_message_priority]" class="use_message_priority" id="use_message_priority_yes" label="Use message priority" value=IncomingMailFilter::USE_MESSAGE_PRIORITY  checked="checked"}
	<br/>
	{radio_field name="filter[action_parameters][use_message_priority]" class="use_message_priority" id="use_message_priority_no" label="Set it to:" value=IncomingMailFilter::USE_CUSTOM_PRIORITY pre_selected_value=$filter_data.action_parameters.use_message_priority}
	{select_priority name="filter[action_parameters][priority]" id="task_priority" value=$filter_data.action_parameters.priority}
  {/wrap}

</div>
