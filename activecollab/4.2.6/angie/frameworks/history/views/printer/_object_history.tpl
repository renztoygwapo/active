<div class="resource object_history object_section" id="object_history_{$_history_object->getId()}">
  <div class="content_section_title"><h2>{lang}History{/lang}</h2></div>
  
  <div class="object_history_logs object_section_content common_object_section_content">
  {if is_foreachable($_history_modifications)}
    {foreach $_history_modifications as $_history_modification}
    <div class="object_history_log">
      <div class="object_history_modification_head">{$_history_modification.head nofilter}</div>
      <ul>
      {foreach $_history_modification.modifications as $_history_modification_modification}
        <li>{$_history_modification_modification nofilter}</li>
      {/foreach}
      </ul>
    </div>
    {/foreach}
  {else}
    <p class="empty_page"><span class="inner">{lang}History is empty{/lang}</span></p>
  {/if}
  </div>
</div>