{title id=$active_status_update->getId()}Status Update #:id{/title}
{add_bread_crumb}View{/add_bread_crumb}

<div id="status_update_details">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="p">
		<li>
			<img class="ui-li-icon" src="{$active_status_update_author->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)}" alt=""/>
			<p class="comment_details ui-li-desc">By <a class="ui-link" href="{$active_status_update_author->getViewUrl()}">{$active_status_update_author->getDisplayName(true)}</a>, on {$active_status_update->getCreatedOn()|datetime}</p>
			<p class="comment_overflow ui-li-desc">{$active_status_update->getMessage()}</p>
		</li>
		
		{if $active_status_update->hasReplies(true)}
		  {foreach from=$active_status_update->getReplies() item=status_update_reply}
		    {assign var=status_update_reply_user value=$status_update_reply->getCreatedBy()}
		    <li class="reply">
		    	<img class="ui-li-icon" src="{$status_update_reply_user->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL)}" alt=""/>
		    	<p class="comment_details ui-li-desc">By <a class="ui-link" href="{$status_update_reply_user->getViewUrl()}">{$status_update_reply_user->getDisplayName(true)}</a>, on {$status_update_reply->getCreatedOn()|datetime}</p>
					<p class="comment_overflow ui-li-desc">{$status_update_reply->getMessage()}</p>
		    </li>
		  {/foreach}
		{/if}
	</ul>
</div>

<div id="status_update_reply">
  {form action=Router::assemble('status_updates_add')}
    {wrap_editor field=message}
		  {editor_field name='status_update[message]' label='Update Status' id=status_message_reply}{$reply_data.message nofilter}{/editor_field}
		{/wrap_editor}
		
		<input type="hidden" name="status_update[parent_id]" value="{$active_status_update->getId()}"/>
    
    {wrap_buttons}
      {submit}Update{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
	$(document).ready(function() {
		var comment_block = $('#status_update_reply');
    
    comment_block.find('textarea').each(function() {
      var comment = $(this).val();
      
      comment_block.find('form').submit(function() {
			  var comment_value = jQuery.trim(comment.val());
	      if(comment_value) {
	        $('#status_update_replies').listview('refresh');
	        return true;
	      } // if
	      
			  return false;
			});
    });
  });
</script>