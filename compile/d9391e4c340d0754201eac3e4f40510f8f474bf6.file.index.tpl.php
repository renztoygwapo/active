<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 12:36:56
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/invoicing/views/default/invoices/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:210241498353e8b8e8a8a987-03428865%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd9391e4c340d0754201eac3e4f40510f8f474bf6' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/invoicing/views/default/invoices/index.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '210241498353e8b8e8a8a987-03428865',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e8b8e8b4f9c8_99162763',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e8b8e8b4f9c8_99162763')) {function content_53e8b8e8b4f9c8_99162763($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_image_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.image_url.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Invoices<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
All Invoices<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php echo smarty_function_use_widget(array('name'=>"paged_objects_list",'module'=>"environment"),$_smarty_tpl);?>

<?php echo smarty_function_use_widget(array('name'=>"payment_container",'module'=>"payments"),$_smarty_tpl);?>


<div id="invoices">
	<div class="empty_content">
		<div class="objects_list_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Invoices<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
		<div class="objects_list_icon"><img src="<?php echo smarty_function_image_url(array('name'=>'icons/48x48/invoicing.png','module'=>'invoicing'),$_smarty_tpl);?>
" alt=""/></div>
		<div class="objects_list_details_actions">
        <ul>
            <li><a href="<?php echo smarty_function_assemble(array('route'=>'invoices_add'),$_smarty_tpl);?>
" id="new_invoice"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New Invoice<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
        </ul>
		</div>

    <div class="object_list_details_additional_actions">
      <a href="<?php echo smarty_function_assemble(array('route'=>'invoices_archive'),$_smarty_tpl);?>
" id="view_archive"><span><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/archive.png",'module'=>"environment"),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Browse Archive<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></a>
    </div>

		<div class="object_lists_details_tips">
		  <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Tips<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</h3>
		  <ul>
		    <li><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
To select a invoice and load its details, please click on it in the list on the left<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
		  </ul>
		</div>
	</div>
</div>

<?php echo $_smarty_tpl->getSubTemplate (get_view_path('_initialize_objects_list','invoices',@INVOICING_MODULE), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>