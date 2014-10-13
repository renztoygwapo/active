<div id="{$id}" class="frontend_object_comments">
	<div id="frontend_object_comments_list">
		{if $object->comments()->countPublic()}
			<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="p">
				<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate.png" module=$smarty.const.COMMENTS_FRAMEWORK interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Comments{/lang}</li>
				{foreach $object->comments()->getPublic() as $comment}
					<li class="{if $comment->getCreatedBy() instanceof User}registered{else}anonymous{/if}">
						<img src="{$comment->getCreatedBy()->avatar()->getUrl()}" class="ui-li-icon" alt="{lang name=$comment->getCreatedBy()->getDisplayName()}:name's avatar{/lang}">
						<p class="comment_details ui-li-desc">By <a class="ui-link" href="{$comment->getCreatedBy()->getViewUrl()}">{$comment->getCreatedBy()->getDisplayName(true)}</a> on {$comment->getCreatedOn()|date}</p>
						<div class="comment_overflow ui-li-desc">{$comment->getBody()|rich_text:frontend nofilter}</div>
						
						{if $comment->attachments()->count()}
					    {foreach $comment->attachments()->getPublic() as $attachment}
					    	<div class="comment_attachment">
					      	<a href="{$attachment->getPublicViewUrl(true)}"><img src="{$attachment->preview()->getLargeIconUrl()}" /><span class="filename">{$attachment->getName()}</span></a>
					      </div>
					    {/foreach}
					  {/if}
					</li>
				{/foreach}
			</ul>
		{/if}
	</div>
	
	{if $post_comment_url}
	  <div class="frontend_object_comments_new">
		  {form action=$post_comment_url enctype="multipart/form-data"}
				{if !($user instanceof User)}
				  {wrap field=created_by_name}
				    {text_field name="comment[created_by_name]" value=$comment_data.created_by_name label="Your Name" id="user_name" required="true"}
				  {/wrap}
				  
				  {wrap field=created_by_email}
				  	{text_field name="comment[created_by_email]" value=$comment_data.created_by_email label="Your Email Address" id="user_email" required="true"}
				  {/wrap}
				{/if}
		    
			  {wrap field=body}
			    {textarea_field name="comment[body]" label="Your Comment" required="true"}{$comment_data.body nofilter}{/textarea_field}
			  {/wrap}
			  
			  {wrap_buttons}
			    {submit}Post Comment{/submit}
			  {/wrap_buttons}
			{/form}
	  </div>
	{/if}
</div>

{if !$post_comment_url}
	<div class="object_comments_locked">
    <img src="{image_url name='icons/32x32/comments-locked.png' module=$smarty.const.COMMENTS_FRAMEWORK interface=AngieApplication::INTERFACE_PHONE}" alt=""/>
    {lang object_type=$object->getVerboseType()|lower}Comments for this :object_type are locked{/lang}
  </div>
{/if}