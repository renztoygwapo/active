<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:05:36
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\views\default\repsite_admin\edit_repsite_domain.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2000542fe29000dfc6-58663026%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b749323e01c462a9bd3057fffd765e71e9dd500c' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\views\\default\\repsite_admin\\edit_repsite_domain.tpl',
      1 => 1410539928,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2000542fe29000dfc6-58663026',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'config_opt' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe29025fc40_06942043',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe29025fc40_06942043')) {function content_542fe29025fc40_06942043($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.add_bread_crumb.php';
if (!is_callable('smarty_block_form')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.form.php';
if (!is_callable('smarty_block_wrap')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.wrap.php';
if (!is_callable('smarty_function_text_field')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.text_field.php';
if (!is_callable('smarty_block_lang')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\block.lang.php';
if (!is_callable('smarty_block_wrap_buttons')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Edit Repsite Domain<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Edit Repsite Domain<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>



<div id="add_new_page">
<?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>Router::assemble('repsite_admin_edit_repsite_domain'))); $_block_repeat=true; echo smarty_block_form(array('action'=>Router::assemble('repsite_admin_edit_repsite_domain')), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<div class="content_stack_wrapper">
		<div class="content_stack_element">
	        <div class="content_stack_element_body">
	          		<?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'repsite_domain')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'repsite_domain'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            			<?php echo smarty_function_text_field(array('name'=>"config_opt[rep_site_domain]",'value'=>$_smarty_tpl->tpl_vars['config_opt']->value['rep_site_domain'],'label'=>'Default Rep Site Domain'),$_smarty_tpl);?>

		            	<p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Repsite Domain Name eg: abuckagallon.com<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
		            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'repsite_domain'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        	</div>
	    </div>


	</div>
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		<?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>Router::assemble('repsite_admin_edit_repsite_domain')), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
    
</div>

<script type="text/javascript">
$(document).ready(function(){
	
});
</script>
<?php }} ?>