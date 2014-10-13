{use_widget name="objects_list" module="environment"}

<script type="text/javascript">
  $('#new_quote').flyoutForm({
    'success_event' : 'quote_created',
    'title' : App.lang('New Quote')
  });

  $('#quotes').each(function() {
    var objects_list_wrapper = $(this);

    var items = {$quotes|json nofilter};
    var mass_edit_url = '{assemble route=recurring_profiles_mass_edit}';
    var companies_map = {$companies_map|json nofilter};
    var state_map = {$state_map|json nofilter};
    var print_url = {$print_url|json nofilter};


    objects_list_wrapper.objectsList({
      'id'                : 'quotes',
      'items'             : items,
      'objects_type'      : 'quotes',
      'required_fields'   : ['id', 'name', 'status', 'company_id', 'permalink'],
      'requirements' : {
        'is_archived' : {if $in_archive}1{else}0{/if}
      },
      'events'            : App.standardObjectsListEvents(),
      'multi_title'       : App.lang(':num Quotes Selected'),
      'multi_url'         : mass_edit_url,
      'print_url'         : print_url,
      'prepare_item'      : function (item) {
        return {
          'id' : item['id'],
          'name' : item['name'],
          'status' : item['status'],
          'company_id' : item['client'] && typeof(item['client']) == 'object' ? item['client']['id'] : item['client_id'],
          'permalink' : item['permalink'],
          'is_archived' : item['state'] == '2' ? 1 : 0
        };
      },

      'grouping'          : [{
        'label' : App.lang("Don't group"),
        'property' : '',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
      }, {
        'label' : App.lang('By State'),
        'property' : 'status',
        'map' : state_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-status.png', 'environment'),
        'default' : true
      }, {
        'label' : App.lang('By Client'),
        'property' : 'company_id',
        'map' : companies_map ,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-client.png', 'system'),
        'uncategorized_label' : App.lang('Unknown Client')
      }],

      'filtering' : [{
        'label' : App.lang('Status'),
        'property'  : 'status', 'values'  : [{
          'label' : App.lang('All Quotes'),
          'value' : '',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/all-invoices.png', 'invoicing'),
          'default' : true,
          'breadcrumbs' : App.lang('All Quotes')
        }, {
          'label' : App.lang('Drafts'),
          'value' : '0',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/draft-invoices.png', 'invoicing'),
          'breadcrumbs' : App.lang('Drafts')
        }, {
          'label' : App.lang('Sent'),
          'value' : '1',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/issued-invoices.png', 'invoicing'),
          'breadcrumbs' : App.lang('Sent')
        }, {
          'label' : App.lang('Won'),
          'value' : '2',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/won-invoices.png', 'invoicing'),
          'breadcrumbs' : App.lang('Won')
        }, {
          'label' : App.lang('Lost'),
          'value' : '3',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/lost-invoices.png', 'invoicing'),
          'breadcrumbs' : App.lang('Lost')
        }]
      }]
    });

    // quote added
    App.Wireframe.Events.bind('quote_created.content', function (event, quote) {
      objects_list_wrapper.objectsList('add_item', quote);
    });

    // quote updated
    App.Wireframe.Events.bind('quote_updated.content quote_sent.content', function (event, quote) {
      objects_list_wrapper.objectsList('update_item', quote);
    });

    // keep company_id map up to date
    App.objects_list_keep_companies_map_up_to_date(objects_list_wrapper, 'company_id', 'content');

    // redirect to project after it's created from quote
    App.Wireframe.Events.bind('project_created.content', function (event, project) {
      App.Wireframe.Content.setFromUrl(project.permalink);
    });

    // quote deleted
    App.Wireframe.Events.bind('quote_deleted.content', function (event, quote) {
      objects_list_wrapper.objectsList('delete_item', quote.id);
      objects_list_wrapper.objectsList('load_empty');
      return true;
    });

    {if $active_quote->isLoaded()}
    objects_list_wrapper.objectsList('load_item', {$active_quote->getId()}, '{$active_quote->getViewUrl()}');
    {/if}
  });
</script>