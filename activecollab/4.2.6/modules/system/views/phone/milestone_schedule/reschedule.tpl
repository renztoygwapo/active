{title}Reschedule Milestone{/title}
{add_bread_crumb}Reschedule{/add_bread_crumb}

<div id="reschedule_milestone">
  {form action=$reschedule_url method=post id=reschedule_milestone_form}
    {wrap field=date_range}
      {select_milestone_dates name="milestone" start_on=$milestone_data.start_on due_on=$milestone_data.due_on label='Start and Due Date' interface=AngieApplication::INTERFACE_PHONE}
    {/wrap}
    
	  {if is_foreachable($successive_milestones)}
	    {wrap field=with_sucessive}
	    	<div class="form_checkbox off" id="milestoneRescheduleSuccessiveMilestones">
					<span>{lang}Adjust all successive milestone by the same number of days{/lang}</span>
					<input type="hidden" class="selected_value" value="move_all" name="milestone[with_sucessive][action]">
				</div>
	    {/wrap}
	  {/if}
    
    {wrap field=reschedule_milestone_objects}
    	{checkbox name="milestone[reschedule_milestone_objects]" checked=$milestone_data.reschedule_milestone_objects id="milestoneRescheduleTasks" label="Also reschedule all tasks that belong to rescheduled milestone"}
    {/wrap}
  
    {wrap_buttons}
      {submit}Reschedule{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.DateBox.init();
		
		// Init state
		var form = $('form');
		var successive_milestones_checkbox = form.find('#milestoneRescheduleSuccessiveMilestones');
		var reschedule_tasks_checkbox = form.find('#milestoneRescheduleTasks');
		
		{if !($milestone_data.start_on instanceof DateValue && $milestone_data.due_on instanceof DateValue) || !($milestone_data.due_on instanceof DateValue)}
			successive_milestones_checkbox.hide();
			reschedule_tasks_checkbox.hide();
		{/if}
		
		// Control selecting milestone dates radio button
		form.find('input[type=radio]').change(function() {
      var radio = $(this);
      if(radio.attr('value') == '1') {
        successive_milestones_checkbox.hide();
				reschedule_tasks_checkbox.hide();
      } else {
        successive_milestones_checkbox.show();
				reschedule_tasks_checkbox.show();
      } // if
    });
		
		// Control successive milestones checkbox
		var control = $('#milestoneRescheduleSuccessiveMilestones');
	  var input_field = control.find('input.selected_value');
	  
	  control.click(function() {
	  	var wrapper = $(this);
	  	
	  	if(wrapper.hasClass('off')) {
	  		wrapper.removeClass('off');
	    	wrapper.addClass('on');
	    	input_field.val('move_all');
	  	} else if (wrapper.hasClass('on')) {
	  		wrapper.removeClass('on');
	    	wrapper.addClass('off');
	    	input_field.val('');
	  	} // if
	    
	    return false;
	  });
	});
</script>