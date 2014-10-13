{title}API Subscriptions{/title}
{add_bread_crumb}List{/add_bread_crumb}

{use_widget name="paged_objects_list" module="environment"}

{if $active_object->isApiUser()}
  <div id="api_subscriptions"></div>
  
  <script type="text/javascript">
    $('#api_subscriptions').pagedObjectsList({
      'load_more_url' : {$active_object->getApiSubscriptionsUrl()|json nofilter},
      'items' : {$subscriptions|json nofilter},
      'items_per_load' : {$subscriptions_per_page}, 
      'total_items' : {$total_subscriptions}, 
      'list_items_are' : 'tr',
      'list_item_attributes' : {
  		  'class' : 'api_client_subscription'
  		},
  		'columns' : {
    		'is_enabled' : '', 
  			'client' : App.lang('Client'), 
  			'access' : App.lang('Access Level'), 
  			'last_used_on' : App.lang('Last Used'), 
  			'options' : ''
  		},
      'empty_message' : App.lang('This user does not have any API subscriptions'), 
      'listen' : 'api_client_subscription', 
      'listen_constraint' : function(event, item) {
        return typeof(item) == 'object' && item && item['user_id'] == {$active_object->getId()};
      },
      'on_add_item' : function(item) {
        var subscription = $(this);

        subscription.append(
          '<td class="is_enabled"></td>' +  
          '<td class="client"></td>' +  
          '<td class="access"></td>' +  
          '<td class="last_used_on"></td>' +  
		    	'<td class="options"></td>'
		   	);

        var checkbox = $('<input type="checkbox">').attr({ 
          'on_url' : item['urls']['enable'], 
          'off_url' : item['urls']['disable']
        }).asyncCheckbox({
          'success_event' : 'api_client_subscription_updated', 
          'success_message' : [ App.lang('Selected API subscription has been disabled'), App.lang('Selected API subscription has been enabled') ]
        }).appendTo(subscription.find('td.is_enabled'));

        checkbox[0].checked = item['is_enabled'];

        if(item['is_enabled']) {
          if(item['is_read_only']) {
  			    subscription.addClass('is_read_only');
  			    subscription.find('td.access').text(App.lang('Read Only'));
  			  } else {
  			    subscription.find('td.access').text(App.lang('Read and Write'));
  			  } // if
        } else {
          subscription.addClass('is_disabled');
          subscription.find('td.access').text(App.lang('Disabled'));
        } // if
        
        if(typeof(item['client_vendor']) == 'string' && item['client_vendor']) {
          subscription.find('td.client').html('<span class="api_client_name">' + App.clean(item['client_name']) + '</span> by <span class="api_client_vendor">' + App.clean(item['client_vendor']) + '</span>');
			  } else {
			    subscription.find('td.client').html('<span class="api_client_name">' + App.clean(item['client_name']) + '</span>');
			  } // if
			  
		   	if(typeof(item['last_used_on']) == 'object' && item['last_used_on']) {
			   	 subscription.find('td.last_used_on').text(item['last_used_on']['formatted']);
		   	} else {
			   	 subscription.find('td.last_used_on').html('<span class="details">' + App.lang('Never Used') + '</span>');
		   	} // if

        var options_cell = subscription.find('td.options');

        $('<a href="' + item['urls']['view'] + '" class="preview_subscription" title="' + App.lang('API Subscription Details') + '"><img src="{image_url name="icons/12x12/preview.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}"></a>').appendTo(options_cell).flyout({
          'width' : 700
        });

        if(item['permissions']['can_edit']) {
          $('<a href="' + item['urls']['edit'] + '" class="edit_api_client_subscription" title="' + App.lang('Update API Subscription') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}"></a>').flyoutForm({
            'success_event' : 'api_client_subscription_updated'
  			  }).appendTo(options_cell);
        } // if

  	    if(item['permissions']['can_delete']) {
  		    $('<a href="' + item['urls']['delete'] + '" class="delete_api_client_subscription" title="' + App.lang('Delete') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}"></a>').asyncLink({
  		      'confirmation' : App.lang('Are you sure that you want to delete this subscription?'), 
  		      'success_event' : 'api_client_subscription_deleted', 
  		      'success_message' : App.lang('Selected API client subscription has been deleted')
  			  }).appendTo(options_cell);
  	    } // if
      }
    });
  </script>
{else}
  <p class="empty_page">{lang}API access is not enabled for this user account{/lang}</p>
{/if}