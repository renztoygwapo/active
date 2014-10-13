{$context->complete()->getNotificationSubjectPrefix()}{lang type=$context->getVerboseType(false, $language) name=$context->getName()|excerpt language=$language}':name' :type Completed{/lang}
================================================================================
{notification_wrapper title=':type Completed' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang completed_by=$context->complete()->getCompletedBy()->getDisplayName() url=$context_view_url name=$context->getName() type=$context->getVerboseType(true, $language) link_style=$style.link language=$language}:completed_by has just completed "<a href=":url" style=":link_style" target="_blank">:name</a>" :type{/lang}.</p>
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}