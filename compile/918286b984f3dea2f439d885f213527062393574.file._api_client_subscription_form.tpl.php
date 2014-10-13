<?php /* Smarty version Smarty-3.1.12, created on 2014-09-12 16:32:53
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/views/default/fw_api_client_subscriptions/_api_client_subscription_form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:40268288354132035007882-00833286%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '918286b984f3dea2f439d885f213527062393574' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/views/default/fw_api_client_subscriptions/_api_client_subscription_form.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '40268288354132035007882-00833286',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'api_client_subscription_data' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_54132035068869_29560662',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54132035068869_29560662')) {function content_54132035068869_29560662($_smarty_tpl) {?><?php if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_yes_no')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.yes_no.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'client_name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'client_name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  <?php echo smarty_function_text_field(array('name'=>"api_client_subscription[client_name]",'value'=>$_smarty_tpl->tpl_vars['api_client_subscription_data']->value['client_name'],'required'=>true,'label'=>"Client Name"),$_smarty_tpl);?>

  <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Example: activeCollab Timer<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'client_name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'client_vendor')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'client_vendor'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  <?php echo smarty_function_text_field(array('name'=>"api_client_subscription[client_vendor]",'value'=>$_smarty_tpl->tpl_vars['api_client_subscription_data']->value['client_vendor'],'label'=>"Client Vendor"),$_smarty_tpl);?>

<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'client_vendor'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'read_only')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'read_only'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  <?php echo smarty_function_yes_no(array('name'=>"api_client_subscription[is_read_only]",'value'=>$_smarty_tpl->tpl_vars['api_client_subscription_data']->value['is_read_only'],'label'=>"Read Only"),$_smarty_tpl);?>

<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'read_only'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>