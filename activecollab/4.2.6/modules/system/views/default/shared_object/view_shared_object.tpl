{title lang=false plain=""}{$active_shared_object->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

{if ($show_instructions)}
	<div class="shared_object_instructions">
	  <img src="{image_url name='icons/12x12/dismiss-public-message.png' module='environment'}" alt="" class="dismiss"/>
	  <p>{lang}Thank you for getting in touch.{/lang}</p>
	  <p>{lang}One of our staff members will answer your inquiry soon.{/lang}</p>
    <p>{lang}You will also receive a confirmation email with details of how to track the progress of your enquiry. If you do not receive this email please check your junk mail folder.{/lang}</p>
	</div>
	
	<script type="text/javascript">
	  var instructions = $('.shared_object_instructions');
	  var dismiss = instructions.find('.dismiss');
	
	  dismiss.click(function () {
	    instructions.remove();
	    $.cookie('{$cookie_name}', null);
	    return false;    
	  });
	</script>
{/if}

<div class="shared_object_details">
  <div class="icon discussion"></div>
  
  <div class="name_and_author">
    <h1>{$active_shared_object->getName()}</h1>
    <span class="action_on_by">{action_on_by format="date" user=$active_shared_object->getCreatedBy() datetime=$active_shared_object->getCreatedOn()}</span>
  </div>
  
  <div class="additional"></div>
</div>

<script type="text/javascript">
  $('.shared_object_details').detach().appendTo($('#public_page_title .public_wrapper').empty());
</script>

{if $active_shared_object->sharing()->displayAsDiscussion() && $active_shared_object->sharing()->supportsComments() && $active_shared_object instanceof IComments}
  {shared_object_comments object=$active_shared_object user=$logged_user errors=$errors comment_data=$comment_data object_as_first_comment=true}
{else}
  {shared_object object=$active_shared_object}
    {if $active_shared_object->sharing()->supportsComments() && $active_shared_object instanceof IComments}
      {shared_object_comments object=$active_shared_object user=$logged_user errors=$errors comment_data=$comment_data}
    {/if}
  {/shared_object}
{/if}