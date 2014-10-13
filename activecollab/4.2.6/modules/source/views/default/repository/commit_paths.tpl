<div class="fields_wrapper">
  <div class="commit_files">
    <p class="commit_details">{lang}Commit Name{/lang}: {$active_commit->getName()|clean|nl2br nofilter}</p>
      {if $active_commit->getCommitedByName() != $active_commit->getAuthoredByName()}
        <p class="commit_details">Author:
          {assign var=modify_author value=$active_commit->getAuthoredBy()}
          {if $modify_author instanceof User}
            {$modify_author->getDisplayName(true)}
          {else}
            {$active_commit->getAuthoredByName()}
          {/if}
        </p>
      {/if}
    <p class="commit_details">{lang}Committer{/lang}:
      {assign var=modify_commiter value=$active_commit->getCommitedBy()}
      {if $modify_commiter instanceof User}
        {$modify_commiter->getDisplayName(true)}
      {else}
        {$active_commit->getCommitedByName()}
      {/if}
    </p>

    <p>{lang}There are <strong>{count($commit_paths.A)}</strong> added, <strong>{count($commit_paths.M)}</strong> modified and <strong>{count($commit_paths.D)}</strong> deleted items{/lang}</p>

    <ul>
      {foreach from=$commit_paths item=path name=files_list key=action}
        {foreach from=$path item=item}
	      <li>
	        <span class="{$action|source_module_get_state_name}">{$action|source_module_get_state_label}</span>
          {if ($active_commit->getType() === 'SvnCommit') && !$active_commit->checkPathAvailability($item)}
            {$item}
          {else}
	          <a href="{$project_object_repository->getBrowseUrl($active_commit, $item)}">{$item}</a>
            <span class="one_file_diff"><a href="{$active_commit->getViewFileUrl($active_project->getSlug(),$project_object_repository->getId(),$item)}">({lang}diff{/lang})</a></span>
          {/if}
	      </li>
        {/foreach}
      {/foreach}
    </ul>
  </div>
</div>