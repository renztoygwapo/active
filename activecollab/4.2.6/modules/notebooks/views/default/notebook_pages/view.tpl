{add_bread_crumb}Latest Version{/add_bread_crumb}

{object object=$active_notebook_page user=$logged_user show_body=false id=notebook_page_details}
  <div class="wireframe_content_wrapper">
    <div class="object_notebook_page">
  	  <div class="object_notebook_page_left_side">
  	    <div class="object_notebook_page_left_side_spiral"></div>
  	  </div>
  	  
  	  <div class="object_body">
  	    <div class="object_content_wrapper">
  	      <div class="object_body_content formatted_content">
  	       {if ($active_notebook_page->getBody())}
		          {$active_notebook_page->getBody()|rich_text nofilter}
		        {else}
		          {lang}No description for this task{/lang}
		        {/if}
  	      </div>
  	      
  	      {object_attachments object=$active_notebook_page user=$logged_user class=main_object_attachments}  	      
  	    </div>
  	  </div>
    </div>
  </div>
  
  <!-- Comments -->
  <div class="wireframe_content_wrapper">{object_comments object=$active_notebook_page user=$logged_user show_first=yes}</div>
  
  <!-- Versions -->
  <div class="wireframe_content_wrapper">
    <div class="notebook_page_versions object_content_section">
        <div class="content_section_title"><h2>{lang}Page Versions{/lang}</h2></div>
        <div class="content_section_container">
  	       {notebook_page_versions page=$active_notebook_page user=$logged_user}
        </div>
  	</div>
  </div>
{/object}