{title object_name=$active_object->getName()}Reschedule: :object_name{/title}

<div id="reschedule_task" class="reschedule_popup base_reschedule_popup">
	{form action=$reschedule_url}
	  <div class="fields_wrapper">
	    {wrap field=date_range}
        {label}Due Date{/label}
        
        <div class="tbd_off">{radio_field name="reschedule[tbd]" value="1" label="To Be Determined" checked=$reschedule_data.tbd}</div>
        <div class="tbd_on">{radio_field name="reschedule[tbd]" value="0" label="Set Now" checked=!$reschedule_data.tbd}</div>
        
        <div class="reschedule_controls">
		      {wrap field=due_on}
		        {select_due_on name="reschedule[due_on]" value=$reschedule_data.due_on}
		      {/wrap}
		      
		      {checkbox_field name="reschedule[reschedule_subtasks]" label="Also reschedule all subtasks for this task" value="1" checked=$reschedule_data.reschedule_subtasks}
        </div>
	    {/wrap}	    
	  </div>
	  
	  {wrap_buttons}
	    {submit}Reschedule{/submit}
	  {/wrap_buttons}
	{/form}
</div>

<script type="text/javascript">
  $('#reschedule_task').each(function() {
    var wrapper = $(this);
    
    var tbd_on = wrapper.find('div.tbd_on input[type=radio]:first');
    var tbd_off = wrapper.find('div.tbd_off input[type=radio]:first');
    var controls = wrapper.find('div.reschedule_controls');

    tbd_on.click(function () {
      controls.show();
    });

    tbd_off.click(function () {
      controls.hide();
    });

    if(tbd_off.prop('checked')) {
      controls.hide();
    } // if
  });
</script>