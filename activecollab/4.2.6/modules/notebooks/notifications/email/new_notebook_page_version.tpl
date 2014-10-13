[{$notebook->getProject()->getName()}] {lang name=$context->getName() language=$language}New Version of ':name' Page has been Created{/lang}
================================================================================
{notification_wrapper title='Page Version Created' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang author_name=$context->getUpdatedBy()->getDisplayName() url=$context_view_url name=$context->getName() notebook_url=$notebook->getViewUrl() notebook_name=$notebook->getName() link_style=$style.link language=$language}:author_name has just created a new version of "<a href=":url" style=":link_style" target="_blank">:name</a>" page in <a href=":notebook_url" style=":link_style">:notebook_name</a> notebook{/lang}:</p>
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}