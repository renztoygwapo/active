{wrap field=name}
  {text_field name='milestone[name]' value=$milestone_data.name id=milestoneName label='Name' required=true}
{/wrap}
        
{wrap_editor field=body}
  {editor_field name='milestone[body]' id=milestoneBody label='Description'}{$milestone_data.body nofilter}{/editor_field}
{/wrap_editor}

{if $active_milestone->isNew()}
  {wrap field=date_range}
    {select_milestone_dates name=milestone start_on=$milestone_data.start_on due_on=$milestone_data.due_on label='Start and Due Date' interface=AngieApplication::INTERFACE_PHONE}
  {/wrap}
{/if}
    
{wrap field=priority}
  {select_priority name='milestone[priority]' value=$milestone_data.priority label='Priority'}
{/wrap}

{wrap field=assignees}
	{select_assignees name='milestone' value=$milestone_data.assignee_id exclude=$milestone_data.exclude_ids other_assignees=$milestone_data.other_assignees object=$active_milestone user=$logged_user choose_responsible=true choose_subscribers=$active_milestone->isNew() interface=AngieApplication::INTERFACE_PHONE}
{/wrap}

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
		App.Wireframe.DateBox.init();
	});
</script>