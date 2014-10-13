<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 22:05:58
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/views/default/tasks/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:130302145253ac9946cc2a42-56104068%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dca261295820f27b109d09de9429b74d561e45d7' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/views/default/tasks/view.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '130302145253ac9946cc2a42-56104068',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_task' => 0,
    'logged_user' => 0,
    'request' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac9946ec0817_42232534',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac9946ec0817_42232534')) {function content_53ac9946ec0817_42232534($_smarty_tpl) {?><?php if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_object')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.object.php';
if (!is_callable('smarty_function_object_attachments')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/attachments/helpers/function.object_attachments.php';
if (!is_callable('smarty_function_object_subtasks')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/subtasks/helpers/function.object_subtasks.php';
if (!is_callable('smarty_function_object_comments')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/comments/helpers/function.object_comments.php';
if (!is_callable('smarty_function_object_history')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/history/helpers/function.object_history.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Details<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('object', array('object'=>$_smarty_tpl->tpl_vars['active_task']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value)); $_block_repeat=true; echo smarty_block_object(array('object'=>$_smarty_tpl->tpl_vars['active_task']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  <div class="wireframe_content_wrapper">
    <div class="object_body with_shadow">
      <div class="object_content_wrapper"><div class="object_body_content formatted_content"></div>
        <?php echo smarty_function_object_attachments(array('object'=>$_smarty_tpl->tpl_vars['active_task']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value),$_smarty_tpl);?>

      </div>
      <?php echo smarty_function_object_subtasks(array('object'=>$_smarty_tpl->tpl_vars['active_task']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value),$_smarty_tpl);?>

    </div>
  </div>
  
  <div class="wireframe_content_wrapper"><?php echo smarty_function_object_comments(array('object'=>$_smarty_tpl->tpl_vars['active_task']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'show_first'=>'yes'),$_smarty_tpl);?>
</div>
  <div class="wireframe_content_wrapper"><?php echo smarty_function_object_history(array('object'=>$_smarty_tpl->tpl_vars['active_task']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value),$_smarty_tpl);?>
</div>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_object(array('object'=>$_smarty_tpl->tpl_vars['active_task']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<script type="text/javascript">
  App.Wireframe.Events.bind('create_invoice_from_task.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
', function (event, invoice) {
   	if (invoice['class'] == 'Invoice') {
   		App.Wireframe.Flash.success(App.lang('New invoice created'));
   		App.Wireframe.Content.setFromUrl(invoice['urls']['view']);
	  } // if
	});
</script>	<?php }} ?>