{$context->complete()->getNotificationSubjectPrefix()}{lang type=$context->getVerboseType(false, $language) name=$context->getName()|excerpt language=$language}':name' :type Reopened{/lang}
================================================================================
{notification_wrapper title=':type Reopened' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang reopened_by=$sender->getDisplayName() url=$context->getViewUrl() name=$context->getName() type=$context->getVerboseType(true, $language) link_style=$style.link language=$language}:reopened_by has just reopened "<a href=":url" style=":link_style" target="_blank">:name</a>" :type{/lang}.</p>
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}