{title}Invoice Note Templates{/title}
{add_bread_crumb}List{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

{if $request->get('flyout')}
<script type="text/javascript">
  App.widgets.FlyoutDialog.front().addButton('new_invoice_note_template', {$wireframe->actions->get('new_invoice_note_template')|json nofilter});
</script>
{/if}

<div id="invoice_notes"></div>

<script type="text/javascript">
  var notes_widget = $('#invoice_notes');
  var flyout_id = {$request->get('flyout')|json nofilter};

  notes_widget.pagedObjectsList({
    'load_more_url' : '{assemble route=admin_invoicing_notes}', 
    'items' : {$invoice_note_templates|json nofilter},
    'items_per_load' : {$items_per_page}, 
    'total_items' : {$total_items}, 
    'list_items_are' : 'tr', 
    'list_item_attributes' : { 'class' : 'note' },
    'class' : 'admin_list',
    'columns' : {
      'default'     : '',
      'name' : App.lang('Name'), 
      'options' : '' 
    }, 
    'sort_by' : 'name', 
    'empty_message' : App.lang('There are no invoice notes defined'), 
    'listen' : 'invoice_note_template',
    'listen_scope' : flyout_id ? flyout_id : 'content',
    'on_add_item' : function(item) {
      var note = $(this);
      
      note.append(
        '<td class="default_toggler"></td>',
       	'<td class="name"></td>' +  
        '<td class="options"></td>'
      );

      var toggler_cell = note.find('td.default_toggler');
      var checkbox_wrapper = $('<span class="checkbox_wrapper"></span>').appendTo(toggler_cell);
      var checkbox_label_set = $('<span class="checkbox_label checkbox_label_set">' + App.lang('Set as Default') + '</span>').appendTo(checkbox_wrapper);
      var checkbox_label_remove = $('<span class="checkbox_label checkbox_label_remove">' + App.lang('Remove Default') + '</span>').appendTo(checkbox_wrapper);
      var checkbox = $('<input type="checkbox" set_as_default_url="' + item['urls']['set_as_default'] + '" remove_default_url="' + item['urls']['remove_default'] + '" />').appendTo(checkbox_wrapper);

      if (item['is_default']) {
        note.addClass('default');
        checkbox.attr('checked', true);
      } // if


      note.attr('id',item['id']);
	  
      $('<a></a>').attr('href', item['urls']['view']).html(item['name']).appendTo(note.find('td.name'));
      note.find('td.name').html(App.clean(item['name']));
      
      note.find('td.options')
        .append('<a href="' + item['urls']['edit'] + '" class="edit_note" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
        .append('<a href="' + item['urls']['delete'] + '" class="delete_note" title="' + App.lang('Remove Note') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
      ;

      note.find('td.options a.note_details').flyout();
      note.find('td.options a.edit_note').flyoutForm({
        'success_event' : 'invoice_note_template_updated',
        'width' : 580
      });
      
      note.find('td.options a.delete_note').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this note?'), 
        'success_event' : 'invoice_note_template_deleted', 
        'success_message' : App.lang('Note has been deleted successfully')
      });
    }
  }).on('click', 'input[type="checkbox"]', function (event) {
      var checkbox = $(this).hide();
      var indicator = $('<img src="' + App.Wireframe.Utils.indicatorUrl('small') + '" />').insertAfter(checkbox);

      var ajax_url = checkbox.attr('set_as_default_url');
      if (!checkbox.is(':checked')) {
        ajax_url = checkbox.attr('remove_default_url');
      } // if

      $.ajax({
        'url' : ajax_url,
        'success' : function (response) {
          if (checkbox.is(':checked')) {
            notes_widget.find('tr.default').each(function () {
              $(this).removeClass('default').find('input[type="checkbox"]').removeAttr('checked');
            });
          } // if

          App.Wireframe.Events.trigger('invoice_note_template_updated', [response]);
          indicator.remove();
          checkbox.show();
        },
        'error' : function (response) {
          indicator.remove();
          checkbox.show();
          if (checkbox.is(':checked')) {
            checkbox.removeAttr('checked');
          } else {
            checkbox.attr('checked', true);
          } // if
        }
      })
    });;
</script>