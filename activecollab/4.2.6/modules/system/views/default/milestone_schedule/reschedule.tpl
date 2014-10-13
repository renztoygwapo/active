{title}Reschedule Milestone{/title}
{add_bread_crumb}Reschedule{/add_bread_crumb}

<div id="reschedule_milestone" {if $request->isAsyncCall()}class="async"{/if}>
  {form action=$reschedule_url method=post id=reschedule_milestone_form}
    <div class="fields_wrapper">
	    {wrap field=date_range}
	      {label}Start and Due Date{/label}
	      {select_milestone_dates name="milestone" start_on=$milestone_data.start_on due_on=$milestone_data.due_on}
	    {/wrap}
	    
		  {if is_foreachable($successive_milestones)}
		    {wrap field=with_sucessive}
		      {label}With Successive Milestones{/label}
		      {with_successive_milestones name="milestone[with_sucessive]" value=$milestone_data.with_sucessive milestone=$active_milestone successive_milestones=$successive_milestones}
		    {/wrap}
		  {/if}
	    
	    {wrap field=reschedule_milestone_objects}
	      <input type="checkbox" name="milestone[reschedule_milestone_objects]" id="milestoneRescheduleTasks" class="inline input_checkbox" {if $milestone_data.reschedule_milestone_objects}checked="checked"{/if} /> {label class=inline after_text="" for=milestoneRescheduleTasks main_label=false}Also reschedule all tasks that belong to rescheduled milestone{/label}
	    {/wrap}
    </div>
  
    {wrap_buttons}
      {submit}Reschedule Milestone{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  (function () {
      var wrapper = $('#reschedule_milestone');
      var successive_milestones = wrapper.find('div.with_successive_milestones div.successive_milestones');

      wrapper.find('div.with_successive_milestones input[type=radio]').click(function() {
        if($(this).val() == 'move_selected') {
          successive_milestones.show('fast');
        } else {
          successive_milestones.hide('fast');
        } // if
      });
  }());
</script>