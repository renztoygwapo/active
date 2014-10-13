{lang name=$context->getName() language=$language}Payment has been Received{/lang}
================================================================================
{notification_wrapper title='Payment Received' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender inspect=false open_in_browser=false}
  <p>{lang name=$context->getName() url=$context_view_url type=$context->getVerboseType(true, $language) language=$language}A payment has been received for "<a href=":url">:name</a>" :type{/lang}. {lang language=$language}Amount received{/lang}: <u>{$payment->getAmount()|money:$payment->getCurrency()}</u></a></p>
{/notification_wrapper}