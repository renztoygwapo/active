{lang language=$language}Recurring Profile has been Archived{/lang}
================================================================================
{notification_wrapper title='Recurring Profile Archived' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender inspect=false open_in_browser=false}
  <p>{lang name=$context->getName() url=$context->getViewUrl() link_style=$style.link language=$language}Recurring invoice profile "<a href=":url" style=":link_style">:name"</a> has been archived{/lang}.</p>
{/notification_wrapper}