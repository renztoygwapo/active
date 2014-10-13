<?php /* Smarty version Smarty-3.1.12, created on 2014-09-12 16:32:52
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/views/default/fw_api_client_subscriptions/add.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10588468054132034e75936-69745977%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '76a6cebd57a47b9fbc967efc9ef3a740f90443b8' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/views/default/fw_api_client_subscriptions/add.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10588468054132034e75936-69745977',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_object' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_54132034f3ef72_03213057',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54132034f3ef72_03213057')) {function content_54132034f3ef72_03213057($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_form')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.form.php';
if (!is_callable('smarty_block_wrap_fields')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_fields.php';
if (!is_callable('smarty_block_wrap_buttons')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New Subscription<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="add_api_client_subscription">
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>$_smarty_tpl->tpl_vars['active_object']->value->getAddApiSubscriptionUrl(),'csfr_protect'=>true)); $_block_repeat=true; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['active_object']->value->getAddApiSubscriptionUrl(),'csfr_protect'=>true), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_fields', array()); $_block_repeat=true; echo smarty_block_wrap_fields(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    	<?php echo $_smarty_tpl->getSubTemplate (get_view_path('_api_client_subscription_form','fw_api_client_subscriptions',@AUTHENTICATION_FRAMEWORK), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_fields(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add Subscription<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['active_object']->value->getAddApiSubscriptionUrl(),'csfr_protect'=>true), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div><?php }} ?>