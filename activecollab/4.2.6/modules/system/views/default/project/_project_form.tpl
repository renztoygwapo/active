<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars">
  <div class="main_form_column">
    {wrap field=name}
      {text_field name="project[name]" value=$project_data.name id=projectName class="title required validate_minlength 3" label="Name" required=true maxlength="150"}
    {/wrap}
    
    {wrap_editor field=overview}
      {label}Description{/label}
      {editor_field name="project[overview]" id=projectOverview inline_attachments=$project_data.inline_attachments object=$active_project images_enabled=false}{$project_data.overview nofilter}{/editor_field}
    {/wrap_editor}
  </div>
  
  <div class="form_sidebar form_first_sidebar">
    {wrap field=leader_id}
      {select_user name="project[leader_id]" value=$project_data.leader_id label="Leader" exclude_ids=$project_data.exclude_ids user=$logged_user optional=false required=true}
    {/wrap}
  
    {wrap field=category_id}
      {select_project_category name="project[category_id]" value=$project_data.category_id label="Category" user=$logged_user optional=true success_event='category_created'}
    {/wrap}
    
    {wrap field=label_id}
      {select_label label="Label" name='project[label_id]' value=$project_data.label_id type=ProjectLabel user=$logged_user can_create_new=false optional=true}
    {/wrap}

	  {wrap field=company_id}
	  {select_company name="project[company_id]" class='project_select_client' value=$project_data.company_id label="Client" user=$logged_user success_event=company_created optional=true required=true}
	  {/wrap}

	  {if AngieApplication::isModuleLoaded('tracking') && $logged_user instanceof User && $logged_user->canSeeProjectBudgets()}
		  {wrap field=budget}
		  {money_field name="project[budget]" value=$project_data.budget label="Budget"}
		  {/wrap}

		  {wrap field=currency_id}
		  {select_currency name="project[currency_id]" value=$project_data.currency_id label="Currency" optional=true}
		  {/wrap}
	  {/if}

	  {custom_fields name='project' object=$active_project object_data=$project_data}
    
  {if $project_data.based_on_type && $project_data.based_on_type == 'Quote'}
    {wrap field=create_milestones class='based_on_quote'}
      {yes_no name='project[create_milestones]' data=$project_data.milestones yes_text="{lang}Yes, create them{/lang}" no_text="{lang}Don't create{/lang}" label="Milestones based on Quote Items" id='create_milestones'}
    {/wrap}
  {/if}

  {if $based_on instanceof IProjectBasedOn}
  	<input type="hidden" name="project[based_on_type]" value="{$based_on|get_class}" />
  	<input type="hidden" name="project[based_on_id]" value="{$based_on->getId()}" />
  {/if}
  </div>
  
  <div class="form_sidebar form_second_sidebar">
	  {if $active_project->isNew()}
		  {wrap field=project_template_id class='select_project_template'}
		    {select_project_template name="project[project_template_id]" value=$project_data.project_template_id label="Project Template"}
		  {/wrap}

		  {wrap field=starts_on class='first_milestone_starts_on'}
		    {select_due_on name="project[first_milestone_starts_on]" id="first_milestone_starts_on" value=$project_data.first_milestone_starts_on label="Project Starts On" required=true}
			  <p class="aid">{lang}Select when project should start. System will reschedule all other milestones and assignments based on that start point{/lang}.</p>
		  {/wrap}

		  <div class="template_positions_container">
			  {wrap field=project_template_position class='select_project_template_position'}
			    {select_position_template_user user=$logged_user label='Position'}
			  {/wrap}
		  </div>
	  {/if}
	</div>
</div>