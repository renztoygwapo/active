{title}Add Status Message{/title}
{add_bread_crumb}Add{/add_bread_crumb}

<div id="add_status_message">
  {form action=Router::assemble('status_updates_add')}
    {wrap_editor field=message}
		  {editor_field name='status_update[message]' label='Status Message'}{$status_data.message nofilter}{/editor_field}
		{/wrap_editor}
    
    {wrap_buttons}
      {submit}Add Message{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $(document).ready(function() {
		var comment_block = $('#add_status_message');
    
    comment_block.find('textarea').each(function() {
      var comment = $(this).val();
      
      comment_block.find('form').submit(function() {
			  var comment_value = jQuery.trim(comment.val());
			  return this.comment_value;
			});
    });
  });
</script>