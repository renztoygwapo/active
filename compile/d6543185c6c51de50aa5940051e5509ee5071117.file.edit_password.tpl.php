<?php /* Smarty version Smarty-3.1.12, created on 2014-06-30 16:33:07
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/users/edit_password.tpl" */ ?>
<?php /*%%SmartyHeaderCode:118696081753b19143696718-37349039%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd6543185c6c51de50aa5940051e5509ee5071117' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/users/edit_password.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '118696081753b19143696718-37349039',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53b1914402c8f0_99506156',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53b1914402c8f0_99506156')) {function content_53b1914402c8f0_99506156($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_form')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.form.php';
if (!is_callable('smarty_block_wrap_fields')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_fields.php';
if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_block_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.label.php';
if (!is_callable('smarty_function_password_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.password_field.php';
if (!is_callable('smarty_function_password_rules')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/helpers/function.password_rules.php';
if (!is_callable('smarty_block_wrap_buttons')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Update Password<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Update Password<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="user_edit_password">
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>$_smarty_tpl->tpl_vars['active_user']->value->getEditPasswordUrl(),'csfr_protect'=>true)); $_block_repeat=true; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['active_user']->value->getEditPasswordUrl(),'csfr_protect'=>true), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_fields', array()); $_block_repeat=true; echo smarty_block_wrap_fields(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'password')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'password'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	      <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>'userPassword','required'=>'yes')); $_block_repeat=true; echo smarty_block_label(array('for'=>'userPassword','required'=>'yes'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Password<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>'userPassword','required'=>'yes'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	      <?php echo smarty_function_password_field(array('name'=>'user[password]','id'=>'userPassword','class'=>'required'),$_smarty_tpl);?>

	    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'password'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	    
	    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'repeat_password')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'repeat_password'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	      <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>'userRepeatPassword','required'=>'yes')); $_block_repeat=true; echo smarty_block_label(array('for'=>'userRepeatPassword','required'=>'yes'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Repeat password<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>'userRepeatPassword','required'=>'yes'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	      <?php echo smarty_function_password_field(array('name'=>'user[repeat_password]','id'=>'userRepeatPassword','class'=>"required validate_same_as userPassword"),$_smarty_tpl);?>

	    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'repeat_password'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


      <?php echo smarty_function_password_rules(array(),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_fields(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    	<?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Update Password<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['active_user']->value->getEditPasswordUrl(),'csfr_protect'=>true), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div>

<script type="text/javascript">
  var form = $('#user_edit_password form:first');
  var password_field = form.find('#userPassword');
  var password_repeat_field = form.find('#userRepeatPassword');

  // you cannot disable submit event propagation, so we use class 'disabled' to prevent it from propagating and handle it in flyoutForm
  form.submit(function (event) {
    var password = $.trim(password_field.val());
    var password_repeat = $.trim(password_repeat_field.val());

    if (!password || password.length < 3) {
      App.Wireframe.Flash.error(App.lang('Minimum password length is 3 characters'));
      form.addClass('disabled');
      return false;
    } // if

    if (password != password_repeat) {
      App.Wireframe.Flash.error(App.lang('Passwords do not match'));
      form.addClass('disabled');
      return false;
    } // if
  });
</script><?php }} ?>