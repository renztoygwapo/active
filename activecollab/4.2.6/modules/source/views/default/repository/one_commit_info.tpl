<p>{$commit->getMessageBody()|stripslashes|nl2br|clickable nofilter}</p>

<div id="commit_info" class="commit_files">
  <ul>
    {foreach from=$grouped_paths item=path name=files_list key=action}
      {foreach from=$path item=item}
        <li>
          <span class="{$action|source_module_get_state_name}">{$action|source_module_get_state_label}</span>
          {$item}
        </li>
      {/foreach}
    {/foreach}
  </ul>
</div>