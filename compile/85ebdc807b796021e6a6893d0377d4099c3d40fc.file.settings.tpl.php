<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:06:31
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\angie\frameworks\payments\views\default\fw_payment_gateways_admin\settings.tpl" */ ?>
<?php /*%%SmartyHeaderCode:27202542fe2c74f4883-56945880%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '85ebdc807b796021e6a6893d0377d4099c3d40fc' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\angie\\frameworks\\payments\\views\\default\\fw_payment_gateways_admin\\settings.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '27202542fe2c74f4883-56945880',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'allow_payments' => 0,
    'allow_payments_for_invoice' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe2c7ae03e6_74937323',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe2c7ae03e6_74937323')) {function content_542fe2c7ae03e6_74937323($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.add_bread_crumb.php';
if (!is_callable('smarty_block_form')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.form.php';
if (!is_callable('smarty_block_lang')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\block.lang.php';
if (!is_callable('smarty_block_wrap')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.wrap.php';
if (!is_callable('smarty_function_radio_field')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.radio_field.php';
if (!is_callable('smarty_block_label')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.label.php';
if (!is_callable('smarty_function_checkbox_field')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.checkbox_field.php';
if (!is_callable('smarty_block_wrap_buttons')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.submit.php';
if (!is_callable('smarty_modifier_json')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\modifier.json.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Payments Settings<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Payments Settings<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="payments_settings">
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>Router::assemble('payment_gateways_settings'),'method'=>'post','id'=>"payments_settings_admin")); $_block_repeat=true; echo smarty_block_form(array('action'=>Router::assemble('payment_gateways_settings'),'method'=>'post','id'=>"payments_settings_admin"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <div class="content_stack_wrapper">
      <div class="content_stack_element odd">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Payments Settings<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'paymentsSettingsDoNotAllow')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'paymentsSettingsDoNotAllow'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php echo smarty_function_radio_field(array('name'=>"payments_config[allow_payments]",'value'=>Payment::DO_NOT_ALLOW,'label'=>"Don't allow payments",'pre_selected_value'=>$_smarty_tpl->tpl_vars['allow_payments']->value,'class'=>"allow_payments_radio"),$_smarty_tpl);?>

          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'paymentsSettingsDoNotAllow'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'paymentsSettingsAllow')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'paymentsSettingsAllow'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php echo smarty_function_radio_field(array('name'=>"payments_config[allow_payments]",'value'=>Payment::ALLOW_FULL,'label'=>"Allow only full payments",'pre_selected_value'=>$_smarty_tpl->tpl_vars['allow_payments']->value,'class'=>"allow_payments_radio"),$_smarty_tpl);?>

          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'paymentsSettingsAllow'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'paymentsSettingsAllowFull')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'paymentsSettingsAllowFull'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php echo smarty_function_radio_field(array('name'=>"payments_config[allow_payments]",'value'=>Payment::ALLOW_PARTIAL,'label'=>"Allow full and partial payments",'pre_selected_value'=>$_smarty_tpl->tpl_vars['allow_payments']->value,'class'=>"allow_payments_radio"),$_smarty_tpl);?>

          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'paymentsSettingsAllowFull'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          
          
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'invoicePaymentsSettings')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'invoicePaymentsSettings'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array()); $_block_repeat=true; echo smarty_block_label(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Default Invoice Payments Settings<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          	<select name="payments_config[allow_payments_for_invoice]" class="default_invoice_payments_select">
          		
          	</select>
          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'invoicePaymentsSettings'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'enforceSettings')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'enforceSettings'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php echo smarty_function_checkbox_field(array('name'=>"payments_config[enforce]",'label'=>"Enforce these settings to all existing invoices",'value'=>"1"),$_smarty_tpl);?>

          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'enforceSettings'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            
        </div>
      </div>
   
    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save Changes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>Router::assemble('payment_gateways_settings'),'method'=>'post','id'=>"payments_settings_admin"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div>

<script type="text/javascript">
	var do_not_allow = <?php echo smarty_modifier_json(Payment::DO_NOT_ALLOW);?>
;
	var allow_full = <?php echo smarty_modifier_json(Payment::ALLOW_FULL);?>
;
	var allow_partial = <?php echo smarty_modifier_json(Payment::ALLOW_PARTIAL);?>
;

	var checked_value = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['allow_payments_for_invoice']->value);?>
;
	
	var allow_payments_radio = $("#payments_settings .allow_payments_radio");

	allow_payments_radio.click(function() {
		var value = $(this).val();
		populate_allow_payment_for_invoice(value);
	});

	var option_do_not_allow = new Option(App.lang('Do not allow payments'), do_not_allow);
	if(checked_value == do_not_allow) {
		$(option_do_not_allow).attr('selected','selected');
	}
	var option_allow_full = new Option(App.lang('Allow only full payments'), allow_full);
	if(checked_value == allow_full) {
		$(option_allow_full).attr('selected','selected');
	}
	var option_allow_partial = new Option(App.lang('Allow full and partial payments'), allow_partial);
	if(checked_value == allow_partial) {
		$(option_allow_partial).attr('selected','selected');
	}
	
	function populate_allow_payment_for_invoice(allow_payments_value) {
		
		var default_invoice_payments_select = $("#payments_settings .default_invoice_payments_select");
		default_invoice_payments_select.empty();
		
		default_invoice_payments_select.append(option_do_not_allow);
		//allow full only
		if(allow_payments_value > do_not_allow) {
			default_invoice_payments_select.append(option_allow_full);
		}//if
		//and partial
		if(allow_payments_value > allow_full) {
			default_invoice_payments_select.append(option_allow_partial);
		}//if
	}//render_allow_payment_for_invoice

	//set initial options
	var predefined_allow_payments = $("#payments_settings .allow_payments_radio:checked").val();
	populate_allow_payment_for_invoice(predefined_allow_payments);
	
</script><?php }} ?>