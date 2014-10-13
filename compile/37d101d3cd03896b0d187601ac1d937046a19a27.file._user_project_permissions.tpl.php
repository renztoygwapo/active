<?php /* Smarty version Smarty-3.1.12, created on 2014-06-18 17:11:52
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/_user_project_permissions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2099816278539bec909d57e2-99557964%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '37d101d3cd03896b0d187601ac1d937046a19a27' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/_user_project_permissions.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2099816278539bec909d57e2-99557964',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_539bec90ab0647_09365323',
  'variables' => 
  array (
    'id' => 0,
    'project_roles' => 0,
    'name' => 0,
    'role_id_field' => 0,
    'project_role_id' => 0,
    'role_id' => 0,
    'project_role_name' => 0,
    'permissions_field' => 0,
    'permissions' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_539bec90ab0647_09365323')) {function content_539bec90ab0647_09365323($_smarty_tpl) {?><?php if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_select_project_permissions')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_project_permissions.php';
?><table class="select_user_project_permissions" id="<?php echo clean($_smarty_tpl->tpl_vars['id']->value,$_smarty_tpl);?>
">
<?php  $_smarty_tpl->tpl_vars['project_role_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['project_role_name']->_loop = false;
 $_smarty_tpl->tpl_vars['project_role_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['project_roles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['project_role_name']->key => $_smarty_tpl->tpl_vars['project_role_name']->value){
$_smarty_tpl->tpl_vars['project_role_name']->_loop = true;
 $_smarty_tpl->tpl_vars['project_role_id']->value = $_smarty_tpl->tpl_vars['project_role_name']->key;
?>
  <tr>
    <td class="radio"><input type="radio" name="<?php echo clean($_smarty_tpl->tpl_vars['name']->value,$_smarty_tpl);?>
[<?php echo clean($_smarty_tpl->tpl_vars['role_id_field']->value,$_smarty_tpl);?>
]" value="<?php echo clean($_smarty_tpl->tpl_vars['project_role_id']->value,$_smarty_tpl);?>
" id="<?php echo clean($_smarty_tpl->tpl_vars['id']->value,$_smarty_tpl);?>
_role_<?php echo clean($_smarty_tpl->tpl_vars['project_role_id']->value,$_smarty_tpl);?>
" class="inline input_radio" <?php if ($_smarty_tpl->tpl_vars['role_id']->value==$_smarty_tpl->tpl_vars['project_role_id']->value){?>checked="checked"<?php }?> /></td>
    <td class="label"><label for="<?php echo clean($_smarty_tpl->tpl_vars['id']->value,$_smarty_tpl);?>
_role_<?php echo clean($_smarty_tpl->tpl_vars['project_role_id']->value,$_smarty_tpl);?>
"><?php echo clean($_smarty_tpl->tpl_vars['project_role_name']->value,$_smarty_tpl);?>
</label></td>
  </tr>
<?php } ?>
  <tr>
    <td class="radio"><input type="radio" name="<?php echo clean($_smarty_tpl->tpl_vars['name']->value,$_smarty_tpl);?>
[<?php echo clean($_smarty_tpl->tpl_vars['role_id_field']->value,$_smarty_tpl);?>
]" value="0" id="<?php echo clean($_smarty_tpl->tpl_vars['id']->value,$_smarty_tpl);?>
_role_0" class="inline input_radio" <?php if ($_smarty_tpl->tpl_vars['role_id']->value==0){?>checked="checked"<?php }?> /></td>
    <td class="label">
      <label for="<?php echo clean($_smarty_tpl->tpl_vars['id']->value,$_smarty_tpl);?>
_role_0"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Custom Permissions ...<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
      
      <div class="custom_permissions" <?php if ($_smarty_tpl->tpl_vars['role_id']->value>0){?>style="display: none"<?php }?>>
        <?php echo smarty_function_select_project_permissions(array('name'=>((string)$_smarty_tpl->tpl_vars['name']->value)."[".((string)$_smarty_tpl->tpl_vars['permissions_field']->value)."]",'value'=>$_smarty_tpl->tpl_vars['permissions']->value),$_smarty_tpl);?>

      </div>
    </td>
  </tr>
</table>
<script type="text/javascript">
  $('#<?php echo clean($_smarty_tpl->tpl_vars['id']->value,$_smarty_tpl);?>
').each(function() {
    var wrapper = $(this);

    // Hide radio button/label if there are no project roles defined
    if(wrapper.find('tr td.radio').length == 1) {
    	wrapper.find('tr td.radio').hide();
    	wrapper.find('tr td.label label:first').hide();
    } // if
    
    // Show/hide custom permissions
    wrapper.find('td.radio input').click(function() {
      if($(this).attr('value') == '0') {
        wrapper.find('td div.custom_permissions').slideDown();
      } else {
        wrapper.find('td div.custom_permissions').slideUp();
      } // if
    });
  });
</script><?php }} ?>