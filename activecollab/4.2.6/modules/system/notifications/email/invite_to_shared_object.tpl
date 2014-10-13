[{$context->getProject()->getName()}] {lang name=$context->getName() type=$context->getVerboseType(false, $language) language=$language}You have been Invited to Collaborate on ':name' :type{/lang}
================================================================================
{notification_wrapper title='Shared :type' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender inspect=false}
  <p style="margin-bottom: 36px;">{lang url=$context_view_url name=$context->getName() type=$context->getVerboseType(true, $language) link_style=$style.link language=$language}You have been invited to collaborate on "<a href=":url" style=":link_style">:name</a>" :type. To add your comment, simply <u>reply to this email</u> or <a href=":url" style=":link_style">visit this page</a>{/lang}.</p>
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}