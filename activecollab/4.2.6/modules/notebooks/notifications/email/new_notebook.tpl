[{$context->getProject()->getName()}] {lang name=$context->getName() language=$language}Notebook ':name' has been Created{/lang}
================================================================================
{notification_wrapper title='Notebook Created' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang author_name=$context->getCreatedBy()->getDisplayName() url=$context_view_url name=$context->getName() link_style=$style.link language=$language}:author_name has just created "<a href=":url" style=":link_style" target="_blank">:name</a>" notebook{/lang}</p>
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}