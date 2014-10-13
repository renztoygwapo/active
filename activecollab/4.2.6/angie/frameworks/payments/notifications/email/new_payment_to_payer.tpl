{lang language=$language}Thank You for Your Payment{/lang}
================================================================================
{notification_wrapper title='Payment Received' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender inspect=false open_in_browser=false}
  <p>{lang name=$context->getName() url=$context_view_url type=$context->getVerboseType(true, $language) language=$language}We have received your payment for "<a href=":url">:name</a>" :type{/lang}. {lang language=$language}Amount received{/lang}: <u>{$payment->getAmount()|money:$payment->getCurrency()}</u></p>
{/notification_wrapper}