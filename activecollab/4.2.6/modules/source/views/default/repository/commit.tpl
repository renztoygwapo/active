{title}Revision Details{/title}
{add_bread_crumb}Revision Details{/add_bread_crumb}

{object object=$active_commit user=$logged_user repository=$project_object_repository}
  {if !(RepositoryEngine::pathInFolder($active_file))}
	  <div class="wireframe_content_wrapper">
	  	<div id="commit_info" class="commit_files">
	  	  <ul>
	  	    {foreach from=$grouped_paths item=path name=files_list key=action}
	  	      {foreach from=$path item=item}
	  	        <li>
	  	          <span class="{$action|source_module_get_state_name}">{$action|source_module_get_state_label}</span>
	  	          <a href="{$project_object_repository->getBrowseUrl($active_commit, $item, $active_commit->getRevisionNumber())}">{$item}</a>
	  	        </li>
	  	      {/foreach}
	  	    {/foreach}
	  	  </ul>
	  	</div>
	  </div>
  {/if}

  <div id="repository_commit" class="wireframe_content_wrapper">
    <div id="source" class="browser">
    {if is_foreachable($diff)}
      {foreach $diff as $key=>$file}
      <div id="{$file.file}">
  			<div class="wireframe_content_wrapper source_navbar">
  			  <h3>{$file.file}</h3>
  			</div>
  	    <div class="file_diff" id="file_{$key}">
  	      <div class="lines" valign="top"><pre>{$file.lines}</pre></div>
          <div class="source" valign="top"><pre><table cellspacing="0"><tr><td>{$file.content nofilter}</td></tr></table></pre></div>
  	    </div>
      </div>
      {/foreach}
    {else}
      {if $error}
        <p class="empty_slate">
          {$message nofilter}
        </p>
        {if $show_paths}
          <div id="show_files" class="loading_div" commit_paths_url = '{assemble route=repository_commit_paths project_slug=$active_project->getSlug() project_source_repository_id=$project_object_repository->getId() r=$active_commit->getRevisionNumber()}'>
            {lang}Loading commited files{/lang}... <img id="first_indicator" alt="{lang}Loading{/lang}..." />
          </div>
        {/if}
      {else}
        <p class="empty_page">{lang}No diff available{/lang}</p>
      {/if}
    {/if}
    </div>
  </div>
{/object}

<script type="text/javascript">
  var show_paths = '{$show_paths}';


  if (show_paths) {
    var show_files_wrapper = $('#show_files');
    $('#first_indicator').attr('src',App.Wireframe.Utils.indicatorUrl());
    $.get(show_files_wrapper.attr('commit_paths_url'), function(data) {
      show_files_wrapper.html(data);
      show_files_wrapper.find('p.commit_details').remove();
    });
  } //if
</script>