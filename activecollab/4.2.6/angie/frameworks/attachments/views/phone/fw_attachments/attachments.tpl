{title}Attachments{/title}
{add_bread_crumb}Attachments{/add_bread_crumb}

<div id="attachments">
{if is_foreachable($attachments)}
  <ul>
  {foreach $attachments as $attachment}
    <li><a href="{$attachment->getViewUrl(true)}"}>{$attachment->getName()}</a></li>
  {/foreach}
  </ul>
{else}
  <p>{lang type=$active_object->getVerboseType(true)}There are no files attached to this :type{/lang}</p>
{/if}
</div>