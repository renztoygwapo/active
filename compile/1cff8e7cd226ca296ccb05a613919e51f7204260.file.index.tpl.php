<?php /* Smarty version Smarty-3.1.12, created on 2014-09-13 01:22:35
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/repsite_admin/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:116909657253ee40c94dc6d2-97422947%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1cff8e7cd226ca296ccb05a613919e51f7204260' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/repsite_admin/index.tpl',
      1 => 1410539930,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '116909657253ee40c94dc6d2-97422947',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ee40c9903dd9_13137189',
  'variables' => 
  array (
    'repsite_pages' => 0,
    'page' => 0,
    'rep_site_domain' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ee40c9903dd9_13137189')) {function content_53ee40c9903dd9_13137189($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_function_image_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.image_url.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manage Repsite<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
All Repsite Pages<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php echo smarty_function_use_widget(array('name'=>"repsite_admin_page",'module'=>"system"),$_smarty_tpl);?>

<!-- 
use_widget name="paged_objects_list" module="environment"

<div id="repsite_pages_admin"></div>
-->

<div id="repsite_pages_lists">
	<?php if ($_smarty_tpl->tpl_vars['repsite_pages']->value){?>
		<table class="common" cellspacing="0">
			<tr>
				<th class="icon"></th>
				<th>ID</th>
				<th>Page Name</th>
				<th>Page Url</th>
				<th>Page HTML</th>
				<th></th>
			</tr>
			<?php  $_smarty_tpl->tpl_vars['page'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['page']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['repsite_pages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['page']->key => $_smarty_tpl->tpl_vars['page']->value){
$_smarty_tpl->tpl_vars['page']->_loop = true;
?>
				<tr>
					<td class="icon"></td> 
					<td><?php echo clean($_smarty_tpl->tpl_vars['page']->value['id'],$_smarty_tpl);?>
</td>
					<td><?php echo clean($_smarty_tpl->tpl_vars['page']->value['name'],$_smarty_tpl);?>
</td>
					<td><?php echo clean($_smarty_tpl->tpl_vars['rep_site_domain']->value,$_smarty_tpl);?>
/page.php?path_info=<?php echo clean($_smarty_tpl->tpl_vars['page']->value['page_url'],$_smarty_tpl);?>
</td>
					<td><?php echo clean($_smarty_tpl->tpl_vars['page']->value['page_html'],$_smarty_tpl);?>
</td>
					<td>
						<a class="delete_repsite_page" title="Delete Repsite Page" href="<?php echo clean($_smarty_tpl->tpl_vars['page']->value['delete_url'],$_smarty_tpl);?>
"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/delete.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" alt="' + App.lang('Delete') + '" /></a>
						<a class="edit_repsite_page" title="Edit Repsite Page" href="<?php echo clean($_smarty_tpl->tpl_vars['page']->value['edit_url'],$_smarty_tpl);?>
"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/edit.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" alt="' + App.lang('Delete') + '" /></a>
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php }else{ ?>
	  <p class="empty_page"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Empty<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
	<?php }?>
    
</div>

<script type="text/javascript">
	
</script><?php }} ?>