{add_bread_crumb}Discussion{/add_bread_crumb}

{object object=$active_discussion user=$logged_user show_body=false}
  <div id="discussion{$active_discussion->getId()}" class="wireframe_content_wrapper">
    {if $active_discussion->hasBody()} 
  		<div class="discussion_starter">
  		  <div class="discussion_starter_avatar_container">
  		    <a style="background-image: url({$active_discussion->getCreatedBy()->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)});" class="avatar" href="{$active_discussion->getCreatedBy()->getViewUrl()}">
  		      <img alt="avatar" src="{$active_discussion->getCreatedBy()->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)}">
  		    </a>
  		  </div>
  		
  		  <div class="discussion_content">
  		    <div class="discussion_starter_meta">
  		      <span class="author">{user_link user=$active_discussion->getCreatedBy()}</span>
  		      <span class="date">{$active_discussion->getCreatedOn()|ago nofilter}</span>
  		    </div>
  		
  		    <div>
  		      <div id="discussions_body_{$active_discussion->getId()}" class="object_body_content formatted_content">
  		        {$active_discussion->getBody()|rich_text nofilter}
  		      </div>
  		      
  		      {if $active_discussion->getSource() == $smarty.const.OBJECT_SOURCE_EMAIL}
  		        <script type="text/javascript">
  		          App.EmailObject.init('discussions_body_{$active_discussion->getId()}');
  		        </script>
  		      {/if}
  		      {object_attachments object=$active_discussion user=$logged_user brief=yes}
  		    </div>
  		  </div>
  		</div>
    {/if}
  </div>
  
  <div class="wireframe_content_wrapper">
    {object_comments object=$active_discussion user=$logged_user}
  </div>
{/object}
<script type="text/javascript">
  // mark the discussion as read in the objects list
  (function() {
    var active_discussion = {$active_discussion|json nofilter};
    active_discussion['is_read'] = 1;
    $('#discussions').each(function() {
    	$(this).objectsList('update_item', active_discussion, true);
    	App.Wireframe.PageTitle.set(active_discussion['name']);
    });
  })();
</script>