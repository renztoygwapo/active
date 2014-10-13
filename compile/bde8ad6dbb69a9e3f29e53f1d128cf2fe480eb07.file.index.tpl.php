<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 13:15:57
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/views/default/fw_roles_admin/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:102384637853e8c20d36b316-28724651%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bde8ad6dbb69a9e3f29e53f1d128cf2fe480eb07' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/views/default/fw_roles_admin/index.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '102384637853e8c20d36b316-28724651',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'roles' => 0,
    'role_details' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e8c20d431af8_16742714',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e8c20d431af8_16742714')) {function content_53e8c20d431af8_16742714($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
All System Roles<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
All System Roles<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="user_roles">
  <table class="common" cellspacing="0" style="width: 300px">
    <tr>
      <th class="icon"></th>
      <th class="name"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
      <th class="number_of_users right"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Number of Users<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
    </tr>
  <?php  $_smarty_tpl->tpl_vars['role_details'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['role_details']->_loop = false;
 $_smarty_tpl->tpl_vars['role_class'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['roles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['role_details']->key => $_smarty_tpl->tpl_vars['role_details']->value){
$_smarty_tpl->tpl_vars['role_details']->_loop = true;
 $_smarty_tpl->tpl_vars['role_class']->value = $_smarty_tpl->tpl_vars['role_details']->key;
?>
    <tr>
      <td class="icon"><img src="<?php echo clean($_smarty_tpl->tpl_vars['role_details']->value['icon'],$_smarty_tpl);?>
"></td>
      <td class="name"><?php echo clean($_smarty_tpl->tpl_vars['role_details']->value['name'],$_smarty_tpl);?>
</td>
      <td class="number_of_users right">
        <?php if ($_smarty_tpl->tpl_vars['role_details']->value['users_count']==0){?>
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('num'=>0)); $_block_repeat=true; echo smarty_block_lang(array('num'=>0), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
:num Users<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('num'=>0), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        <?php }elseif($_smarty_tpl->tpl_vars['role_details']->value['users_count']==1){?>
          <a href="<?php echo clean($_smarty_tpl->tpl_vars['role_details']->value['url'],$_smarty_tpl);?>
" title="<?php echo clean($_smarty_tpl->tpl_vars['role_details']->value['name'],$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
One User<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
        <?php }else{ ?>
          <a href="<?php echo clean($_smarty_tpl->tpl_vars['role_details']->value['url'],$_smarty_tpl);?>
" title="<?php echo clean($_smarty_tpl->tpl_vars['role_details']->value['name'],$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('num'=>$_smarty_tpl->tpl_vars['role_details']->value['users_count'])); $_block_repeat=true; echo smarty_block_lang(array('num'=>$_smarty_tpl->tpl_vars['role_details']->value['users_count']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
:num Users<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('num'=>$_smarty_tpl->tpl_vars['role_details']->value['users_count']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
        <?php }?>
      </td>
    </tr>
  <?php } ?>
  </table>
</div>

<script type="text/javascript">
  $('#user_roles td.number_of_users a').flyout({
    'width' : 500,
  });
</script><?php }} ?>