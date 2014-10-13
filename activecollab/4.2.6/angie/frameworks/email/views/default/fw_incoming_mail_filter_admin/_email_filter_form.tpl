<div class="content_stack_wrapper">
  <div class="content_stack_element odd">
    <div class="content_stack_element_info">
      <h3>{lang}General Settings{/lang}</h3>
    </div>
    
    <div class="content_stack_element_body">
  	  {wrap field=name}
        {text_field name="filter[name]" value=$filter_data.name id=name label='Filter Name' required=true}
      {/wrap}

      {if IncomingMailboxes::countActive() > 0}
        {wrap field=apply_to}
          {label}Apply this Filter To{/label}
          {wrap field=all_mailboxes id="mailbox_container"}
            {radio_field name="filter[all_mailboxes]" value='1' label='All Mailboxes' checked=!is_foreachable($filter_data.mailbox_id)}
            <br/>
            {radio_field name="filter[all_mailboxes]" value='0' label='Selected Mailboxes' checked=is_foreachable($filter_data.mailbox_id)}
            <div class='mailbox_list' style='display:{if is_foreachable($filter_data.mailbox_id)}block;{/if}'>
              {select_filter_type_mailbox multiple=true size=7 name="filter[mailbox_id]" id="mailbox" value=$filter_data.mailbox_id}
            </div>
          {/wrap}
        {/wrap}
      {/if}


    </div>
  </div>
  
  <div class="content_stack_element odd">
    <div class="content_stack_element_info">
      <div class="content_stack_optional">{checkbox name="filter[subject][use]" class="turn_on" for_id="subject" label="Enabled" value=1 checked=$filter_data.subject_type}</div>
      <h3>{lang}Subject{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=subjectField} 
        {select_filter_type name="filter[subject][type]" id="subject" value=$filter_data.subject_type disabled="disabled"} {text_field name="filter[subject][text]" value=$filter_data.subject_text id=subject_text disabled="disabled"}
      {/wrap}
    </div>
  </div>
  
  <div class="content_stack_element odd">
    <div class="content_stack_element_info">
      <div class="content_stack_optional">{checkbox name="filter[body][use]" class="turn_on" for_id="body"  label="Enabled" value=1 checked=$filter_data.body_type}</div>
      <h3>{lang}Body{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=bodyField} 
        {select_filter_type name="filter[body][type]" value=$filter_data.body_type id="body" disabled="disabled"} {text_field name="filter[body][text]" value=$filter_data.body_text id=body_text disabled="disabled"}
      {/wrap}
    </div>
  </div>
  
  
   <div class="content_stack_element odd">
    <div class="content_stack_element_info">
      <div class="content_stack_optional">{checkbox name="filter[priority][use]" class="turn_on" for_id="priority"  label="Enabled" value=1 checked=$filter_data.priority}</div>
      <h3>{lang}Priority{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=priorityField} 
        {select_filter_type_priority name="filter[priority]" id=priority value=$filter_data.priority disabled="disabled"}
      {/wrap}
    </div>
  </div>
  
  <div class="content_stack_element odd">
    <div class="content_stack_element_info">
      <div class="content_stack_optional">{checkbox name="filter[attachments][use]" class="turn_on" for_id="attachment" label="Enabled" value=1 checked=$filter_data.attachments}</div>
      <h3>{lang}Has Attachments{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=attachmentField} 
        {select_filter_type_attachments name="filter[attachments]" id=attachment value=$filter_data.attachments disabled="disabled"}
      {/wrap}
    </div>
  </div>
  
  <div class="content_stack_element odd">
    <div class="content_stack_element_info">
      <div class="content_stack_optional">{checkbox name="filter[sender][use]" class="turn_on" for_id="sender" label="Enabled" value=1 checked=$filter_data.sender_type}</div>
      <h3>{lang}Sender Email{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=senderField} 
        {select_filter_type_sender name="filter[sender][type]" id="sender" value=$filter_data.sender_type readonly="$readonly" disabled="disabled"} {text_field name="filter[sender][text]" value=$filter_data.sender_text id=sender_text disabled="disabled"}
      {/wrap}
    </div>
  </div>
  
  <div class="content_stack_element odd">
    <div class="content_stack_element_info">
      <div class="content_stack_optional">{checkbox name="filter[to_email][use]" class="turn_on" for_id="to_email" label="Enabled" value=1 checked=$filter_data.to_email_type}</div>
      <h3>{lang}To Email{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=toEmailField} 
        {select_filter_type_to_email name="filter[to_email][type]" id="to_email" value=$filter_data.to_email_type disabled="disabled"} {text_field name="filter[to_email][text]" value=$filter_data.to_email_text id=to_email_text disabled="disabled"}
      {/wrap}
    </div>
  </div>
  
  
   <div class="content_stack_element odd">
    <div class="content_stack_element_info">
      <h3>{lang}Action{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      <input type="hidden" action="{$filter_data.action_name}" id="selected_action">
      {include file=get_view_path("_actions_form","fw_incoming_mail_filter_admin",$smarty.const.EMAIL_FRAMEWORK)}
    </div>
  </div>
 
 {if is_foreachable($unavailable_actions)} 
  <div class="content_stack_element odd">
    <div class="content_stack_element_info">
      <h3>{lang}Unavailable Actions{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {include file=get_view_path("_unavailable_actions_form","fw_incoming_mail_filter_admin",$smarty.const.EMAIL_FRAMEWORK)}
    </div>
  </div>
 {/if}
 
</div>
 
<script type="text/javascript">

	$('#mailbox_container input[type=radio]').change(function(){
		var obj = $(this);
		var mailbox_list = $('#mailbox_container div.mailbox_list');
		if(obj.val() == 0) {
			mailbox_list.show();
		} else {
			mailbox_list.hide();
		}//if
	});


  $('#incoming_mail_filter').each(function() {
    var wrapper = $(this);

    var sender = $("#incoming_mail_filter #sender");
  	var sender_text = $("#incoming_mail_filter #sender_text");
  	
  	if(sender.val() != '{IncomingMailFilter::IM_FILTER_IS}' && sender.val() != '{IncomingMailFilter::IM_FILTER_IS_NOT}' && sender.val() != '{IncomingMailFilter::IM_FILTER_STARTS_WITH}' && sender.val() != '{IncomingMailFilter::IM_FILTER_ENDS_WITH}' && sender.val() != '{IncomingMailFilter::IM_FILTER_HAS}') {
  		sender_text.hide();
  	} // if
  	
  	sender.change(function (){
  		if(sender.val() == '{IncomingMailFilter::IM_FILTER_IS}' || sender.val() == '{IncomingMailFilter::IM_FILTER_IS_NOT}' || sender.val() == '{IncomingMailFilter::IM_FILTER_STARTS_WITH}' || sender.val() == '{IncomingMailFilter::IM_FILTER_ENDS_WITH}' || sender.val() == '{IncomingMailFilter::IM_FILTER_HAS}') {
  			sender_text.show().focus();	
  		} else {
  			sender_text.hide();
  		} // if
  	});

  	/**
  	 * Handle clicks on enable checkboxes
  	 */
  	function enable_or_disable_filter_controls() {
    	var obj = $(this);

    	var element = obj.parents('div.content_stack_element');

    	if(obj.prop('checked')) {
      	element.find('div.content_stack_element_body input, div.content_stack_element_body select').prop('disabled', false);
    	} else {
    	  element.find('div.content_stack_element_body input, div.content_stack_element_body select').prop('disabled', true);
    	} // if
  	} // enable_or_disable_filter_controls

  	$("#incoming_mail_filter .turn_on").each(function() {
  	  enable_or_disable_filter_controls.apply(this);
    }).change(function() {
      enable_or_disable_filter_controls.apply(this);
    });
  });
</script>