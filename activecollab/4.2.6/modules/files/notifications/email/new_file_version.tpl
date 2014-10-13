[{$context->getProject()->getName()}] {lang object_name=$context->getName() language=$language}New Version of ':object_name' File has been Uploaded{/lang}
================================================================================
{notification_wrapper title='File Version Uploaded' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang new_version_by=$sender->getDisplayName() url=$context_view_url name=$context->getName() link_style=$style.link language=$language}:new_version_by has just uploaded a new version of "<a href=":url" style=":link_style">:name</a>" file{/lang}.</p>

  <div style="padding: 10px; text-align: center">
    {if $context->preview()->isEmailFriendly()}
      {$context->preview()->renderLarge() nofilter}
      {else}
      <img src="{$context->preview()->getLargeIconUrl()}">
    {/if}
    <p style="font-style: italic"><a href="{$context->getDownloadUrl(true)}" target="_blank">{$context->getName()}</a> ({$context->getSize()|filesize})</p>
  </div>

  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
{/notification_wrapper}