<div class="fields_wrapper">
  <p>{lang}Repository name{/lang}: {$source_repository->getName()}</p>
  <div class="admin_repository_usage">
  {if (is_foreachable($projects) && count($projects) > 0)} 
    <p>{lang}Repository usage in projects{/lang}:</p>
    <ul>
    {foreach from=$projects item=project}
  	  <li><a href="{source_module_url($project)}">{lang}Project{/lang}: {$project->getName()}</a></li>
  	{/foreach}
    </ul>
  {else}
  	<p>{lang}This repository is not in use{/lang}.</p>
  {/if}
  </div>
</div>