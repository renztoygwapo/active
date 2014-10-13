<div id="print_container">
{title}{$page_title}{/title}

{if is_foreachable($projects)}
	
	{foreach from=$projects key=map_name item=project_list name=print_project}
		<h2>{$map_name}</h2>
		<table class="projects_table common" cellspacing="0">
          <thead>
            <tr>
              <th class="project_id" align="left">{lang}ID{/lang}</th>
              <th class="label" align="left" style="width:100px;">{lang}Label{/lang}</th>
              <th class="name">{lang}Project Name{/lang}</th>
              <th class="status" align="right" style="text-align: right;">{lang}Status{/lang}</th>
            </tr>
          </thead>
          <tbody>
		  {foreach from=$project_list item=project}	
		  <tr>
		    <td class="project_id" align="left">#{$project->getId()}</td>
		    <td class="label" align="left">
          {if $project->label()->get() instanceof Label}
            {$project->label()->get()->getName()}
          {else}
            -
          {/if}
        </td>
		    <td class="name">{$project->getName()}</td>
		    <td class="status" align="right">
          {if $project->complete()->isCompleted()}
            {lang}Completed{/lang}
          {else}
            {lang}Active{/lang}
          {/if}
        </td>
		  </tr>
		  {/foreach}
		 </tbody>
	  </table>
	{/foreach}
{else}
	<p>{lang}There are no Projects that match this criteria{/lang}</p>	
{/if}

</div>