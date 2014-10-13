{$context->reminders()->getNotificationSubjectPrefix()}{lang name=$context->getName() language=$language}Self Reminder{/lang}
================================================================================
{notification_wrapper title='Self Reminder' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang url=$context_view_url name=$context->getName() type=$context->getVerboseType(true, $language) link_style=$style.link language=$language}Please check "<a href=":url" style=":link_style">:name</a>" :type{/lang}.</p>
  {notification_wrap_body}{$reminder->getComment()|clean|nl2br nofilter}{/notification_wrap_body}
{/notification_wrapper}