{title lang=false}{$active_job_type->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

<div id="job_type" class="wireframe_content_wrapper settings_panel">
  <div class="settings_panel_header">
    <table class="settings_panel_header_cell_wrapper">
      <tr>
        <td class="settings_panel_header_cell">
          <h2>{lang}Job Type Details{/lang}</h2>
		      <div class="properties">
		        <div class="property" id="tasks_setting_auto_reopen">
		          <div class="label">{lang}Name{/lang}</div>
		          <div class="data">{$active_job_type->getName()}</div>
		        </div>
		        
		        <div class="property" id="tasks_setting_public_forms_enabled">
		          <div class="label">{lang}Default Hourly Rate{/lang}</div>
		          <div class="data">{$active_job_type->getDefaultHourlyRate()|money}</div>
		        </div>
		      </div>
        </td>
      </tr>
    </table>
  </div>
  
  <div class="settings_panel_body">
  {if $projects_with_custom_hourly_rate}
    <p>{lang}Following projects don't use default hourly rate, but have a custom rate set{/lang}:</p>
    <table class="common" cellspacing="0">
      <thead>
        <tr>
          <th class="project">{lang}Project{/lang}</th>
          <th class="client">{lang}Client{/lang}</th>
          <th class="hourly_rate">{lang}Hourly Rate{/lang}</th>
        </tr>
      </thead>
      <tbody>
    	{foreach $projects_with_custom_hourly_rate as $project}
      <tr>
      	<td class="name"><a href="{assemble route=project project_slug=$project.project_slug}">{$project.project_name}</a></td>
      	<td class="client">
      	{if $project.company_id && $project.company_name}
      	  <a href="{assemble route=people_company company_id=$project.company_id}">{$project.company_name}</a>
      	{else}
      	  <span class="details">{lang}Internal{/lang}</span>
      	{/if}
      	</td>
      	<td class="hourly_rate">{$project.hourly_rate|money}</td>
      </tr>
    	{/foreach}
      </tbody>
    </table>
    <p>{lang}Tip: To change the custom value, or revert back to default hourly rate, please go to project overview page and select Hourly Rates option from Options drop-down{/lang}.</p>
  {else}
    <p class="empty_page">{lang}All projects are using default hourly rate for this job type{/lang}</p>
  {/if}
  </div>
</div>