{title object_name=$active_object->getName()}Reschedule: :object_name{/title}

<div class="reschedule_popup base_reschedule_popup">
	{form method="post" action=$reschedule_url}
	  <div class="fields_wrapper">
	    {wrap field=date_range}
        {if $active_object->fieldExists('start_on')}
	        {label}Start and Due Date{/label}
        {else}
          {label}Due Date{/label}
        {/if}
        
        
        <div class="tbd_off">{radio_field name="reschedule[tbd]" value="1" label="To Be Determined" checked=$reschedule_data.tbd}</div>
        <div class="tbd_on">{radio_field name="reschedule[tbd]" value="0" label="Set Now" checked=!$reschedule_data.tbd}</div>
        
        <div class="reschedule_controls">
			    <table class="" cellspacing="0">
			      <tr>
					    {if $active_object->fieldExists('start_on')}
			        <td>
			          {wrap field=due_on}
			            {select_due_on value=$reschedule_data.start_on name="reschedule[start_on]"}
			          {/wrap}
			        </td>
              <td>—</td>
					    {/if}
					    <td>    
					      {wrap field=due_on}
					        {select_due_on value=$reschedule_data.due_on name="reschedule[due_on]"}
					      {/wrap}
					    </td>
			      </tr>
			    </table>
        </div>
	    {/wrap}	    
	  </div>
	  {wrap_buttons}
	    {submit lang=false}{lang type=$active_object->getVerboseType()}Reschedule :type{/lang}{/submit}
	  {/wrap_buttons}
	{/form}
</div>

<script type="text/javascript">
  var wrapper = $('div.reschedule_popup.base_reschedule_popup');
  var tbd_on = wrapper.find('div.tbd_on input[type=radio]:first');
  var tbd_off = wrapper.find('div.tbd_off input[type=radio]:first');
  var controls = wrapper.find('div.reschedule_controls');

  tbd_on.click(function () {
    controls.show();
  });

  tbd_off.click(function () {
    controls.hide();
  });

  if (tbd_off.is(':checked')) {
    tbd_off.click();
  } else {
    tbd_on.click();
  } // if
</script>