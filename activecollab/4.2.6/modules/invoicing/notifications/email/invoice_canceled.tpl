{lang num=$context->getName() language=$language}Invoice ':num' has been Canceled{/lang}
================================================================================
{notification_wrapper title='Invoice Canceled' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender inspect=false open_in_browser=false}
  {notification_invoice_info context=$context recipient=$recipient}
  {notification_invoice_comment recipient=$recipient}{$context->getNote() nofilter}{/notification_invoice_comment}
{/notification_wrapper}