{title}Reminders{/title}
{add_bread_crumb}Reminders{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="object_reminders"></div>

<script type="text/javascript">
  $('#object_reminders').pagedObjectsList({
    'load_more_url' : '{$active_object->reminders()->getUrl()}', 
	  'items' : {$reminders|json nofilter},
	  'items_per_load' : {$reminders_per_load}, 
	  'total_items' : {$total_reminders},
	  'list_items_are' : 'tr',
	  'list_item_attributes' : {
		  'class' : 'reminder'
		},
		'columns' : {
			'info' : App.lang('Reminder'),
      'comment' : App.lang('Comment'),
			'options' : App.lang('Options')
		}, 
		'show_columns' : true,
		'listen' : 'reminder', 
		'listen_constraint' : function(event, item) {
			return typeof(item) == 'object' && item && item['parent_class'] == {$active_object|get_class|json nofilter} && item['parent_id'] == {$active_object->getId()|json nofilter};
		},
		'listen_scope' : 'flyout', 
	  'on_add_item' : function(item) {
		  var reminder = $(this);
	    
	    reminder.append('<td class="info"></td>' +
        '<td class="comment"></td>' +
		    '<td class="options"></td>');

	    if(item['sent_on'] && typeof(item['sent_on']) == 'object' && item['sent_on']['timestamp']) {
	      reminder.addClass('sent');
	    } else if(item['dismissed_on'] && typeof(item['dismissed_on']) == 'object' && item['dismissed_on']['timestamp']) {
	      reminder.addClass('dismissed');
	    } // if

	    switch(item['send_to']) {

	      // Self reminder
	      case 'self':
	        if(item['sent_on']) {
			      reminder.find('td.info').html(App.lang('<a href=":user_link">:user_name</a> sent a reminder to himself', {
				      'user_link' : item['created_by']['urls']['view'], 
				      'user_name' : item['created_by']['short_display_name'] 
				    }));
			    } else {
			      reminder.find('td.info').html(App.lang('<a href=":user_link">:user_name</a> set a reminder for himself', {
				      'user_link' : item['created_by']['urls']['view'], 
				      'user_name' : item['created_by']['short_display_name'] 
				    }));
				  } // if
				  
		      break;

		    // Remind assignees
	      case 'assignees':
	        if(item['sent_on']) {
			      reminder.find('td.info').html(App.lang('<a href=":user_link">:user_name</a> sent a reminder to assignees', {
				      'user_link' : item['created_by']['urls']['view'], 
				      'user_name' : item['created_by']['short_display_name'] 
				    }));
			    } else {
			      reminder.find('td.info').html(App.lang('<a href=":user_link">:user_name</a> set a reminder for assignees', {
				      'user_link' : item['created_by']['urls']['view'], 
				      'user_name' : item['created_by']['short_display_name'] 
				    }));
				  } // if
		      
	        break;

	      // Remind subscribers
	      case 'subscribers':
	        if(item['sent_on']) {
			      reminder.find('td.info').html(App.lang('<a href=":user_link">:user_name</a> sent a reminder to subscribers', {
				      'user_link' : item['created_by']['urls']['view'], 
				      'user_name' : item['created_by']['short_display_name'] 
				    }));
			    } else {
			      reminder.find('td.info').html(App.lang('<a href=":user_link">:user_name</a> set a reminder for subscribers', {
				      'user_link' : item['created_by']['urls']['view'], 
				      'user_name' : item['created_by']['short_display_name'] 
				    }));
				  } // if
		      
	        break;

	      // Remind commenters
	      case 'commenters':
	        if(item['sent_on']) {
			      reminder.find('td.info').html(App.lang('<a href=":user_link">:user_name</a> sent a reminder to people involved in a discussion', {
				      'user_link' : item['created_by']['urls']['view'], 
				      'user_name' : item['created_by']['short_display_name'] 
				    }));
			    } else {
			      reminder.find('td.info').html(App.lang('<a href=":user_link">:user_name</a> set a reminder for people involved in a discussion', {
				      'user_link' : item['created_by']['urls']['view'], 
				      'user_name' : item['created_by']['short_display_name'] 
				    }));
				  } // if
		      
	        break;

	      // Remind selected users
	      default:
		      if(item['sent_on']) {
			      reminder.find('td.info').html(App.lang('<a href=":user_link">:user_name</a> sent a reminder to selected users', {
				      'user_link' : item['created_by']['urls']['view'], 
				      'user_name' : item['created_by']['short_display_name'] 
				    }));
			    } else {
			      reminder.find('td.info').html(App.lang('<a href=":user_link">:user_name</a> set a reminder for selected users', {
				      'user_link' : item['created_by']['urls']['view'], 
				      'user_name' : item['created_by']['short_display_name'] 
				    }));
				  } // if
	    } // switch

      if(typeof(item['comment']) == 'string' && item['comment']) {
        if(item['comment'].length > 50) {
          reminder.find('td.comment').append(App.clean(item['comment'].substr(0, 50)) + '...');
        } else {
          reminder.find('td.comment').append(App.clean(item['comment']));
        } // if
      } // if

	    var options_cell = reminder.find('td.options'); 

	    if(item['permissions']['can_send']) {
		    $('<a href="' + item['urls']['send'] + '" class="send_reminder" title="' + App.lang('Send Now') + '"><img src="{image_url name="icons/12x12/email.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>').asyncLink({
			    'confirmation' : App.lang('Are you sure that you want to send this reminder now?'), 
			    'success_event' : 'reminder_updated', 
			    'success' : function(response) {
				     reminder.addClass('sent');

				     options_cell.find('a.send_reminder').remove();
				     
			       App.Wireframe.Flash.success('Selected reminder has been sent');
				   }, 
				   'error' : function() {
				     App.Wireframe.Flash.error('Failed to send selected reminder');
					 }
		    }).appendTo(options_cell);
	    } // if

	    if(item['permissions']['can_dismiss']) {
		    $('<a href="' + item['urls']['dismiss'] + '" class="dismiss_reminder" title="' + App.lang('Dismiss') + '"><img src="{image_url name="icons/12x12/complete.png" module=$smarty.const.REMINDERS_FRAMEWORK}" /></a>').asyncLink({
		      'confirmation' : App.lang('Are you sure that you want to dismiss this reminder?'), 
		      'success' : function(response) {
			      reminder.addClass('dismissed');
			    }
			  }).appendTo(options_cell);
	    } // if

	    if(item['permissions']['can_delete']) {
		    $('<a href="' + item['urls']['delete'] + '" class="delete_reminder" title="' + App.lang('Delete') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>').asyncLink({
		      'confirmation' : App.lang('Are you sure that you want to delete this reminder?'), 
		      'success_event' : 'reminder_deleted', 
		      'success_message' : App.lang('Selected reminder has been deleted')
			  }).appendTo(options_cell);
	    } // if
	  }
  });
</script>