{title}Conflicts{/title}
{add_bread_crumb}Conflict resolve{/add_bread_crumb}

<div id="incoming_mail_conflicts_resolve">
  {form action="{$active_mail->getImportUrl()}" method=post}
  <div class="scrollable">

      <div class="content_stack_element" id="details">
        <div class="content_stack_element_info">
          <h3>{lang}Conflict Mail Info{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {if $active_mail->getCreatedByEmail()}
              <dt>{lang}Sender{/lang}</dt>
             <dd>{user_link user=$active_mail->getCreatedBy()}</dd>
          {/if}
          {if $active_mail->getToUser()}
              <dt>{lang}Recipient{/lang}</dt>
             <dd>{user_link user=$active_mail->getToUser()}</dd>
          {/if}
           {if $active_mail->getMailbox()}
              <dt>{lang}Mailbox{/lang}</dt>
             <dd>{$active_mail->getMailbox()->getName()}</dd>
          {/if}
          {if $active_mail->getSubject()}
              <dt>{lang}Subject{/lang}</dt>
             <dd>{$active_mail->getSubject()}</dd>
          {/if}
          {if $active_mail->getPriority()}
              <dt>{lang}Mail Priority{/lang}</dt>
            <dd>{$active_mail->getPriority()}</dd>
          {/if}

          {if $active_mail->getCreatedOn()}
              <dt>{lang}Created On{/lang}</dt>
             <dd>{$active_mail->getCreatedOn()|date}</dd>
          {/if}
          {if $active_mail->getStatus()}
              <dt>{lang}Conflict Reason{/lang}</dt>
            <dd>{$active_mail->getStatus()}</dd>
          {/if}
          {if $active_mail->getParent() && $active_mail->getParent() instanceof IState && $active_mail->getParent()->getState() == $smarty.const.STATE_DELETED}
            <dt>{lang}Deleted Parent{/lang}</dt>
            <dd>{$active_mail->getParent()->getVerboseType()} : {$active_mail->getParent()->getName()}</dd>
          {/if}

          {if $active_mail->getBody()}
            <dt>{lang}Email Body{/lang}</dt>
            <dd>{$active_mail->getBody() nofilter}</dd>
          {/if}

          {if $active_mail->getAttachments()}
              <dt>{lang}Attachments{/lang}</dt>
            <dd>
             {foreach from=$active_mail->getAttachments() item=attachment}
              {$attachment->getOriginalFilename()}<br/>
             {/foreach}</dd>
          {/if}

        </div>
      </div>

      <div class="content_stack_element_info even">
        <h3>{lang}Choose Action To Perform{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
        {include file=get_view_path("_actions_form","fw_incoming_mail_filter_admin",$smarty.const.EMAIL_FRAMEWORK)}
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

	  {wrap_buttons}
  	  {submit}Resolve Conflict{/submit}
    {/wrap_buttons}
  {/form}

</div>