{title lang=false}{$active_project_request->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

{if $request_submitted || $project_request_expired}
  <div class="wireframe_content_wrapper">
    <div class="object_body_content page_message">
      {if $request_submitted}
        <p class="ok">{lang}Thank you for submitting project request.{/lang}</p>
        <p class="ok">{lang}Note: Make sure to bookmark this URL so you can track your request's progress:{/lang}</p>
        <p>{lang request_url=$active_project_request->getPublicUrl()}<a href=":request_url">:request_url</a>{/lang}</p>
      {/if}

      {if $project_request_expired}
        <p class="nok">{lang}Important: This project request is marked as closed, so access to this page is no longer available to clients{/lang}</p>
      {/if}
    </div>
  </div>
{/if}

<div class="vcard project_request_details">
	<div class="vcard_content">
		<div class="vcard_image">
			<div class="vcard_image_frame">
				<img src="{image_url name="icons/96x96/project.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" alt="">
			</div>
		</div>
		
		<div class="vcard_data">
			<div class="properties">
				<div class="property">
					<div class="label">{lang}Requested By{/lang}</div>
					<div class="content">{user_link user=$active_project_request->getCreatedBy()} ({$active_project_request->getCreatedByCompanyName()})</div>
				</div>
				
				{foreach $active_project_request->getCustomFields() as $custom_field_key => $custom_field}
					<div class="property">
						<div class="label">{$custom_field.label}</div>
						<div class="content">{if $custom_field.value}{$custom_field.value}{else}--{/if}</div>
					</div>
				{/foreach}
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="vcard_bottom"></div>
	<div class="vcard_bottom_shadow_left"></div>
	<div class="vcard_bottom_shadow_right"></div>
</div>

<div class="body">
	<div class="shared_text_document">
		<div class="object_content">
			{if $active_project_request->getBody()}
				<div class="wireframe_content_wrapper">
					<div class="object_body_content">
						{$active_project_request->getBody()|rich_text nofilter}
					</div>
				</div>
			{/if}
		</div>
	</div>
</div>

{frontend_object_comments object=$active_project_request user=$logged_user errors=$errors post_comment_url=$active_project_request->getPublicUrl() comment_data=$comment_data}