{title lang=false}{$active_outgoing_message->getSubject()}{/title}
{add_bread_crumb}Message Details{/add_bread_crumb}
{use_widget name=properties_list module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="message_sent_details" class="object_inspector properties_list">
  <div class="head">
    <div class="properties">
      <div class="property">
        <div class="label">{lang}Sender{/lang}</div>
        <div class="data">{user_link user=$active_outgoing_message->getSender()}</div>
      </div>

      <div class="property">
        <div class="label">{lang}Recipient{/lang}</div>
        <div class="data">{user_link user=$active_outgoing_message->getRecipient()}</div>
      </div>

      <div class="property">
        <div class="label">{lang}Subject{/lang}</div>
        <div class="data">{$active_outgoing_message->getSubject()}</div>
      </div>
    </div>
  </div>
  
  <div class="body" style="text-align: center">
    <iframe src="{$active_outgoing_message->getRawBodyUrl()}" style="margin: 0 auto" width="750" height="550" seamless></iframe>
</div>