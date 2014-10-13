<?php /* Smarty version Smarty-3.1.12, created on 2014-07-11 03:54:55
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/views/default/fw_admin/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:160601781253bf600f38fa35-84556780%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '158905fdce34900e7f91d5f45f8c2eed25d967dd' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/views/default/fw_admin/index.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '160601781253bf600f38fa35-84556780',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'admin_panel' => 0,
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53bf600f49b331_09858710',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53bf600f49b331_09858710')) {function content_53bf600f49b331_09858710($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_cycle')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/vendor/smarty/smarty/plugins/function.cycle.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Administration<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Available Administration Tools<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div class="admin_panel">
<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_smarty_tpl->tpl_vars['row_name'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['admin_panel']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
 $_smarty_tpl->tpl_vars['row_name']->value = $_smarty_tpl->tpl_vars['row']->key;
?>
  <?php if ($_smarty_tpl->tpl_vars['row']->value->hasContent()){?>
  <div class="admin_panel_row <?php echo smarty_function_cycle(array('values'=>'odd,even'),$_smarty_tpl);?>
">
    <h3><?php echo clean($_smarty_tpl->tpl_vars['row']->value->getTitle(),$_smarty_tpl);?>
</h3>
    <?php echo $_smarty_tpl->tpl_vars['row']->value->getContent();?>

  </div>
  <?php }?>
<?php } ?>
</div><?php }} ?>