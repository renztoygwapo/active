{title}Source Version Control{/title}
{add_bread_crumb}Repositories{/add_bread_crumb}

  <div id="repository_index" class="repository_listing">
  {if is_foreachable($repositories)}
    <table class="common" cellspacing="0">
      <tr>
        <th></th>
        <th class="graph">{lang}Activity Graph{/lang}</th>
        <th>{lang}Repository Name{/lang}</th>
        <th class="last_commit">{lang}Last Commit{/lang}</th>
        <th></th>
        <th></th>
      </tr>
    {foreach $repositories as $repository}
      {include file=get_view_path('_repository_row', 'repository', $smarty.const.SOURCE_MODULE)}
    {/foreach}
    </table>
  {else}
    <p class="empty_page"><span class="inner">{lang}There are no repositories added{/lang}.</span></p>
  {/if}
  </div>
  
  <script type="text/javascript">
    // reload the page as we need to render complex things like graphs
	  App.Wireframe.Content.bind('repository_created.content', function(event, repository) {
	    App.Wireframe.Content.setFromUrl('{assemble route=project_repositories project_slug=$active_project->getSlug()}');
	  });
  </script>