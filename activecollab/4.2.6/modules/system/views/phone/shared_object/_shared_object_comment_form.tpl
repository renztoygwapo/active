<div class="shared_object_comment_form">
	{form action=$add_comment_url enctype="multipart/form-data"}
		{if $user instanceof IUser && (!$user instanceof AnonymousUser)}
      <div class="meta">
			  <input type="hidden" name="comment[created_by_name]" value="{$user->getDisplayName()}" />
			  <input type="hidden" name="comment[created_by_email]" value="{$user->getEmail()}" />
      </div>			  
		{else}
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