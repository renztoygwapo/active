{title}Project Budget vs Cost{/title}
{add_bread_crumb}Budget vs Cost{/add_bread_crumb}

<div id="budget_vs_cost">
{if $projects}
  <table class="common" cellspacing="0">
    <thead>
      <tr>
        <th class="project">{lang}Project{/lang}</th>
        <th class="budget center">{lang}Budget{/lang}</th>
        <th class="cost_so_far center">{lang}Cost so Far{/lang}</th>
        <th class="status right">{lang}Status{/lang}</th>
      </tr>
    </thead>
    <tbody>
    {foreach $projects as $project}
      <tr>
        <td class="project quick_view_item">{project_link project=$project}</td>
        <td class="budget center">{$project->getBudget()|money:$project->getCurrency()}</td>
        <td class="cost_so_far center"><a href="{assemble route=project_budget project_slug=$project->getSlug()}">{$project->getCostSoFar($logged_user)|money:$project->getCurrency()}</a></td>
        <td class="status right">
        {if $project->getCostSoFarInPercent() > 100}
          <span class="project_budget cost_over_budget">{lang}Over Budget{/lang}</span>
        {elseif $project->getCostSoFarInPercent() >= 90}
          <span class="project_budget cost_close_to_budget">{lang}Close to Budget{/lang}</span>
        {else}
          <span class="project_budget cost_ok">{lang}OK{/lang}</span>
        {/if}
        </td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{else}
  <p class="empty_page">{lang}There are no projects with budget value set. Please, set budget values for your projects in order to use this report{/lang}</p>
{/if}
</div>