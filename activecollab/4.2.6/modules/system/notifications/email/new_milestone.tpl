[{$context->getProject()->getName()}] {lang object_name=$context->getName() language=$language}Milestone ':object_name' has been created{/lang}
================================================================================
{notification_wrapper title='Milestone Created' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang author_name=$context->getCreatedBy()->getDisplayName() url=$context_view_url name=$context->getName() link_style=$style.link  language=$language}:author_name has just created "<a href=":url" style=":link_style" target="_blank">:name</a>" milestone{/lang}</p>
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}

  {if $context->assignees()->getAssignee() instanceof User}
    {if $context->assignees()->isResponsible($recipient)}
    <p>{lang type=$context->getVerboseType(true, $language) language=$language}<u>You are responsible</u> for this :type{/lang}!</p>
      {elseif $context->assignees()->isAssignee($recipient)}
    <p>{lang responsible_name=$context->assignees()->getAssignee()->getDisplayName(true) type=$context->getVerboseType(true, $language) language=$language}You are <u>assigned to this :type</u> and :responsible_name is responsible{/lang}!</p>
    {/if}
  {/if}
{/notification_wrapper}