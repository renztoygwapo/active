{lang language=$language}Reset Your Password{/lang}
================================================================================
{notification_wrapper title='Reset Your Password' context=$context recipient=$recipient sender=$sender}
  <p>{lang reset_url=$context->getResetPasswordUrl() link_style=$style.link language=$language}Visit <a href=":reset_url" style=":link_style">this page</a> to reset your password. This link will be valid for 2 days only{/lang}.</p>
{/notification_wrapper}