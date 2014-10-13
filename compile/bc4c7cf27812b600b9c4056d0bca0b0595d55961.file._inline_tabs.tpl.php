<?php /* Smarty version Smarty-3.1.12, created on 2014-10-03 05:46:32
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\angie\frameworks\environment\views\default\_inline_tabs.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15516542e3838f0c752-96962341%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bc4c7cf27812b600b9c4056d0bca0b0595d55961' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\angie\\frameworks\\environment\\views\\default\\_inline_tabs.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15516542e3838f0c752-96962341',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_smarty_function_inline_tabs' => 0,
    '_smarty_function_inline_tabs_id' => 0,
    'inline_tab' => 0,
    'inline_tab_id' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542e38390cff07_88005200',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542e38390cff07_88005200')) {function content_542e38390cff07_88005200($_smarty_tpl) {?><?php if (!is_callable('smarty_function_use_widget')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.use_widget.php';
?><?php echo smarty_function_use_widget(array('name'=>'inline_tabs','module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>


<?php if (is_foreachable($_smarty_tpl->tpl_vars['_smarty_function_inline_tabs']->value)){?>
  <div class="inline_tabs" id="<?php echo clean($_smarty_tpl->tpl_vars['_smarty_function_inline_tabs_id']->value,$_smarty_tpl);?>
">
    <div class="inline_tabs_links">
      <ul>
        <?php  $_smarty_tpl->tpl_vars['inline_tab'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['inline_tab']->_loop = false;
 $_smarty_tpl->tpl_vars['inline_tab_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['_smarty_function_inline_tabs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['inline_tab']->key => $_smarty_tpl->tpl_vars['inline_tab']->value){
$_smarty_tpl->tpl_vars['inline_tab']->_loop = true;
 $_smarty_tpl->tpl_vars['inline_tab_id']->value = $_smarty_tpl->tpl_vars['inline_tab']->key;
?><li><a href="<?php echo clean($_smarty_tpl->tpl_vars['inline_tab']->value['url'],$_smarty_tpl);?>
" id="<?php echo clean($_smarty_tpl->tpl_vars['_smarty_function_inline_tabs_id']->value,$_smarty_tpl);?>
_<?php echo clean($_smarty_tpl->tpl_vars['inline_tab_id']->value,$_smarty_tpl);?>
"><?php echo clean($_smarty_tpl->tpl_vars['inline_tab']->value['title'],$_smarty_tpl);?>
<?php if (isset($_smarty_tpl->tpl_vars['inline_tab']->value['count'])){?> (<span><?php echo clean($_smarty_tpl->tpl_vars['inline_tab']->value['count'],$_smarty_tpl);?>
</span>)<?php }?></a></li><?php } ?>
      </ul>
    </div>
    
    <div class="inline_tabs_content_wrapper">
      <div class="inline_tabs_loader"></div>
      <div class="inline_tabs_content"></div>
    </div>
  </div>
<?php }?>

<script type="text/javascript">
  App.widgets.InlineTabs.init('<?php echo clean($_smarty_tpl->tpl_vars['_smarty_function_inline_tabs_id']->value,$_smarty_tpl);?>
');
</script><?php }} ?>