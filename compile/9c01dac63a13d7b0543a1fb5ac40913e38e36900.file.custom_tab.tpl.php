<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 11:56:42
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/backend/custom_tab.tpl" */ ?>
<?php /*%%SmartyHeaderCode:81600020553e8af7ab9ce66-12682972%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9c01dac63a13d7b0543a1fb5ac40913e38e36900' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/backend/custom_tab.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '81600020553e8af7ab9ce66-12682972',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_custom_tab' => 0,
    'logged_user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e8af7ac1cf89_35232633',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e8af7ac1cf89_35232633')) {function content_53e8af7ac1cf89_35232633($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array('lang'=>false)); $_block_repeat=true; echo smarty_block_title(array('lang'=>false), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo clean($_smarty_tpl->tpl_vars['active_custom_tab']->value->getName(),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array('lang'=>false), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array('lang'=>false)); $_block_repeat=true; echo smarty_block_add_bread_crumb(array('lang'=>false), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo clean($_smarty_tpl->tpl_vars['active_custom_tab']->value->getName(),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array('lang'=>false), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php echo smarty_function_use_widget(array('name'=>'homescreen','module'=>@HOMESCREENS_FRAMEWORK),$_smarty_tpl);?>


<div id="homescreen_tab"><?php echo $_smarty_tpl->tpl_vars['active_custom_tab']->value->render($_smarty_tpl->tpl_vars['logged_user']->value);?>
</div><?php }} ?>