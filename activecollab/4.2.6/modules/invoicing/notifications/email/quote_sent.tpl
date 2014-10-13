{lang name=$context->getName() language=$notification_langauge}Quote ':name' has been Sent{/lang}
================================================================================
{notification_wrapper title='Quote Sent' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender inspect=false open_in_browser=false}
  {if $recipient instanceof User}
    {assign_var name=notification_sent_by_url}{$sender->getViewUrl()}{/assign_var}
    {assign_var name=notification_quote_url}{$context->getCompanyViewUrl()}{/assign_var}
  {else}
    {assign_var name=notification_sent_by_url}mailto:{$sender->getEmail()}{/assign_var}
    {assign_var name=notification_quote_url}{$context->getPublicUrl()}{/assign_var}
  {/if}

  <p>{lang sent_by_url=$notification_sent_by_url sent_by_name=$sender->getDisplayName() quote_url=$notification_quote_url pdf_url=$context->getPublicPdfUrl() quote_name=$context->getName() link_style=$style.link language=$notification_langauge}<a href=":sent_by_url" style=":link_style">:sent_by_name</a> has just sent you a quote: <a href=":quote_url" style=":link_style">:quote_name</a>. You can <a href=":quote_url" style=":link_style">view the quote details</a> or <a href=":pdf_url" style=":link_style">download the PDF version here</a>.{/lang}</p>
{/notification_wrapper}