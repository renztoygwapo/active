{title}Conflicts{/title}
{add_bread_crumb}Conflicts{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="incoming_conflicts_wrapper">
	<div id="incoming_conflicts"></div>
	
	<div id="incoming_conflict_buttons_wrapper">
		{button success_event='incoming_mail_mass_delete_all' class="link_button_alternative" confirm='Are you sure that you want to remove all conflicts from your system? These conflicts will be permanently removed.' href=Router::assemble('incoming_mail_remove_all_conflicts')}Remove All{/button}
    {button id="remove_selected" class="link_button_alternative"}Remove Selected{/button}
  </div>
</div>

<script type="text/javascript">

	
  $('#incoming_conflicts').pagedObjectsList({
    'load_more_url' : '{assemble route=incoming_mail}', 
    'items' : {$conflicts|json nofilter},
    'items_per_load' : {$conflicts_per_page}, 
    'total_items' : {$total_conflicts}, 
    'list_items_are' : 'tr', 
    'list_item_attributes' : { 'class' : 'conflicts' }, 
    'columns' : {
      'subject' : App.lang('Subject'), 
      'from' : App.lang('From'), 
      'mailbox' : App.lang('Mailbox'),
      'status' : App.lang('Status'),  
      'options' : '',
      'mass_select' : "<input type='checkbox'/>"  
    }, 
    'clean_columns' : false,
    'empty_message' : App.lang('There are no incoming mail conflicts'), 
    'listen' : {
      'delete' : 'incoming_mail_conflict_deleted incoming_mail_conflict_resolved'
    }, 
    'listen_scope' : 'inline_tab',
    'on_add_item' : function(item) {
      var conflict = $(this);
      
      conflict.append(
    		'<td class="subject"></td>' + 
        '<td class="from"></td>' + 
        '<td class="mailbox"></td>' + 
        '<td class="status"></td>' + 
        '<td class="options"></td>' + 
        '<td class="select_item"><input type="checkbox" name="resolve_conflicts[]" value="' + item['id'] + '" /></td>'
      );
      
      conflict.find('td.subject').text(App.clean(item['subject']));
      conflict.find('td.from').text(App.clean(item['from']));
      conflict.find('td.mailbox').text(App.clean(item['mailbox']));
      conflict.find('td.status').text(App.clean(item['status']));
      
      conflict.find('td.options')
        .append('<a href="' + item['urls']['import_url'] + '" class="conflict_resolve" title="' + App.lang('Resolve conflict') + '"><img src="{image_url name="icons/12x12/preview.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
        .append('<a href="' + item['urls']['delete'] + '" class="delete_conflict" title="' + App.lang('Remove Conflict') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
      ;

      conflict.find('td.options a.conflict_resolve').flyoutForm({
        'success_event' : 'incoming_mail_conflict_resolved',
        'success_message' : App.lang('Incoming mail conflict resolved')
      });

      conflict.find('td.options a.delete_conflict').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this conflicting message?'), 
        'success_event' : 'incoming_mail_conflict_deleted', 
        'success_message' : App.lang('Conflicting message has been deleted successfully')
      });
    }
  });

  var wrapper = $('div#incoming_conflicts_wrapper');
	var incoming_buttons_container = wrapper.find('div#incoming_conflict_buttons_wrapper');
	var remove_selected_btn = incoming_buttons_container.find('button#remove_selected');
	var total_conflicts = '{$total_conflicts}';

	
	remove_selected_btn.click(function(){
		if(!confirm('Are you sure that you want to remove selected conflicts from your system? These conflicts will be permanently removed.')) {
      return false;
    } // if
	  var href = '{Router::assemble('incoming_mail_remove_selected_conflicts')}'; 
	  var data = new Array();
		wrapper.find('tr.list_item td.select_item input[type=checkbox]:checked').each(function(index, object) {
			data.push($(object).val());
		});

		var options = {
      'url' : href,
      'type' : 'post',
      'data' : {
        'incoming_mail_conflict_ids' : data,
    		'submitted' : 'submitted'
    	},
      'dataType' : 'json',
      'success' : function(response) {
    	  App.Wireframe.Events.trigger('incoming_mail_mass_delete', [response]);
      }
    };
		$.ajax(options);
	});

//update incoming mail conflict number and list on delete selected
	App.Wireframe.Events.bind('incoming_mail_mass_delete.inline_tab', function(e, response) {
		if(response && response.ids != null) {
			
			$(response.ids).each(function(index, conflict_id){
				$('#incoming_conflicts').pagedObjectsList('delete_item_by_id', [conflict_id]);
			}); 
			App.Wireframe.Events.trigger('refresh_conflict_number', [response.conflicts]);
			refresh_conflict_buttons(response.conflicts); 
			App.Wireframe.Flash.success(App.lang(':num conflict(s) deleted', {
				'num' : response.ids.length
				}
			)); 
		} else {
			App.Wireframe.Flash.error(App.lang('No conflict selected')); 
		}//if
		
	});

	//update incoming mail conflict number and list on delete all
	App.Wireframe.Events.bind('incoming_mail_mass_delete_all.inline_tab', function(e, response) {
		if(response) {
			if(response.conflicts == 0) {
				$('#incoming_conflicts').pagedObjectsList('delete_all_items');
			}//if
			App.Wireframe.Events.trigger('refresh_conflict_number', [response.conflicts]);
			refresh_conflict_buttons(response.conflicts); 
			App.Wireframe.Flash.success(App.lang('All conflicts deleted')); 
		} else {
			App.Wireframe.Flash.error(App.lang('No conflict selected')); 
		}//if
		
	});

  //update incoming mail conflict number and list on resolve
  App.Wireframe.Events.bind('incoming_mail_conflict_resolved.inline_tab', function(e, response) {
    if(response) {
      refresh_conflict_buttons(response.conflicts);
    } //if
  });

	//select in th
	var select_all_checkbox = wrapper.find('div#incoming_conflicts tr th.mass_select input');
	var items_checkboxes = wrapper.find('div#incoming_conflicts tr td.select_item input');

	//disable/enable remove selected btn
	var refresh_remove_selected_btn = function() {
		var items_checkboxes_selected = wrapper.find('div#incoming_conflicts tr td.select_item input:checked');
		if(items_checkboxes_selected.length > 0) {
			remove_selected_btn.removeAttr('disabled');
		} else {
			remove_selected_btn.attr('disabled','disabled');
		}//if
	};//refresh_remove_selected_btn
	
	
	/*
	*	Refresh showing of buttons
	*
	*/
	var refresh_conflict_buttons = function(total_conflicts) {
		if(total_conflicts == 0) {
			select_all_checkbox.hide();
			incoming_buttons_container.hide();
		} else {
			select_all_checkbox.show();
			incoming_buttons_container.show();
			refresh_remove_selected_btn();
		}//if
	};//refresh_conflict_buttons
	
	refresh_conflict_buttons(total_conflicts);

	
	//check/uncheck all
	select_all_checkbox.change(function(){
		if($(this).is(':checked')) {
			items_checkboxes.attr('checked','checked');
		} else {
			items_checkboxes.removeAttr('checked');
		}//if
		refresh_remove_selected_btn();
	});

	
	items_checkboxes.change(function(){
		refresh_remove_selected_btn();
		if(!$(this).is(':checked')) {
			select_all_checkbox.removeAttr('checked'); 
		}//if
	});
	
	

</script>