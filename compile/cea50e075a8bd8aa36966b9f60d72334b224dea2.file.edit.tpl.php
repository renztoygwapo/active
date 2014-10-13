<?php /* Smarty version Smarty-3.1.12, created on 2014-09-30 02:11:16
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/repsite_admin/edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:100273703540a2fd9718c58-18481188%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cea50e075a8bd8aa36966b9f60d72334b224dea2' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/repsite_admin/edit.tpl',
      1 => 1410539927,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '100273703540a2fd9718c58-18481188',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_540a2fd98b8340_96120872',
  'variables' => 
  array (
    'page_data' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_540a2fd98b8340_96120872')) {function content_540a2fd98b8340_96120872($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_block_form')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.form.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_block_wrap_editor')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_editor.php';
if (!is_callable('smarty_block_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.label.php';
if (!is_callable('smarty_block_editor_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/visual_editor/helpers/block.editor_field.php';
if (!is_callable('smarty_block_wrap_buttons')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Edit Respsite Page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Edit Repsite Page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php echo smarty_function_use_widget(array('name'=>"repsite_admin_page",'module'=>"system"),$_smarty_tpl);?>


<div id="edit_page">
<?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>'','onsubmit'=>"return getSubmitDivEditable()")); $_block_repeat=true; echo smarty_block_form(array('action'=>'','onsubmit'=>"return getSubmitDivEditable()"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<div class="content_stack_wrapper">
		<div class="content_stack_element">
	        <div class="content_stack_element_info">
	          	<h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Page Name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
	        </div>
	        <div class="content_stack_element_body">
	          	<?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	            	<?php echo smarty_function_text_field(array('rows'=>700,'name'=>"data[name]",'value'=>$_smarty_tpl->tpl_vars['page_data']->value['name'],'label'=>"Page name",'required'=>true),$_smarty_tpl);?>


	          	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	          	<p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Will be the page unique url <i>(eg:domain.com/index.php?page=<?php echo clean($_smarty_tpl->tpl_vars['page_data']->value['page_url'],$_smarty_tpl);?>
)<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</i></p>	
        	</div>	
	    </div>

	    <div class="content_stack_element">
	    	<div class="content_stack_element_info">
	        </div>
	        <div class="content_stack_element_body">

	        	<?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_editor', array('field'=>'overview')); $_block_repeat=true; echo smarty_block_wrap_editor(array('field'=>'overview'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

			      <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array()); $_block_repeat=true; echo smarty_block_label(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			      <?php $_smarty_tpl->smarty->_tag_stack[] = array('editor_field', array('class'=>"new_page_html",'name'=>"data[page_html]",'images_enabled'=>false,'id'=>"page_html_textarea")); $_block_repeat=true; echo smarty_block_editor_field(array('class'=>"new_page_html",'name'=>"data[page_html]",'images_enabled'=>false,'id'=>"page_html_textarea"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['page_data']->value['page_html'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_editor_field(array('class'=>"new_page_html",'name'=>"data[page_html]",'images_enabled'=>false,'id'=>"page_html_textarea"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_editor(array('field'=>'overview'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	        	
	          	
        	</div>
	    </div>

	</div>
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		<?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save Changes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<a href="<?php echo clean(Router::assemble('repsite_admin'),$_smarty_tpl);?>
" class="comment_cancel"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Cancel<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>'','onsubmit'=>"return getSubmitDivEditable()"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
  
</div><?php }} ?>