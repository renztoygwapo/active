  <p>{lang first_name=$notification_recipient->getFirstName() language=$notification_language}Hello :first_name{/lang},</p>
{if is_foreachable($notification_bodies)}
  <p>{lang count=count($notification_bodies) language=$notification_language}System compiled a list of :count notifications that might interest you{/lang}:</p>
  {foreach $notification_bodies as $notification_body}
    <div class="notification_body">{$notification_body nofilter}</div>
  {/foreach}
{elseif $notification_body}
  <div class="notification_body">{$notification_body nofilter}</div>
{/if}