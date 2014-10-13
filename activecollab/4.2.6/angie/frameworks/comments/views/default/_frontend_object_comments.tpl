<div id="{$id}" class="frontend_object_comments">

	{if $post_comment_url}
	  <div id="frontend_object_comments_new" class="frontend_object_comments_new">
	  {form action=$post_comment_url enctype="multipart/form-data"}
	    {if $user instanceof User} 
	      <p>{lang name=$user->getDisplayName(true) url=$user->getViewUrl()}Logged in as <a href=":url">:name</a>{/lang}:</p>
	    {else}
		    {wrap field=created_by_name}
		      {text_field name="comment[created_by_name]" value=$comment_data.created_by_name label="Your Name" required=true}
		    {/wrap}
		    
		    {wrap field=created_by_email}
		      {text_field name="comment[created_by_email]" value=$comment_data.created_by_email label="Your Email" required=true}
		    {/wrap}
		  {/if}

		  {wrap field=body class="custom_editor_height"}
	      {editor_field name='comment[body]' id="{$comments_id}_comment_body" visual=false code_enabled=false ajax_submit_enabled=false label="Your Comment" required=true}{$comment_data.body nofilter}{/editor_field}
	    {/wrap}
	    
	    {wrap field=attachment}
	      {file label="Attachment"}
	    {/wrap}
	  
	    {wrap_buttons}
	      {submit}Comment{/submit}
	    {/wrap_buttons}
	  {/form}
	  </div>
	{/if}

	{if $object->comments()->countPublic()}
	  <div class="frontend_object_comments_existing">
	  {foreach $object->comments()->getPublic() as $comment}
	    <div class="frontend_object_comment {if $comment->getCreatedBy() instanceof User}registered{else}unregistered{/if}">
	   
	      <div class="frontend_object_comment_avatar">
	        <img src="{$comment->getCreatedBy()->avatar()->getUrl()}" alt="{lang name=$comment->getCreatedBy()->getDisplayName()}:name's Avatar{/lang}" />
	      </div>
	          
		    <div class="frontend_object_comment_content">
				  <div class="frontend_object_comment_meta">
				    {if $user instanceof User}
				      {lang author_name=$comment->getCreatedBy()->getDisplayName(true) author_url=$comment->getCreatedBy()->getViewUrl() on=$comment->getCreatedOn()->formatForUser()}By <a class="comment_author" href=":author_url">:author_name</a> on <span class="comment_date">:on</span>{/lang}
				    {else}
				      {lang author_name=$comment->getCreatedBy()->getDisplayName(true) on=$comment->getCreatedOn()->formatForUser()}By <span class="comment_author" >:author_name</span> on <span class="comment_date">:on</span>{/lang}
				    {/if}
				  </div>
	        
			    <div class="frontend_object_comment_body">{$comment->getBody()|rich_text:frontend nofilter}</div>
				    {if $comment->attachments()->count()}
				      <div class="frontend_object_comment_body">
				        <p>{lang}Attachments{/lang}:</p>
				        <ul>
				        {foreach $comment->attachments()->getPublic() as $attachment}
				          <li><a href="{$attachment->getPublicViewUrl()}">{$attachment->getName()}</a></li>
				        {/foreach}
		            </ul>
		          </div>
		        {/if}
		      </div>
	      </div>
	  {/foreach}
    </div>
	{else}
    <p class="empty_page">
    {if $post_comment_url}
      {lang}No comments yet{/lang}
    {else}
      <span class="object_comments_locked" style="display: block;">
        <img src="{image_url name='icons/24x24/comments-locked.png' module=$smarty.const.COMMENTS_FRAMEWORK}" alt=""/>
        {lang}Comments for this quote are locked{/lang}
      </span>
    {/if}
    </p>
	{/if}
</div>