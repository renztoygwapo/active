{add_bread_crumb}Details{/add_bread_crumb}

{object object=$active_task user=$logged_user}
  <div class="wireframe_content_wrapper">
    <div class="object_body with_shadow">
      <div class="object_content_wrapper"><div class="object_body_content formatted_content"></div>
        {object_attachments object=$active_task user=$logged_user}
      </div>
      {object_subtasks object=$active_task user=$logged_user}
    </div>
  </div>
  
  <div class="wireframe_content_wrapper">{object_comments object=$active_task user=$logged_user show_first=yes}</div>
  <div class="wireframe_content_wrapper">{object_history object=$active_task user=$logged_user}</div>
{/object}

<script type="text/javascript">
  App.Wireframe.Events.bind('create_invoice_from_task.{$request->getEventScope()}', function (event, invoice) {
   	if (invoice['class'] == 'Invoice') {
   		App.Wireframe.Flash.success(App.lang('New invoice created'));
   		App.Wireframe.Content.setFromUrl(invoice['urls']['view']);
	  } // if
	});
</script>	