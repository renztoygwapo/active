{lang language=$language name=$context->getName()}Document ':name' has been Posted{/lang}
================================================================================
{notification_wrapper title='Document Posted' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang author_name=$context->getCreatedBy()->getDisplayName() url=$context_view_url name=$context->getName() link_style=$style.link language=$language}:author_name has just posted "<a href=":url" style=":link_style">:name</a>" document{/lang}.</p>
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}