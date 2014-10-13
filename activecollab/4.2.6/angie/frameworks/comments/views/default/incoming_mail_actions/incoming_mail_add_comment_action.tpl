<div id="comments_working_box" class="working_box">

  {if $active_mail instanceof IncomingMail && $active_mail->isReplyToNotification()}
    <table class="comment_conflict">
      {if method_exists($active_mail->getParent(),'getProject') && $active_mail->getParent()->getProject()}
        <tr>
          <th>{lang}Project:{/lang}</th>
          <td>{project_link project=$active_mail->getParent()->getProject()}</td>
        </tr>
      {/if}
      <tr>
        <th>{lang}In {/lang}{$active_mail->getParent()->getVerboseType()}:</th>
        <td>{object_link object=$active_mail->getParent()}</td>
      </tr>
      <tr>
        <th colspan="2">{checkbox name="filter[action_parameters][notify_sender]" id="notify_sender" label="Subscribe and notify sender and all cc recipients" value=1 checked=true}
        </th>
      </tr>
    </table>
  {else}

    <p class="incoming_mail_warning_box">{lang}Replies to email notifications are automatically submitted as comments, and <u>that functionality has nothing to do with this action</u>. Use this action if you want to convert some non-reply incoming messages to comments{/lang}.</p>

    {wrap field=project_id}
      {label for=project required=yes}In Project{/label}
      {select_project name="filter[action_parameters][project_id]" value=$filter_data.action_parameters.project_id user=$logged_user show_all=true class="project_box required"}
    {/wrap}

    <div class="render_project_settings"></div>

    {if !$tpl_params.force}
      {wrap field=allow_for_everyone}
      {label}Allow for{/label}
      {radio_field name="filter[action_parameters][allow_for_everyone]" id="allow_for_everyone" label="Everyone" value=IncomingMailFilter::ALLOW_FOR_EVERYONE checked="checked"}
        <br/>
      {radio_field name="filter[action_parameters][allow_for_everyone]" id="allow_for_registered" label="Registered users with proper permissions" value=IncomingMailFilter::ALLOW_FOR_PEOPLE_WHO_CAN pre_selected_value=$filter_data.action_parameters.allow_for_everyone}
      {/wrap}
    {/if}


    {wrap field=notify_sender}
      {label for=notify_sender}Send notification email{/label}
      {checkbox name="filter[action_parameters][notify_sender]" id="notify_sender" label="Subscribe and notify sender and all cc recipients" value=1 checked=$filter_data.action_parameters.notify_sender}
    {/wrap}
  {/if}
	
    
</div>
