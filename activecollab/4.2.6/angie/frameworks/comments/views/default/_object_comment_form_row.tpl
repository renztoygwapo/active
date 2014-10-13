{if $comment->isLoaded()}
<div class="comment edit_comment quick_comment_form" id="{$comments_id}" style="display: none">
{else}
<div class="comment new_comment quick_comment_form" id="{$comments_id}" style="display: none">
{/if}

  <div class="comment_avatar_container">
		{if $comment->isLoaded()}
	    <span style="background-image: url({$comment->getCreatedBy()->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)});" class="avatar">
	      <img src="{$comment->getCreatedBy()->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)}" alt="avatar" />
	    </span>
	  {else}
	    <span style="background-image: url({$user->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)});" class="avatar">
	      <img src="{$user->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)}" alt="avatar" />
	    </span>
	  {/if}
  </div>

  <div class="comment_content_container">
    <div class="body">
    {if $comment->isLoaded()}
      <form action="{$comment->getEditUrl()}" method="post" enctype="multipart/form-data">
    {else}
    	<form action="{$comment_parent->comments()->getPostUrl()}" method="post" enctype="multipart/form-data">
  	  {if $user instanceof AnonymousUser}
  	    {wrap field=created_by_name}
  	      {text_field name="comment[created_by_name]" value=$comment_data.created_by_name label="Your Name" required=true}
  	    {/wrap}
  	    
  	    {wrap field=created_by_email}
  	      {text_field name="comment[created_by_email]" value=$comment_data.created_by_email label="Your Email" required=true}
  	    {/wrap}
  	  {/if}
    {/if}
    
        <input type="hidden" name="submitted" value="submitted" />
        
        <div class="expandable_editor">
          <div class="real_textarea">
            {wrap field=body class="comment_visual_editor"}
              {editor_field name='comment[body]' id="{$comments_id}_comment_body" resize="true" label="Your Comment" required=true object=$comment_parent headings_enabled=false}{$comment_data.body nofilter}{/editor_field}
            {/wrap}
            
            {wrap field=attachments class="attachments_field_wrapper"}
              {select_attachments name="comment[attachments]" object=$comment user=$user}
            {/wrap}
            
            <div class="comment_subscribers"></div>
            
            <div class="comment_form_buttons_wrapper button_holder">
              {if $comment->isNew()}
	              <div class="comment_form_main_buttons">
                  {submit}Comment{/submit} {lang}or{/lang} <a href="#" class="comment_cancel">{lang}Cancel{/lang}</a>
	              </div>
	              
	              <div class="comment_form_additional_buttons">
		              {if ($comment_parent->canEdit($user) && (($comment_parent instanceof IComplete && $comment_parent->complete()->canChangeStatus($user)) || $comment_parent instanceof ILabel || $comment_parent instanceof ICategory))}
                    {lang type=$comment_parent->getVerboseType(true, $user->getLanguage())}also, update :type{/lang}
			              {if $comment_parent instanceof IComplete && $comment_parent->complete()->canChangeStatus($user)}
			                {select_completion_status name="parent[is_completed]" label="Status" label_type="inner" value=$comment_parent->complete()->isCompleted() id=parent_completion}<span>|</span>
                      {select_assignee name="parent[assignee_id]" parent=$comment_parent user=$user value=$comment_parent->getAssigneeId() id="parent_assignee_id"}<span>|</span>
			              {/if}
			              {if $comment_parent instanceof ILabel && $comment_parent->canEdit($user)}
			                {select_label name="parent[label_id]" type=$comment_parent->label()->getLabelType() user=$user label='Label' label_type="inner" value=$comment_parent->getLabelId() id=parent_label_id}<span>|</span>
			              {/if}
			              {if $comment_parent instanceof ICategory && $comment_parent->canEdit($user)}
			                {select_category name='parent[category_id]' parent=$comment_parent->category()->getCategoryContext() type=$comment_parent->category()->getCategoryClass() user=$user label='Category' label_type="inner" success_event="category_created" value=$comment_parent->getCategoryId() id="parent_category_id"}
			              {/if}
		              {/if}
	              </div>
              {else}
                {submit}Save Changes{/submit} {lang}or{/lang} <a href="#" class="comment_cancel">{lang}Cancel{/lang}</a>
              {/if}
            </div>
          </div>
        </div>
        
        
        
        
      </form>
    </div>
  </div>
</div>