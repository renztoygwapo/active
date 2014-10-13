{lang language=$language type=$context->getVerboseType(false, $language)}New :type has been Created{/lang}
================================================================================
{notification_wrapper title=':type Created' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang type=$context->getVerboseType(false, $language) url=$context_view_url name=$context->getName() link_style=$style.link language=$language}Thank you for your message. :type <a href=":url" style=":link_style">:name</a> has been created{/lang}.</p>
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}