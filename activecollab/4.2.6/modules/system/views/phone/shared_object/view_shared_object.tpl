{title lang=false}{$active_shared_object->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

{if ($show_instructions)}
  <div class="wireframe_content_wrapper shared_object_instructions">
    <div class="object_body_content">
      <p>{lang}Thank you for getting in touch.{/lang}</p>
      <p>{lang}One of our staff members will answer your inquiry soon.{/lang}</p>
      <p>{lang}You will also receive a confirmation email with details of how to track the progress of your enquiry. If you do not receive this email please check your junk mail folder.{/lang}</p>
    </div>
  </div>

  <script type="text/javascript">
    $.cookie('{$cookie_name}', null);
  </script>
{/if}

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
					<div class="content">{$active_shared_object->getCreatedOn()|date} by <a href="{$active_shared_object->getCreatedBy()->getViewUrl()}">{$active_shared_object->getCreatedBy()->getDisplayName()}</a></div>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="vcard_bottom"></div>
	<div class="vcard_bottom_shadow_left"></div>
	<div class="vcard_bottom_shadow_right"></div>
</div>

{if $active_shared_object->sharing()->displayAsDiscussion() && $active_shared_object->sharing()->supportsComments() && $active_shared_object instanceof IComments}
	{shared_object_comments object=$active_shared_object user=$logged_user errors=$errors comment_data=$comment_data object_as_first_comment=true interface=AngieApplication::INTERFACE_PHONE}
{else}
  {shared_object object=$active_shared_object}
    {if $active_shared_object->sharing()->supportsComments() && $active_shared_object instanceof IComments}
    	{shared_object_comments object=$active_shared_object user=$logged_user errors=$errors comment_data=$comment_data interface=AngieApplication::INTERFACE_PHONE}
    {/if}
  {/shared_object}
{/if}