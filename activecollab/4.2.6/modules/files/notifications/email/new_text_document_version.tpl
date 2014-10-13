[{$context->getProject()->getName()}] {lang name=$context->getName() language=$language}New Version of ':name' Document has been Posted{/lang}
================================================================================
{notification_wrapper title='Document Version Posted' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang new_version_by=$sender->getDisplayName() url=$context_view_url name=$context->getName() link_style=$style.link language=$language}:new_version_by has just posted a new version of "<a href=":url" style=":link_style">:name</a>" document{/lang}.</p>
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}