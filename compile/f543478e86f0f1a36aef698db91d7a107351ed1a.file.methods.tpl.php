<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:06:35
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\angie\frameworks\payments\views\default\fw_payment_gateways_admin\methods.tpl" */ ?>
<?php /*%%SmartyHeaderCode:32666542fe2cb2807b3-88280705%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f543478e86f0f1a36aef698db91d7a107351ed1a' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\angie\\frameworks\\payments\\views\\default\\fw_payment_gateways_admin\\methods.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '32666542fe2cb2807b3-88280705',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'payment_methods' => 0,
    'payment_method' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe2cb4e1e47_55611420',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe2cb4e1e47_55611420')) {function content_542fe2cb4e1e47_55611420($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.add_bread_crumb.php';
if (!is_callable('smarty_block_form')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.form.php';
if (!is_callable('smarty_function_string_list')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/modules/system/helpers\\function.string_list.php';
if (!is_callable('smarty_block_wrap_buttons')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Payments Methods<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Payments Methods<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="payments_settings_methods">
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>Router::assemble('payment_methods_settings'),'method'=>'post','id'=>"payments_methods_admin")); $_block_repeat=true; echo smarty_block_form(array('action'=>Router::assemble('payment_methods_settings'),'method'=>'post','id'=>"payments_methods_admin"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <div class="content_stack_wrapper">
      
    	<?php  $_smarty_tpl->tpl_vars['payment_method'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['payment_method']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_methods']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['payment_method']->key => $_smarty_tpl->tpl_vars['payment_method']->value){
$_smarty_tpl->tpl_vars['payment_method']->_loop = true;
?>
        <div class="content_stack_element">
          <div class="content_stack_element_info">
            <h3><?php echo clean($_smarty_tpl->tpl_vars['payment_method']->value['label'],$_smarty_tpl);?>
</h3>
          </div>
          
          <div class="content_stack_element_body">
            <?php echo smarty_function_string_list(array('name'=>$_smarty_tpl->tpl_vars['payment_method']->value['name'],'value'=>$_smarty_tpl->tpl_vars['payment_method']->value['value'],'link_title'=>'Add a Method'),$_smarty_tpl);?>

          </div>
        </div>
      <?php } ?>
       
    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save Changes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>Router::assemble('payment_methods_settings'),'method'=>'post','id'=>"payments_methods_admin"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div><?php }} ?>