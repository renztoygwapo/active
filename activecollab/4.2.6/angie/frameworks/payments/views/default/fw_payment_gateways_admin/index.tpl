{title}Payments{/title}
{add_bread_crumb}List{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="payments_admin" class="wireframe_content_wrapper settings_panel">
  <div class="settings_panel_header">
    <table class="settings_panel_header_cell_wrapper">
      <tr>
        <td class="settings_panel_header_cell">
		      <h2>{lang}Payments Settings{/lang}</h2>
		      <div class="properties">
		        <div class="property" id="payment_settings_global">
		          <div class="label">{lang}Global payment{/lang}</div>
		          <div class="data">{display_payments_type value=$allow_payments}</div>
		        </div>
		        
		        <div class="property" id="payment_settings_invoice">
		          <div class="label">{lang}Default Invoice Payments Settings{/lang}</div>
		          <div class="data">{display_payments_type value=$allow_payments_for_invoice}</div>
		        </div>
		      </div>
          <ul class="settings_panel_header_cell_actions">
            <li>{link href=Router::assemble('payment_gateways_settings') mode=flyout_form success_event="payments_settings_updated" title="Payments Settings" class="link_button_alternative"}Change Settings{/link}</li>
         		<li>{link href=Router::assemble('payment_methods_settings') mode=flyout_form success_message="Payment methods successfully changed" title="Payment Methods" class="link_button_alternative"}Payment Methods{/link}</li>
          </ul>
        </td>
      </tr>
    </table>
  </div>
  
  <div class="settings_panel_body"><div id="payment_gateways"></div></div>
</div>

<script type="text/javascript">
  App.Wireframe.Events.bind('payments_settings_updated.content', function(event, settings) {
  	var global = settings['payment_settings_global'];
      $("#payment_settings_global .data").html(global); 
      var invoice_payment = settings['invoice_payment'];
      $("#payment_settings_invoice .data").html(invoice_payment); 
  	App.Wireframe.Flash.success(App.lang('Payments settings has been changed successfully'));
  });

  App.Wireframe.Events.bind('payment_gateway_enabled_disabled.content', function(event, payment_gateway) {
	  var radio = $('tr.list_item[list_item_id=' + payment_gateway['id'] + '] input[type=radio]');
	  if(!payment_gateway['is_enabled']) {
    	  radio.attr('disabled','disabled');
      } else {
    	  radio.removeAttr("disabled");
      }//if
  });
  

  $('#payment_gateways').pagedObjectsList({
    'load_more_url' : '{assemble route=payment_gateways_admin_section}', 
    'items' : {$payment_gateways|json nofilter},
    'items_per_load' : {$payment_gateways_per_page}, 
    'total_items' : {$total_payment_gateways}, 
    'list_items_are' : 'tr', 
    'list_item_attributes' : { 'class' : 'gateways' }, 
    'columns' : {
      'is_default' : App.lang('Default'), 
      'is_enabled' : App.lang('Enabled'), 
      'name' : App.lang('Name'), 
      'options' : '' 
    }, 
    'sort_by' : 'name', 
    'empty_message' : App.lang('There are no payment gateways defined'), 
    'listen' : 'payment_gateway', 
    'on_add_item' : function(item) {
      var gateway = $(this);
      
      gateway.append('<td class="is_default">' + 
        '<td class="is_enabled"></td>' +
        '<td class="name"></td>' + 
        '<td class="options"></td>'
      );

      var radio = $('<input name="set_default_gateway" type="radio" value="' + item['id'] + '" />').click(function() {
    	
        if(!gateway.is('tr.is_default')) {
          
          if(confirm(App.lang('Are you sure that you want to set this gateway as default?'))) {
            var cell = radio.parent();
            
            $('#payment_gateways td.is_default input[type=radio]').hide();

            cell.append('<img src="' + App.Wireframe.Utils.indicatorUrl() + '">');

            $.ajax({
              'url' : item['urls']['set_as_default'],
              'type' : 'post', 
              'data' : { 'submitted' : 'submitted' }, 
              'success' : function(response) {
                cell.find('img').remove();
                radio[0].checked = true;
                $('#payment_gateways td.is_default input[type=radio]').show();
                $('#payment_gateways tr.is_default').removeClass('is_default');

                gateway.addClass('is_default').highlightFade();
              }, 
              'error' : function(response) {
                cell.find('img').remove();
                $('#payment_gateways td.is_default input[type=radio]').show();

                App.Wireframe.Flash.error('Failed to set selected gateway as default');
              } 
            });
          } // if
        } // if

        return false;
      }).appendTo(gateway.find('td.is_default'));

      if(!item['is_enabled']) {
    	  radio.attr('disabled','disabled');
      } else {
    	  radio.removeAttr("disabled");
      }//if

      if(item['is_default']) {
        gateway.addClass('is_default');
        radio[0].checked = true;
      }

      var check_box = $('<input name="set_is_enabled" type="checkbox" value="' + item['id'] + '" on_url="' + item['urls']['enable'] + '" off_url="' + item['urls']['disable'] + '" />')
      .asyncCheckbox({
       	'success_message' : [ App.lang('Payment gateway has been disabled'), App.lang('Payment gateway has been enabled') ],
       	'success_event' : 'payment_gateway_enabled_disabled'
       })
      .appendTo(gateway.find('td.is_enabled'));
      
      if(item['is_enabled']) {
        check_box.attr('checked','checked');
      } // if
    
      gateway.find('td.name').text(item['name']);
      
      gateway.find('td.options')
        .append('<a href="' + item['urls']['view'] + '" class="gateway_details" title="' + App.lang('View Details') + '"><img src="{image_url name="icons/12x12/preview.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
        .append('<a href="' + item['urls']['edit'] + '" class="edit_gateway" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>');

      gateway.find('td.options a.gateway_details').flyout({
        'width' : 750
      });
      
      gateway.find('td.options a.edit_gateway').flyoutForm({
        'success_event' : 'payment_gateway_updated',
        'width' : 350
      });

      if(!item['is_used']) {
    	  gateway.find('td.options')
    	  	.append('<a href="' + item['urls']['delete'] + '" class="delete_gateway" title="' + App.lang('Remove gateway') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>');

    	  gateway.find('td.options a.delete_gateway').asyncLink({
	        'confirmation' : App.lang('Are you sure that you want to permanently delete this gateway?'), 
	        'success_event' : 'payment_gateway_deleted', 
	        'success_message' : App.lang('Payment gateway has been deleted successfully')
	      });
      } // if
    }
  });
</script>