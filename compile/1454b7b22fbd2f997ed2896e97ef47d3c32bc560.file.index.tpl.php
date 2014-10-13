<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:06:26
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\angie\frameworks\payments\views\default\fw_payment_gateways_admin\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16524542fe2c214dc42-63207230%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1454b7b22fbd2f997ed2896e97ef47d3c32bc560' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\angie\\frameworks\\payments\\views\\default\\fw_payment_gateways_admin\\index.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16524542fe2c214dc42-63207230',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'allow_payments' => 0,
    'allow_payments_for_invoice' => 0,
    'payment_gateways' => 0,
    'payment_gateways_per_page' => 0,
    'total_payment_gateways' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe2c260cae1_73542769',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe2c260cae1_73542769')) {function content_542fe2c260cae1_73542769($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.use_widget.php';
if (!is_callable('smarty_block_lang')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\block.lang.php';
if (!is_callable('smarty_function_display_payments_type')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/payments/helpers\\function.display_payments_type.php';
if (!is_callable('smarty_block_link')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.link.php';
if (!is_callable('smarty_function_assemble')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.assemble.php';
if (!is_callable('smarty_modifier_json')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\modifier.json.php';
if (!is_callable('smarty_function_image_url')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.image_url.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Payments<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
List<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php echo smarty_function_use_widget(array('name'=>"paged_objects_list",'module'=>"environment"),$_smarty_tpl);?>


<div id="payments_admin" class="wireframe_content_wrapper settings_panel">
  <div class="settings_panel_header">
    <table class="settings_panel_header_cell_wrapper">
      <tr>
        <td class="settings_panel_header_cell">
		      <h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Payments Settings<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
		      <div class="properties">
		        <div class="property" id="payment_settings_global">
		          <div class="label"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Global payment<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
		          <div class="data"><?php echo smarty_function_display_payments_type(array('value'=>$_smarty_tpl->tpl_vars['allow_payments']->value),$_smarty_tpl);?>
</div>
		        </div>
		        
		        <div class="property" id="payment_settings_invoice">
		          <div class="label"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Default Invoice Payments Settings<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
		          <div class="data"><?php echo smarty_function_display_payments_type(array('value'=>$_smarty_tpl->tpl_vars['allow_payments_for_invoice']->value),$_smarty_tpl);?>
</div>
		        </div>
		      </div>
          <ul class="settings_panel_header_cell_actions">
            <li><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('href'=>Router::assemble('payment_gateways_settings'),'mode'=>'flyout_form','success_event'=>"payments_settings_updated",'title'=>"Payments Settings",'class'=>"link_button_alternative")); $_block_repeat=true; echo smarty_block_link(array('href'=>Router::assemble('payment_gateways_settings'),'mode'=>'flyout_form','success_event'=>"payments_settings_updated",'title'=>"Payments Settings",'class'=>"link_button_alternative"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Change Settings<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('href'=>Router::assemble('payment_gateways_settings'),'mode'=>'flyout_form','success_event'=>"payments_settings_updated",'title'=>"Payments Settings",'class'=>"link_button_alternative"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
         		<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('href'=>Router::assemble('payment_methods_settings'),'mode'=>'flyout_form','success_message'=>"Payment methods successfully changed",'title'=>"Payment Methods",'class'=>"link_button_alternative")); $_block_repeat=true; echo smarty_block_link(array('href'=>Router::assemble('payment_methods_settings'),'mode'=>'flyout_form','success_message'=>"Payment methods successfully changed",'title'=>"Payment Methods",'class'=>"link_button_alternative"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Payment Methods<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('href'=>Router::assemble('payment_methods_settings'),'mode'=>'flyout_form','success_message'=>"Payment methods successfully changed",'title'=>"Payment Methods",'class'=>"link_button_alternative"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
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
    'load_more_url' : '<?php echo smarty_function_assemble(array('route'=>'payment_gateways_admin_section'),$_smarty_tpl);?>
', 
    'items' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['payment_gateways']->value);?>
,
    'items_per_load' : <?php echo clean($_smarty_tpl->tpl_vars['payment_gateways_per_page']->value,$_smarty_tpl);?>
, 
    'total_items' : <?php echo clean($_smarty_tpl->tpl_vars['total_payment_gateways']->value,$_smarty_tpl);?>
, 
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
        .append('<a href="' + item['urls']['view'] + '" class="gateway_details" title="' + App.lang('View Details') + '"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/preview.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" /></a>')
        .append('<a href="' + item['urls']['edit'] + '" class="edit_gateway" title="' + App.lang('Change Settings') + '"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/edit.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" /></a>');

      gateway.find('td.options a.gateway_details').flyout({
        'width' : 750
      });
      
      gateway.find('td.options a.edit_gateway').flyoutForm({
        'success_event' : 'payment_gateway_updated',
        'width' : 350
      });

      if(!item['is_used']) {
    	  gateway.find('td.options')
    	  	.append('<a href="' + item['urls']['delete'] + '" class="delete_gateway" title="' + App.lang('Remove gateway') + '"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/delete.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" /></a>');

    	  gateway.find('td.options a.delete_gateway').asyncLink({
	        'confirmation' : App.lang('Are you sure that you want to permanently delete this gateway?'), 
	        'success_event' : 'payment_gateway_deleted', 
	        'success_message' : App.lang('Payment gateway has been deleted successfully')
	      });
      } // if
    }
  });
</script><?php }} ?>