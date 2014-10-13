{$context->assignees()->getNotificationSubjectPrefix()}{lang name=$context->getName()|excerpt type=$context->getVerboseType(false, $language) language=$language}You are no Longer Responsible for ':name' :type{/lang}
================================================================================
{notification_wrapper title=':type Reassigned' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang reassigned_by_name=$sender->getDisplayName() url=$context_view_url name=$context->getName() type=$context->getVerboseType(true, $language) link_style=$style.link language=$language}You are no longer responsible for "<a href=":url" style=":link_style" target="_blank">:name</a>" :type{/lang}.</p>
{/notification_wrapper}