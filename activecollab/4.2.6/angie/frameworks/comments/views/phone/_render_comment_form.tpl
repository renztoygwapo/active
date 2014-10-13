{if $post_comment_url}
  <div id="post_comment">
	  {form action=$post_comment_url}
	    {wrap_editor field=body}
			  {editor_field name='comment[body]' label='Leave a Comment' id=comment_body}{$comment_data.body nofilter}{/editor_field}
			{/wrap_editor}
	    
	    {wrap_buttons}
	      {submit}Comment{/submit}
	    {/wrap_buttons}
	  {/form}
	</div>
	
	<script type="text/javascript">
		$(document).ready(function() {
			var comment_block = $('#post_comment');
	    
	    comment_block.find('textarea').each(function() {
	      var comment = $(this).val();
	      
	      comment_block.find('form').submit(function() {
				  var comment_value = jQuery.trim(comment.val());
		      if(comment_value) {
		        $('#{$id}').listview('refresh');
		        return true;
		      } // if
		      
				  return false;
				});
	    });
	  });
	</script>
{else}
  <div class="object_comments_locked">
    <img src="{image_url name='icons/32x32/comments-locked.png' module=$smarty.const.COMMENTS_FRAMEWORK interface=AngieApplication::INTERFACE_PHONE}" alt=""/>
    {lang object_type=$object->getVerboseType()|lower}Comments for this :object_type are locked{/lang}
  </div>
{/if}