{title lang=false}{$active_shared_notebook_page->getName()}{/title}

<div class="vcard shared_object_details">
	<div class="vcard_content">
		<div class="vcard_image">
			<div class="vcard_image_frame">
				<img src="{image_url name="icons/96x96/discussion.png" module=$smarty.const.DISCUSSIONS_MODULE interface=AngieApplication::INTERFACE_PHONE}" alt="">
			</div>
		</div>
		<div class="vcard_data">
			<div class="properties">
				<div class="property">
					<div class="label">{lang}Created{/lang}</div>
					<div class="content">{$active_shared_notebook_page->getCreatedOn()|date} by <a href="{$active_shared_notebook_page->getCreatedBy()->getViewUrl()}">{$active_shared_notebook_page->getCreatedBy()->getDisplayName()}</a></div>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="vcard_bottom"></div>
	<div class="vcard_bottom_shadow_left"></div>
	<div class="vcard_bottom_shadow_right"></div>
</div>

<div class="shared_notebook_wrapper">
  <div class="object_content">
    <div class="wireframe_content_wrapper">
	    <div class="object_body_content">
		    {if $active_shared_notebook_page->getBody()}
		      {$active_shared_notebook_page->getBody() nofilter}
		    {else}
		      {lang}Content not provided{/lang}
		    {/if}
		  </div>
	  </div>
  </div>
  
  {if is_foreachable($active_shared_notebook_page->getSubpages())}
	  <ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
	  	<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-subpages.png" module=$smarty.const.NOTEBOOKS_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Subpages{/lang}</li>
	    {$active_shared_object->sharing()->renderSubpages($active_shared_notebook_page, AngieApplication::INTERFACE_PHONE) nofilter}
	  </ul>
	{/if}
</div>

{if $active_shared_object->sharing()->supportsComments()}
  {shared_notebook_page_comments object=$active_shared_notebook_page user=$logged_user errors=$errors comment_data=$comment_data}
{/if}