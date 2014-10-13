{$context->assignees()->getNotificationSubjectPrefix()}{lang name=$context->getName()|excerpt type=$context->getVerboseType(false, $language) language=$language}You are now Responsible for ':name' :type{/lang}
================================================================================
{notification_wrapper title=$title_lang context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
	{notification_assignment_reassigned context=$context reassigned_by_name=$sender->getDisplayName() recipient=$recipient name=$context->getName() url=$context_view_url type=$context->getVerboseType(true, $language) link_style=$style.link language=$language}
  
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}