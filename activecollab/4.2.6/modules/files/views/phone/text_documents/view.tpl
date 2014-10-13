{add_bread_crumb}Text Document Details{/add_bread_crumb}

{object object=$active_asset user=$logged_user}
  <div class="wireframe_content_wrapper">
		<div class="object_content_wrapper"><div class="object_body_content">
			<p class="comment_details ui-li-desc">
				{if $active_asset->inspector()->hasBody()}
          {$active_asset->inspector()->getBody() nofilter}
        {else}
          {lang}No description provided{/lang}
        {/if}
			</p>
		</div></div>
	</div>
	
	{object_comments object=$active_asset user=$logged_user interface=AngieApplication::INTERFACE_PHONE id=text_document_comments}
  
  {if $active_asset->getState() == $smarty.const.STATE_VISIBLE}
  	{render_comment_form object=$active_asset id=text_document_comments}
  {/if}
{/object}