[{$context->getProject()->getName()}] {lang object_name=$context->getName() language=$language}Discussion ':object_name' has been started{/lang}
================================================================================
{notification_wrapper title='Discussion Created' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang author_name=$context->getCreatedBy()->getDisplayName() url=$context_view_url name=$context->getName() link_style=$style.link  language=$language}:author_name has just created "<a href=":url" style=":link_style" target="_blank">:name</a>" discussion{/lang}</p>
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}