{title lang=false}{$active_notebook_page->getName()}{/title}
{add_bread_crumb}Latest Version{/add_bread_crumb}

<div class="notebook_page">
	{object object=$active_notebook_page user=$logged_user show_body=false}
		<div class="wireframe_content_wrapper">
			<div class="object_content_wrapper"><div class="object_body_content">
				<p class="comment_details ui-li-desc">
					{if $active_notebook_page->inspector()->hasBody()}
	          {$active_notebook_page->inspector()->getBody() nofilter}
	        {else}
	          {lang}No description provided{/lang}
	        {/if}
				</p>
			</div></div>
			
			{object_attachments object=$active_notebook_page user=$logged_user}
			
			<div id="notebook_page_subpages">
				{list_subpages parent=$active_notebook_page subpages=$subpages}
			</div>
			
			{object_comments object=$active_notebook_page user=$logged_user interface=AngieApplication::INTERFACE_PHONE id=notebook_page_comments}
			
			{if $active_notebook_page->getState() == $smarty.const.STATE_VISIBLE}
		  	{render_comment_form object=$active_notebook_page id=notebook_page_comments}
		  {/if}
		</div>
	{/object}
</div>