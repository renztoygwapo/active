<?php /* Smarty version Smarty-3.1.12, created on 2014-08-16 03:25:28
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_roles_admin/_role_form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:191448875953eecf28712f43-00365083%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '364534adc1606a6db4171c0509823b407cd55ad4' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_roles_admin/_role_form.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '191448875953eecf28712f43-00365083',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'role_data' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53eecf28809721_35002680',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53eecf28809721_35002680')) {function content_53eecf28809721_35002680($_smarty_tpl) {?><?php if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_function_select_project_permissions')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_project_permissions.php';
?><div class="content_stack_wrapper">
  <div class="content_stack_element odd">
    <div class="content_stack_element_info">
      <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Role<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
    </div>
    <div class="content_stack_element_body">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_text_field(array('name'=>"role[name]",'value'=>$_smarty_tpl->tpl_vars['role_data']->value['name'],'required'=>true),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </div>
  </div>
  
  <div class="content_stack_element role_permissions even">
    <div class="content_stack_element_info">
      <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Permissions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
    </div>
    <div class="content_stack_element_body">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'permissions')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'permissions'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_select_project_permissions(array('name'=>"role[permissions]",'value'=>$_smarty_tpl->tpl_vars['role_data']->value['permissions']),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'permissions'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </div>
  </div>
</div><?php }} ?>