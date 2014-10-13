{title}File Source{/title}
{add_bread_crumb}File Source{/add_bread_crumb}

<div class="object_wrapper">
  <div class="wireframe_content_wrapper first">
    <div class="object_inspector with_actions">
      <div class="head">
      
        <table cellspacing="0" class="inspector_table">
          <tr>
            <td class="properties">
              <div class="property">
                <div class="label">{lang}Revision ID{/lang}</div>
                <div class="content">{$active_commit->getName()}</div>
              </div>
              <div class="property">
                <div class="label">{lang}Revision Author{/lang}</div>
                <div class="content">{$active_commit->getAuthor() nofilter}</div>
              </div>
              <div class="property">
                <div class="label">{lang}Path{/lang}</div>
                <div class="content">{$navigation nofilter}</div>
              </div>
              <div class="property">
                <div class="label">{lang}Revision Details:{/lang}</div>
                <div class="content">
					        {if $latest_revision->getMessageBody()}
					          {$latest_revision->getMessageBody()|nl2br|clickable|stripslashes nofilter}
					        {else}
					          {lang}Commit message was not provided{/lang}
					        {/if}
                </div>
              </div>
              {if $source}
                <div class="property">
                  <div class="label">{lang}File Size{/lang}</div>
                  <div class="content">{format_file_size(strlen($source))}</div>
                </div>
              {/if}
            </td>
          </tr>
        </table>
        
        <ul class="actions">
          <li>
            <a href="{$project_object_repository->getFileDownloadUrl($active_commit, $active_file)}" target="_blank" title="Click to Download"><img src="{image_url name='icons/12x12/download.png' module=$smarty.const.ENVIRONMENT_FRAMEWORK}">{lang}Download{/lang}</a>
          </li>
        </ul>
        
      </div>
    </div>
  </div>
  
	<div class="wireframe_content_wrapper">
  
		<div class="wireframe_content_wrapper source_navbar">
		  <h3>{lang}Viewing File Source{/lang}</h3>
		</div>
		
		<div id="repository_file" class="repository_file">
		    {if $path_info === false}
		      <p class="empty_page"><span class="inner">{lang}This file/directory does not exist in this revision.{/lang}</span></p>
		    {else}
		      	{if $file_type === 'text'}
		          <div class="file_source">
		            {HyperlightForAngie::htmlPreview($source, $syntax) nofilter}
		          </div>
            {elseif $file_type === 'image'}
              <img class="browse_file_image" alt="{$active_file}" src="data:{$image_mime_type};base64,{$image_base64}"/>
		        {else}
		          <p class="empty_page">
                {lang}File cannot be displayed or larger than 1MB{/lang}
                <br />
                <a href="{$project_object_repository->getFileDownloadUrl($active_commit, $active_file)}" title="Click to Download" target="_blank">{lang}Download File{/lang}</a>
              </p>
		        {/if}
		    {/if}
		</div>

	
	</div>
</div>



