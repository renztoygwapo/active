<div class="repository_compare_files_header">
  <div class="repository_compare_files_filename">
    <strong>{lang}Path{/lang}</strong>: {$navigation nofilter}
  </div>
  <div class="repository_compare_files_back">
    <a href="#">{lang}Back{/lang}</a>
  </div>
</div>

<div id="repository_compare_files">
  <table class="repository_compare_revisions_info" cellspacing="0">
    <tr>
      <td class="label">{lang}Compare From{/lang}</td>
      <td class="revision">{substr($compare_from->getName(),0,8)}</td>
      <td class="comment">{$compare_from->getMessageBody()|stripslashes|nl2br|clickable|excerpt:60 nofilter}</td>
    </tr>
    <tr>
      <td class="label">{lang}Compare To{/lang}</td>
      <td class="revision">{substr($compare_to->getName(),0,8)}</td>
      <td class="comment">{$compare_to->getMessageBody()|stripslashes|nl2br|clickable|excerpt:60 nofilter}</td>
    </tr>
  </table>
     
    {if is_foreachable($diff)}
      {foreach from=$diff item=file name=file_diff}
      <div class="file_diff">
        <div class="lines" valign="top"><pre>{$file.lines}</pre></div>
        <div class="source" valign="top"><pre><table cellspacing="0"><tr><td>{$file.content nofilter}</td></tr></table></pre></div>
      </div>
      {/foreach}
    {else}
        <p class="empty_page"><span class="inner">{lang}No diff available{/lang}</span></p>
      <p>
        {if !$info_compare_from}
        	{lang revision=$compare_from->getName()} This file does not exist in :revision revision.{/lang}
        {else if !$info_compare_to}
        	{lang revision=$compare_to->getName()} This file does not exist in :revision revision.{/lang}
        {else}
        	{lang} File is the same in both revisions.{/lang}
        {/if}
      </p>
    {/if}

</div>