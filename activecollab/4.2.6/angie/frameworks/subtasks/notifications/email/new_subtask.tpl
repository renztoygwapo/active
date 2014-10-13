{$context->subtasks()->getNotificationSubjectPrefix()}{lang name=$subtask->getName()|excerpt language=$language}':name' Subtask Created{/lang}
================================================================================
{notification_wrapper title='Subtask Created' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang author_name=$subtask->getCreatedBy()->getDisplayName() url=$context_view_url name=$subtask->getName() context_name=$context->getName() context_url=$context_view_url context_type=$context->getVerboseType(true, $language) link_style=$style.link language=$language}:author_name has just created "<a href=":url" style=":link_style" target="_blank">:name</a>" subtask for "<a href=":context_url" style=":link_style">:context_name</a>" :context_type{/lang}.
  {if $subtask->assignees()->getAssignee() instanceof User}
    {if $subtask->assignees()->isResponsible($recipient)}
      {lang}This subtask is <u>assigned to you</u>{/lang}!
    {else}
      {lang assignee=$subtask->assignees()->getAssignee()->getDisplayName(true)}This subtask is assigned to :assignee{/lang}.
    {/if}
  {/if}
  </p>
{/notification_wrapper}