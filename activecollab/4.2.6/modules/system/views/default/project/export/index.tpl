<div id="object_main_info" class="object_info">
  <h1>{lang}Project Overview{/lang}</h1>
</div>

<div id="object_details" class="object_info">
  <dl class="properties">  
    <dt>{lang}Created By{/lang}:</dt>
    <dd>{$active_project->getCreatedByName()}</dd>
    
    <dt>{lang}Created On{/lang}:</dt>
    <dd>{$active_project->getCreatedOn()|datetime}</dd>
    
    <dt>{lang}Name{/lang}:</dt>
    <dd>{$active_project->getName()}</dd>
    
    {if $project_leader instanceof User}
    <dt>{lang}Leader{/lang}:</dt>
    <dd>{$project_leader->getName()}</dd>
    {/if}
    
    {if $project_company instanceof Company}
    <dt>{lang}Client{/lang}:</dt>
    <dd>{$project_company->getName()}</dd>
    {/if}
    
    {if $active_project->category()->get() instanceof ProjectCategory}
    <dt>{lang}Category{/lang}:</dt>
    <dd>{$active_project->category()->get()->getName()}</dd>
    {/if}
    
    <dt>{lang}Status{/lang}:</dt>
    <dd>{$active_project->getVerboseStatus()}</dd>
    
    <dt>{lang}Details{/lang}:</dt>
    <dd><div class="body content">{$active_project->getOverview()}</div></dd>
  </dl>
  <div class="clear"></div>
  
</div>