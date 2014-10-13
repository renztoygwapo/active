{$context->comments()->getNotificationSubjectPrefix()}{lang object_name=$context->getName() object_type=$context->getVerboseType(true, $language) language=$language}New comment on ':object_name' :object_type{/lang}
================================================================================
<table width="654" cellpadding="0" cellspacing="0" border="0" align="center" id="mainTable" style="width:654px; margin: 0 auto; border-spacing: 0; border-collapse:separate; border:1px solid #d0d2c9; -webkit-border-radius: 20px; -moz-border-radius: 20px; border-radius: 20px; text-align:left;">
	<tr>
		<td>      		 
			<table cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:separate; padding: 15px; -webkit-border-radius: 20px; -moz-border-radius: 20px; border-radius: 20px;" id="inspector">																	
				<tr>
					<td class="branding" style="text-align: center; padding:10px;">
						{notification_identity context=$context recipient=$recipient sender=$sender show_site_name=false}
					</td>
					{notification_inspector context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="width: 654px; background-color:#ffffff; border-collapse:separate; -webkit-border-bottom-right-radius: 20px; -webkit-border-bottom-left-radius: 20px; -moz-border-radius-bottomright: 20px; -moz-border-radius-bottomleft: 20px; border-bottom-right-radius: 20px; border-bottom-left-radius: 20px;">					
			<table border="0" cellpadding="0" cellspacing="0" class="ExternalClass" style="font-family: Lucida Grande, Verdana, Arial, Helvetica, sans-serif; font-size:12px;">
				<tr><td colspan="2" style="border-top:1px solid #d7d8cf; height:1px; line-height:1px; width:654px;"></td></tr>
				{assign var=latest_comments value=$context->comments()->getLatest($recipient, 5)}
				
				{foreach from=$latest_comments item=comm name=comments}
					<tr>
						<td class="avatar" style="padding: 15px; vertical-align: top; text-align:center; width:40px;"><a href="{$comm->getCreatedBy()->getViewUrl()}"><img src="{$comm->getCreatedBy()->avatar()->getUrl(IProjectAvatarImplementation::SIZE_BIG)}" alt="avatar"></a></td>
						<td style="padding: 15px 15px 15px 0;">
							<div style="padding-bottom: 5px;"><a href="{$comm->getCreatedBy()->getViewUrl()}" style="{$style.link} font-weight: bold;">{$comm->getCreatedBy()->getDisplayName(true)}</a> &nbsp; {$comm->getCreatedOn()->formatDateForUser($recipient)}{if $comment->getId() == $comm->getId()} &nbsp; <span style="font-weight: bold;">{lang language=$language}New!{/lang}</span>{/if}</div>
							<div>
								{$comm->getBody()|rich_text:'notification' nofilter}
								{notification_attachments_table object=$comm recipient=$recipient}
							</div>
						</td>
					</tr>
					
					{if $smarty.foreach.comments.last}
						<tr><td colspan="3" style="padding: 15px; color: #999; text-align: center;">
							{assign var=total_comments value=$context->comments()->count($recipient)}
							
							{if $total_comments == 1}
			          {lang language=$language}This is the First Comment in the Thread{/lang}
			          {elseif $total_comments > 5}
			          	{if $context_view_url}
			            	{lang total=$total_comments url=$context_view_url link_style=$style.link language=$language}Showing <a href=":url" style=":link_style">5 out of :total Comments</a>{/lang}
			            {else}
			            	{lang total=$total_comments language=$language}Showing 5 out of :total Comments{/lang}
			          	{/if}
			          {else}
			          {lang total=$total_comments language=$language}Showing All :total Comments{/lang}
			        {/if}
						</td></tr>
					{else}
						<tr><td colspan="3" height="1" style="width: 654px;"><div style="height:1px; border-bottom:1px solid #d7d8cf; padding:0; height:1; line-height:1;"></div></td></tr>
					{/if}
	      {/foreach}              						
			</table>
		</td>
	</tr>
</table>