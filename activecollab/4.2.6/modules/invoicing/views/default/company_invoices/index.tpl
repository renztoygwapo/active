{title}Invoices{/title}
{add_bread_crumb}All Invoices{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}
{use_widget name="objects_list" module="environment"}
{use_widget name="payment_container" module="payments"}

<!-- mass_edit_url="{assemble route=invoices}" -->

<div id="invoices">
	<div class="empty_content">
		<div class="objects_list_title">{lang}Invoices{/lang}</div>
		<div class="objects_list_icon"><img src="{image_url name='icons/48x48/invoicing.png' module=invoicing}" alt=""/></div>
		
		<div class="objects_list_details_actions">
			{if Invoices::canAdd($logged_user)}
        <ul>
            <li><a href="{assemble route='invoices_add'}" id="new_invoice">{lang}New Invoice{/lang}</a></li>
        </ul>
			{/if}
		</div>


		<div class="object_lists_details_tips">
		  <h3>{lang}Tips{/lang}:</h3>
		  <ul>
		    <li>{lang}To select a invoice and load its details, please click on it in the list on the left{/lang}</li>
		    <!--<li>{lang}It is possible to select multiple invoices at the same time. Just hold Ctrl key on your keyboard and click on all the invoices that you want to select{/lang}</li>-->
		  </ul>
		</div>
	</div>
</div>

<script type="text/javascript">
  
  $('#invoices').each(function() {
    var objects_list_wrapper = $(this);
    
    var items = {$invoices|json nofilter};
    var states_map = {$status_map|json nofilter};
    var dates_map = {$invoice_dates_map|json nofilter};
    
    var mass_edit_url = '';
    var print_url = '{assemble route=people_company_invoices print=1 company_id={$active_company->getId()|json nofilter}}';

    objects_list_wrapper.objectsList({
      'id' : 'invoices',
      'items' : items,
      'objects_type' : 'invoices',
      'required_fields' : ['id', 'long_name', 'name', 'client_id', 'client_name', 'status', 'permalink'],
      'print_url' : print_url,
      'events' : App.standardObjectsListEvents(),
  		'multi_title' : App.lang(':num Invoices Selected'),
  		'multi_url' : mass_edit_url,
      'prepare_item' : function (item) {
        return {
          'id' : item['id'],
  	  		'name' : item['short_name'],
  	  		'long_name' : item['name'],
  	  		'client_id' : item['client']['id'],
  	  		'client_name' : item['client']['name'],
  	  		'status' : item['status'],
  	  		'permalink' : item['urls']['view']
        };
      },

      'grouping' : [{ 
        'label' : App.lang("Don't group"), 
        'property' : '', 
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
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

    // invoice updated
    App.Wireframe.Events.bind('invoice_updated.content invoice_issued.content invoice_canceled.content', function (event, invoice) {
      objects_list_wrapper.objectsList('update_item', invoice);
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

	  {if $active_invoice->isLoaded()}
    objects_list_wrapper.objectsList('load_item', {$active_invoice->getId()}, {$active_invoice->getCompanyViewUrl()|json nofilter}); // Pre select item if this is permalink
	  {/if}
  });
</script>