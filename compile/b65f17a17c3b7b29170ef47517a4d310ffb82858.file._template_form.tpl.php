<?php /* Smarty version Smarty-3.1.12, created on 2014-08-19 15:49:00
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_templates/_template_form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:95959062253f371ec922158-25603915%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b65f17a17c3b7b29170ef47517a4d310ffb82858' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_templates/_template_form.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '95959062253f371ec922158-25603915',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'template_data' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53f371ec9d7af1_14396283',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53f371ec9d7af1_14396283')) {function content_53f371ec9d7af1_14396283($_smarty_tpl) {?><?php if (!is_callable('smarty_block_wrap_fields')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_fields.php';
if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_fields', array()); $_block_repeat=true; echo smarty_block_wrap_fields(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <?php echo smarty_function_text_field(array('name'=>'template[name]','value'=>$_smarty_tpl->tpl_vars['template_data']->value['name'],'id'=>'templateName','class'=>'title required validate_minlength 3','required'=>true,'label'=>"Name",'maxlength'=>"150"),$_smarty_tpl);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_fields(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>