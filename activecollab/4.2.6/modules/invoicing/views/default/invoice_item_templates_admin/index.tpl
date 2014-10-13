{title}Item Templates{/title}
{add_bread_crumb}View All{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

{if $request->get('flyout')}
<script type="text/javascript">
  App.widgets.FlyoutDialog.front().addButton('new_invoice_item', {$wireframe->actions->get('new_invoice_item')|json nofilter});
</script>
{/if}

<div id="invoice_items"></div>

<script type="text/javascript">
  var flyout_id = {$request->get('flyout')|json nofilter};

  $('#invoice_items').pagedObjectsList({
    'load_more_url' : '{assemble route=admin_invoicing_items}', 
    'items' : {$invoice_item_templates|json nofilter},
    'items_per_load' : {$items_per_page}, 
    'total_items' : {$total_items}, 
    'list_items_are' : 'tr', 
    'list_item_attributes' : { 'class' : 'item_templates' },
    'class' : 'admin_list',
    'columns' : {
      'description' : App.lang('Description'), 
      'first_tax_rate' : App.lang('First Tax Rate'),
      'second_tax_rate' : App.lang('Second Tax Rate'),
      'quantity' : App.lang('Quantity'),
      'unit_cost' : App.lang('Unit Cost'), 
      'options' : '' 
    }, 
    'sort_by' : 'description', 
    'empty_message' : App.lang('There are no invoice items defined'), 
    'listen' : 'invoice_item_template',
    'listen_scope' : flyout_id ? flyout_id : 'content',
    'on_add_item' : function(item) {
      var invoice_item_template = $(this);
      
      invoice_item_template.append(
        '<td class="description"></td>' +
       	'<td class="tax_rate first_tax_rate"></td>' +
       	'<td class="tax_rate second_tax_rate"></td>' +
       	'<td class="quantity"></td>' +
       	'<td class="unit_cost"></td>' + 
       	'<td class="options"></td>'
      );

      invoice_item_template.attr('id',item['id']);
      invoice_item_template.find('td.description').text(item['description']);

      if (item['first_tax_rate']) {
      	invoice_item_template.find('td.first_tax_rate').text(App.clean(item['first_tax_rate']['name']));
      } else {
        invoice_item_template.find('td.first_tax_rate').html('<i>' + App.lang('No Tax') + '</i>');
      } // if

      if (item['second_tax_rate']) {
        invoice_item_template.find('td.second_tax_rate').text(App.clean(item['second_tax_rate']['name']));
      } else {
        invoice_item_template.find('td.second_tax_rate').html('<i>' + App.lang('No Tax') + '</i>');
      } // if
      
      invoice_item_template.find('td.quantity').text(item['quantity']);
      invoice_item_template.find('td.unit_cost').text(item['unit_cost']);
      
      invoice_item_template.find('td.options')
        .append('<a href="' + item['urls']['edit'] + '" class="edit_note" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
        .append('<a href="' + item['urls']['delete'] + '" class="delete_note" title="' + App.lang('Remove Item') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
      ;

      invoice_item_template.find('td.options a.note_details').flyout();
      invoice_item_template.find('td.options a.edit_note').flyoutForm({
        'success_event' : 'invoice_item_template_updated',
        'width' : 480
      });
      invoice_item_template.find('td.options a.delete_note').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this item?'), 
        'success_event' : 'invoice_item_template_deleted', 
        'success_message' : App.lang('Item has been deleted successfully')
      });
    }
  });
</script>