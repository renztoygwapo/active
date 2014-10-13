{if is_foreachable($_users) OR is_foreachable($_projects)}
  <div id="basecamp_import_form">
    {if is_foreachable($_users)}
      <div class="item">
        <div class="icon"><img src="{image_url name="icons/32x32/user.png" module="system"}"/></div>
        <div class="title">{lang num_users=$_users|count}:num_users users available for import{/lang}</div>
        <div class="action" id="button_holder_0">{button step_url=$active_data_source->getImportUrl() action="import_users" project_id='0'}Import{/button}</div>
      </div>
      <hr>
    {/if}

    {if is_foreachable($_projects)}
      {foreach $_projects as $project}
        <div class="item">
          <div class="icon"><img src="{image_url name="icons/32x32/project.png" module="system"}"/></div>
          <div class="title">
            {if $project->archived == true}
              <span class="aid">[archived]</span>
            {/if}
            {$project->name}
          </div>
          <div class="action" id="button_holder_{$project->id}">{button step_url=$active_data_source->getImportUrl() action="import_project" project_id=$project->id}Import{/button}</div>
        </div>
      {/foreach}
    {/if}

    <div class="already_imported_container">
      <p class="aid">
        {if $_imported_project_num == 0}
          {lang}You haven't imported any projects from this Basecamp account.{/lang}
        {else}
          {lang num=$_imported_project_num total_projects=$_projects_num}You have imported :num of :total_projects projects from this Basecamp account.{/lang}
        {/if}
      </p>
    </div>
  </div>
  {use_widget name="import_from_basecamp" module="data_sources"}
  <script type="text/javascript">
    var validate_url = {$_validate_url|json nofilter};
    $("#basecamp_import_form").importFromBasecamp({
      'validate_url' : validate_url
    });
  </script>
{else}
  <div id="basecamp_import_form_empty">
    <p class="details">{lang}You have imported all data from this Basecamp account{/lang}</p>
  <div>
{/if}



