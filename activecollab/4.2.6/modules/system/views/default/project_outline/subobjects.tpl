{if is_foreachable($subobjects)}
  <ul class="{$object->getVerboseType()|strtolower}_subobjects subobjects">
    {foreach from=$subobjects item=subobject}
      {include file=get_view_path('subobject', 'project_outline', 'system')}
    {/foreach}
  </ul>
{/if}