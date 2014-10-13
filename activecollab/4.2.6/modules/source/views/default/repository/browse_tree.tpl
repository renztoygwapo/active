{if is_foreachable($list)}
  {foreach from=$list.entries item=entry}
    {if is_null($parent_key)}
      {assign_var name=key}{$entry.url_key}{/assign_var}
      {assign_var name=child_of_key}child_of_root{/assign_var}
    {else}
      {assign_var name=key}{$parent_key}_{$entry.url_key}{/assign_var}
      {assign_var name=child_of_key}child_of_{$parent_key}{/assign_var}
    {/if}

    <tr class="{cycle values='odd,even'} {$child_of_key}" id="result_container_{$key}" level = "{substr_count($key,'_')}" toggle_key="{$key}" expanded="false" style="display: none">
      <td class="{if $entry.kind eq 'dir'}directory{else}file{/if}">
        {assign_var name=source_path}{$active_file}/{$entry.name}{/assign_var}
        {if $entry.kind eq 'dir'}
          <span class="toggle_tree" state="shrinked" toggle_key ="{$key}"
          toggle_url="{$project_object_repository->getToggleUrl($active_commit, $source_path, $key)}" loaded="false">
            <img id="img_{$key}" src="{image_url name='icons/16x16/folder-closed.png' module=$smarty.const.SOURCE_MODULE}"></img>
            {$entry.name}
          </span>
        {else}
        <span class = "file_toggle_tree">
          <a class="browse_url" href="{$project_object_repository->getBrowseUrl($active_commit, $source_path, $entry.revision_number)}">
            <img id="img_{$key}" src="{file_icon_url filename=$source_path}"></img>
            {$entry.name}
          </a>
        </span>
        {/if}
      </td>
      <td class="file_size">
        {if ($entry.size)}
          {$entry.size}
        {else}
          {lang}Folder{/lang}
        {/if}
      </td>
      {if $entry.date === false}
        <td class="date">{lang}N/A{/lang}</td>
      {else}
        <td class="date">{$entry.date|date:0}</td>
      {/if}
      <td class="author">{$entry.author}</td>
      <td class="revision"><a title="View commit information" href="{$project_object_repository->getCommitUrl($entry.revision_number)}">{substr($entry.revision,0,8)}</a></td>
    </tr>
  {/foreach}
{/if}