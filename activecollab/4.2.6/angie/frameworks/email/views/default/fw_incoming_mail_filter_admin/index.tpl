{title}Incoming Mail Filters{/title}
{add_bread_crumb}Filters{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}
{use_widget name="ui_sortable" module="environment"}

<div id="incoming_filters"></div>

<script type="text/javascript">
  $('#incoming_filters').pagedObjectsList({
    'load_more_url' : '{assemble route=incoming_email_admin_filters}', 
    'items' : {$filters|json nofilter},
    'items_per_load' : {$filters_per_page}, 
    'total_items' : {$total_filters}, 
    'list_items_are' : 'tr', 
    'list_item_attributes' : { 'class' : 'filter' }, 
    'columns' : {
      'drag' : '',
      'is_enabled' : '', 
      'name' : App.lang('Name'), 
      'description' : App.lang('Description'), 
      'options' : '' 
    }, 
    'empty_message' : App.lang('There are no incoming filters defined'), 
    'listen' : 'incoming_filter', 
    'on_add_item' : function(item) {
      var filter = $(this);
      
      filter.append(
    		'<td class="drag_icon"></td>' +
       	'<td class="is_enabled"></td>' + 
        '<td class="name"></td>' + 
        '<td class="options"></td>'
      );

	  	filter.attr('id',item['id']);
	  	
      var checkbox = $('<input type="checkbox" />').attr({ 
        'on_url' : item['urls']['enable'], 
        'off_url' : item['urls']['disable']
      }).asyncCheckbox({
        'success_event' : 'incoming_filter_updated', 
        'success_message' : [ App.lang('Filter has been disabled'), App.lang('Filter has been enabled') ]
      }).appendTo(filter.find('td.is_enabled'));

      if(item['is_enabled']) {
        checkbox[0].checked = true;
      } // if
      
      filter.find(".drag_icon").append('<img src="{image_url name="layout/bits/handle-drag.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" style="cursor: move;">');
	  
      $('<a></a>').attr('href', item['urls']['view']).text(item['name']).appendTo(filter.find('td.name'));
      filter.find('td.name').text(App.clean(item['name']));
      
      filter.find('td.options')
        .append('<a href="' + item['urls']['view'] + '" class="filter_details" title="' + App.lang('View Details') + '"><img src="{image_url name="icons/12x12/preview.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
        .append('<a href="' + item['urls']['edit'] + '" class="edit_filter" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
        .append('<a href="' + item['urls']['delete'] + '" class="delete_filter" title="' + App.lang('Remove Filter') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
      ;

      filter.find('td.options a.filter_details').flyout();
      filter.find('td.options a.edit_filter').flyoutForm({
        'success_event' : 'incoming_filter_updated'
      });
      
      filter.find('td.options a.delete_filter').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this filter?'), 
        'success_event' : 'incoming_filter_deleted', 
        'success_message' : App.lang('Filter has been deleted successfully')
      });
    }
  }).sortable({ 
  	'items' : "tr.filter",
  	'handle' : 'td.drag_icon',
  	'cursor' : 'pointer',
  	'update' : function(event, ui) {
	  	var new_order = $(this).sortable('toArray').toString();
	  	var reorder_filter_url = '{assemble route=incoming_email_filter_reorder}';
  		
  		$.ajax({
        'url' : reorder_filter_url,
        'type' : 'post',
        'data' : { 
    			'submitted' : 'submitted',
    			'new_order' : new_order
        }, 
        'success' : function(response) {
        	App.Wireframe.Flash.success(App.lang('Filters have been reordered.'));
        },
        'error' : function(error) {
          if(error) {
        	  App.Wireframe.Flash.error(error.responseText);
	    		} else {  
        		App.Wireframe.Flash.error('Failed to reorder filter position. Please try again later');
        	} // if
        }
      });
  	}
  });
</script>