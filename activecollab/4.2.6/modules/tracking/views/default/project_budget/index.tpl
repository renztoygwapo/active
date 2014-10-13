{title}Project Budget{/title}
{add_bread_crumb}Budget{/add_bread_crumb}
{use_widget name=properties_list module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="project_budget_details" class="object_inspector properties_list">
  <div class="head">
    <div class="properties">
    
      <!--  Budget -->
      <div class="property">
        <div class="label">{lang}Budget{/lang}</div>
        <div class="data">{$budget|money:$project_currency}</div>
      </div>
      
      <!-- Cost so Far -->
      <div class="property">
        <div class="label">{lang}Cost so Far{/lang}</div>
        <div class="data">
        {if $budget > 0}
          {if $cost_so_far_perc > 100}
            <span class="project_budget cost_over_budget"><span class="amount">{$cost_so_far|money:$project_currency}</span> ({lang percent=$cost_over_budget_perc}:percent% over budget{/lang})</span>
          {else if cost_so_far_perc > 90}
            <span class="project_budget cost_close_to_budget"><span class="amount">{$cost_so_far|money:$project_currency}</span> ({lang percent=$cost_so_far_perc}:percent%{/lang})</span>
          {else}
            <span class="project_budget cost_ok"><span class="amount">{$cost_so_far|money:$project_currency}</span> ({lang percent=$cost_so_far_perc}:percent%{/lang})</span>
          {/if}
        {else}
          {$cost_so_far|money:$project_currency}
        {/if}
        </div>
      </div>
      
    </div>
  </div>
  
  <div class="body">
  {if is_foreachable($cost_by_type)}
    <p>{lang}Cost per Type{/lang}:</p>
    <table class="common" cellspacing="0">
      <thead>
        <tr>
          <th>{lang}Type{/lang}</th>
          <th class="center">{lang}Hourly Rate{/lang}</th>
          <th class="center">{lang}Billable Hours{/lang}</th>
          <th class="right">{lang}Cost so Far{/lang}</th>
        </tr>
      </thead>
      <tbody>
      {foreach $cost_by_type as $job_type => $job_type_cost}
        <tr job_type_id="{$job_type}">
          <td class="name">{$job_type_cost.name}</td>
        {if $job_type_cost.is_time}
          <td class="hourly_rate center">{$job_type_cost.rate|money:$project_currency}</td>
          <td class="hours center">{$job_type_cost.hours|hours}</td>
        {else}
          <td class="hourly_rate center"></td>
          <td class="hours center"></td>
        {/if}
          <td class="cost right">{$job_type_cost.value|money:$project_currency}</td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  {/if}
  </div>
</div>