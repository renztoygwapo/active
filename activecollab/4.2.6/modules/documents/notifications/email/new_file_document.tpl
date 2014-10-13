{lang name=$context->getName() language=$language}File ':name' has been Uploaded{/lang}
================================================================================
{notification_wrapper title='File Uploaded' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
<p>{lang author_name=$context->getCreatedBy()->getDisplayName() url=$context_view_url name=$context->getName() link_style=$style.link language=$language}:author_name has just uploaded "<a href=":url" style=":link_style">:name</a>" file{/lang}.</p>
  <div style="padding: 10px; text-align: center;">
    {if $context->preview()->isEmailFriendly()}
      {$context->preview()->renderLarge() nofilter}
    {else}
      <img src="{$context->preview()->getLargeIconUrl()}">
    {/if}
    <p style="font-style: italic;"><a href="{$context->getDownloadUrl(true)}" style="{$style.link}">{$context->getName()}</a> ({$context->getSize()|filesize})</p>
  </div>
{/notification_wrapper}