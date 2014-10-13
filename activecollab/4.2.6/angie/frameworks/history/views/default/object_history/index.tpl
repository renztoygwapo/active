{if is_foreachable($modifications)}
  {foreach $modifications as $_history_modification}
    <div class="object_history_log">
      <div class="object_history_modification_head">{$_history_modification.head nofilter}</div>
      <ul>
      {foreach $_history_modification.modifications as $_history_modification_modification}
        <li>{$_history_modification_modification nofilter}</li>
      {/foreach}
      </ul>
    </div>
  {/foreach}
{/if}