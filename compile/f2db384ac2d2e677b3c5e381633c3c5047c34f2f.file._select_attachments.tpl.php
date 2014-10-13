<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 22:05:20
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/attachments/views/default/_select_attachments.tpl" */ ?>
<?php /*%%SmartyHeaderCode:214716520753ac99204bd520-91227582%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f2db384ac2d2e677b3c5e381633c3c5047c34f2f' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/attachments/views/default/_select_attachments.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '214716520753ac99204bd520-91227582',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_select_object_attachments_id' => 0,
    '_select_object_attachments_uploader_options' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac9920544e51_30565487',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac9920544e51_30565487')) {function content_53ac9920544e51_30565487($_smarty_tpl) {?><?php if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_max_file_size_warning')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.max_file_size_warning.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
?><?php echo smarty_function_use_widget(array('name'=>"select_attachments",'module'=>@FILE_UPLOADER_FRAMEWORK),$_smarty_tpl);?>


<div class="select_attachments" id="<?php echo $_smarty_tpl->tpl_vars['_select_object_attachments_id']->value;?>
">
  <table class="select_attachments_list" cellspacing="0"></table>

  <div class="upload_button" id="<?php echo $_smarty_tpl->tpl_vars['_select_object_attachments_id']->value;?>
_attach_file_button_wrapper">
    <a href="#" id="<?php echo $_smarty_tpl->tpl_vars['_select_object_attachments_id']->value;?>
_attach_file_button" class="link_button"><span class="inner"><span class="icon button_add"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Attach Files<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></span></a>
  </div>

  <p class="select_object_attachments_max_size details"><?php echo smarty_function_max_file_size_warning(array(),$_smarty_tpl);?>
</p>
</div>

<script type="text/javascript">
  (function () {
    $('#<?php echo clean($_smarty_tpl->tpl_vars['_select_object_attachments_id']->value,$_smarty_tpl);?>
').selectAttachments(<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['_select_object_attachments_uploader_options']->value);?>
);
  }());
</script><?php }} ?>