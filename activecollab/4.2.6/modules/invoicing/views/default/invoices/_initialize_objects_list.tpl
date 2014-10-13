{use_widget name="objects_list" module="environment"}

<script type="text/javascript">
$('#new_invoice').flyoutForm({
  'success_event' : 'invoice_created',
  'title': App.lang('New Invoice')
});

$('#invoices').each(function() {
  var objects_list_wrapper = $(this);

  var items = {$invoices|json nofilter};
  var companies_map = {$companies_map|json nofilter};
  var states_map = {$invoice_states_map|json nofilter};
  var dates_map = {$invoice_dates_map|json nofilter};
  var mass_edit_url = '{assemble route=invoices}';
  var print_url = {$print_url|json nofilter};

  objects_list_wrapper.objectsList({
    'id' : 'invoices',
    'items' : items,
    'objects_type' : 'invoices',
    'required_fields' : ['id', 'long_name', 'name', 'client_id', 'client_name', 'status', 'permalink'],
    'requirements' : {
      'is_archived' : {if $in_archive}1{else}0{/if}
    },
    'print_url' : print_url,
    'events' : App.standardObjectsListEvents(),
    'multi_title' : App.lang(':num Invoices Selected'),
    'multi_url' : mass_edit_url,
    'prepare_item' : function (item) {
      // update issued on grouping map
      if (item['issued_on_month'] && item['issued_on_month_verbose']) {
        var issued_on_grouping = objects_list_wrapper.objectsList('grouping_map_get', 'issued_on_month');
        if (!issued_on_grouping.map[item['issued_on_month']]) {
          var issued_on_grouping_map = issued_on_grouping.map;
          var new_issued_on_grouping_map = new Object();

          var inserted = false;
          $.each(issued_on_grouping_map, function (index, value) {
            if ((item['issued_on_month'] > index) && !inserted) {
              new_issued_on_grouping_map[item['issued_on_month']] = item['issued_on_month_verbose'];
              inserted = true;
            } // if
            new_issued_on_grouping_map[index] = value;
          });

          if (!inserted) {
            new_issued_on_grouping_map[item['issued_on_month']] = item['issued_on_month_verbose'];
          } // if

          objects_list_wrapper.objectsList('grouping_map_replace', 'issued_on_month', new_issued_on_grouping_map);
        } // if
      } // if

      // update due on grouping map
      if (item['due_on_month'] && item['due_on_month_verbose']) {
        var due_on_grouping = objects_list_wrapper.objectsList('grouping_map_get', 'due_on_month');
        if (!due_on_grouping.map[item['due_on_month']]) {
          var due_on_grouping_map = due_on_grouping.map;
          var new_due_on_grouping_map = new Object();

          var inserted = false;
          $.each(due_on_grouping_map, function (index, value) {
            if ((item['due_on_month'] > index) && !inserted) {
              new_due_on_grouping_map[item['due_on_month']] = item['due_on_month_verbose'];
              inserted = true;
            } // if
            new_due_on_grouping_map[index] = value;
          });

          if (!inserted) {
            new_due_on_grouping_map[item['due_on_month']] = item['due_on_month_verbose'];
          } // if

          objects_list_wrapper.objectsList('grouping_map_replace', 'due_on_month', new_due_on_grouping_map);
        } // if
      } // if

      return {
        'id' : item['id'],
        'name' : item['short_name'],
        'long_name' : item['name'],
        'client_id' : item['client'] ? item['client']['id'] : null,
        'client_name' : item['client'] ? item['client']['name'] : item['client_name'],
        'currency' : item['currency'],
        'total' : item['total_before_rounding'],
        'issued_on_month' : item['issued_on_month'],
        'due_on_month' : item['due_on_month'],
        'status' : item['status'],
        'is_overdue' : item['status_conditions']['is_overdue'],
        'is_archived' : item['state'] == '2' ? 1 : 0,
        'permalink' : item['urls']['view']
      };
    },
    'render_item' : function (item) {
      var row = '';

      row += '<td class="invoice_name">' + App.clean(item['name']) + '</td>';
      row += '<td class="invoice_company_name ' + (item['is_overdue'] ? 'overdue' : '') + '">' + App.clean(item['client_name']) + (item['is_overdue'] ? '<img src="' + App.Wireframe.Utils.imageUrl('icons/12x12/warning.png', 'environment') + '" alt="overdue" class="status"/>' : '') + '</td>';
      row += '<td class="invoice_total">' + App.moneyFormat(item['total'], item['currency'], null, false, true) + '</td>';
      row += '<td class="invoice_currency_code">' + App.clean(item['currency']['code']) + '</td>';

      return row;
    },
    'search_index' : function (item) {
      return App.clean(item['name']) + ' ' + App.clean(item['client_name']);
    },
    'grouping' : [{
      'label' : App.lang("Don't group"),
      'property' : '',
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
    }, {
      'label' : App.lang('By Client'),
      'property' : 'client_id',
      'map' : companies_map,
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-client.png', 'system')
    }, {
      'label' : App.lang('By Status'),
      'property' : 'status',
      'map' : states_map ,
      'default' : true,
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-status.png', 'environment')
    }, {
      'label' : App.lang('Issued On'),
      'property' : 'issued_on_month',
      'map' : dates_map.issued_on,
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-status.png', 'environment'),
      'uncategorized_label' : App.lang('Not Issued')
    }, {
      'label' : App.lang('Payment Due On'),
      'property' : 'due_on_month',
      'map' : dates_map.due_on,
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-status.png', 'environment'),
      'uncategorized_label' : App.lang('No Due Date')
    }],

    'filtering' : [{
      'label' : App.lang('Status'),
      'property'  : 'status',
      'values'  : [{
        'label' : App.lang('All Invoices'),
        'value' : '',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/all-invoices.png', 'invoicing') ,
        'default' : true,
        'breadcrumbs' : App.lang('All Invoices')
      }, {
        'label' : App.lang('Drafts'),
        'value' : '0',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/draft-invoices.png', 'invoicing'),
        'breadcrumbs' : App.lang('Drafts')
      }, {
        'label' : App.lang('Issued'),
        'value' : '1',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/issued-invoices.png', 'invoicing'),
        'breadcrumbs' : App.lang('Issued')
      }, {
        'label' : App.lang('Paid'),
        'value' : '2',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/paid-invoices.png', 'invoicing'),
        'breadcrumbs' : App.lang('Paid')
      }, {
        'label' : App.lang('Canceled'),
        'value' : '3',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/canceled-invoices.png', 'invoicing'),
        'breadcrumbs' : App.lang('Canceled')
      }]
    }]
  });

  // invoice added
  App.Wireframe.Events.bind('invoice_created.content', function (event, invoice) {
    objects_list_wrapper.objectsList('add_item', invoice);
  });

  // invoice updated
  App.Wireframe.Events.bind('invoice_updated.content invoice_issued.content invoice_canceled.content', function (event, invoice) {
    objects_list_wrapper.objectsList('update_item', invoice);
  });

  // invoice deleted
  App.Wireframe.Events.bind('invoice_deleted.content', function (event, invoice_id) {
    objects_list_wrapper.objectsList('delete_item', invoice_id);
    objects_list_wrapper.objectsList('load_empty');
  });

  App.Wireframe.Events.bind('payment_updated.content payment_deleted.content', function (event, payment) {
    objects_list_wrapper.objectsList('update_item', payment['invoice']);
    $('#render_object_payments').paymentContainer('update_payment',payment);
    App.Wireframe.Events.trigger('invoice_updated', payment['invoice']);
  });

  App.Wireframe.Events.bind('payment_created.content', function (event, payment) {
    if(typeof(payment) == 'object') {
      objects_list_wrapper.objectsList('update_item', payment['invoice']);
      $('#render_object_payments').paymentContainer('add_payment',payment);
      App.Wireframe.Events.trigger('invoice_updated', payment['invoice']);
    } else {
      App.Wireframe.Flash.error(payment);
    }
  });


  // keep client_id map up to date
  App.objects_list_keep_companies_map_up_to_date(objects_list_wrapper, 'client_id', 'content');

  {if $active_invoice->isLoaded()}
  objects_list_wrapper.objectsList('load_item', {$active_invoice->getId()}, {$active_invoice->getViewUrl()|json nofilter}); // Pre select item if this is permalink
  {/if}
});
</script>