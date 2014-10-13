[{$context->getProject()->getName()}] {lang name=$context->getName() language=$language}Task ':name' has been Created via Public Form{/lang}
================================================================================
{notification_wrapper title='Task Created (via Form)' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang author_name=$context->getCreatedBy()->getDisplayName() url=$context_view_url name=$context->getName() form_url=$form->getPublicUrl() form_name=$form->getName() link_style=$style.link language=$language}:author_name has just created "<a href=":url" style=":link_style" target="_blank">:name</a>" task using "<a href=":form_url" style=":link_style">:form_name</a>{/lang}" public form.</p>
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}