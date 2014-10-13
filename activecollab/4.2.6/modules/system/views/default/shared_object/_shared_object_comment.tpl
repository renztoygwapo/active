<div class="shared_object_comment {if $created_by instanceof User}registered{else}anonymous{/if}">
  <div class="head">
    <img src="{$created_by->avatar()->getUrl()}" alt="{lang name=$created_by->getFirstName()}:name's avatar{/lang}">
  </div>
  <div class="body">
    <div class="meta">{$created_by->getDisplayName(true)}<span class="created_on">{$created_on|date}</span></div>
    <div class="body_text">{$body|rich_text:frontend nofilter}</div>
  {if $attachments}
    <div class="body_attachment">
      <strong>{lang}Attachments{/lang}</strong>
      <ul>
      {foreach $attachments as $attachment}
        <li><a href="{$attachment->getPublicViewUrl(true)}"><img src="{$attachment->preview()->getSmallIconUrl()}" /><span class="label">{$attachment->getName()}</span></a><span class="size">{$attachment->getSize()|format_file_size}</span></li>
      {/foreach}
      </ul>
    </div>
  {/if} 
  </div>
</div>