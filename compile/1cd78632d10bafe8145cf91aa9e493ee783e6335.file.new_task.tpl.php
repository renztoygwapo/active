<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 22:05:55
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/notifications/email/new_task.tpl" */ ?>
<?php /*%%SmartyHeaderCode:41719189353ac9943e79246-46715189%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1cd78632d10bafe8145cf91aa9e493ee783e6335' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/notifications/email/new_task.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '41719189353ac9943e79246-46715189',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'context' => 0,
    'language' => 0,
    'context_view_url' => 0,
    'recipient' => 0,
    'sender' => 0,
    'style' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac994408c6f2_00627047',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac994408c6f2_00627047')) {function content_53ac994408c6f2_00627047($_smarty_tpl) {?><?php if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_block_notification_wrapper')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/email/helpers/block.notification_wrapper.php';
if (!is_callable('smarty_block_notification_wrap_body')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/email/helpers/block.notification_wrap_body.php';
if (!is_callable('smarty_function_notification_task_responsibility')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/helpers/function.notification_task_responsibility.php';
?>[<?php echo clean($_smarty_tpl->tpl_vars['context']->value->getProject()->getName(),$_smarty_tpl);?>
] <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('name'=>$_smarty_tpl->tpl_vars['context']->value->getName(),'language'=>$_smarty_tpl->tpl_vars['language']->value)); $_block_repeat=true; echo smarty_block_lang(array('name'=>$_smarty_tpl->tpl_vars['context']->value->getName(),'language'=>$_smarty_tpl->tpl_vars['language']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Task ':name' has been Created<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('name'=>$_smarty_tpl->tpl_vars['context']->value->getName(),'language'=>$_smarty_tpl->tpl_vars['language']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

================================================================================
<?php $_smarty_tpl->smarty->_tag_stack[] = array('notification_wrapper', array('title'=>'Task Created','context'=>$_smarty_tpl->tpl_vars['context']->value,'context_view_url'=>$_smarty_tpl->tpl_vars['context_view_url']->value,'recipient'=>$_smarty_tpl->tpl_vars['recipient']->value,'sender'=>$_smarty_tpl->tpl_vars['sender']->value)); $_block_repeat=true; echo smarty_block_notification_wrapper(array('title'=>'Task Created','context'=>$_smarty_tpl->tpl_vars['context']->value,'context_view_url'=>$_smarty_tpl->tpl_vars['context_view_url']->value,'recipient'=>$_smarty_tpl->tpl_vars['recipient']->value,'sender'=>$_smarty_tpl->tpl_vars['sender']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('author_name'=>$_smarty_tpl->tpl_vars['context']->value->getCreatedBy()->getDisplayName(),'url'=>$_smarty_tpl->tpl_vars['context_view_url']->value,'name'=>$_smarty_tpl->tpl_vars['context']->value->getName(),'link_style'=>$_smarty_tpl->tpl_vars['style']->value['link'],'language'=>$_smarty_tpl->tpl_vars['language']->value)); $_block_repeat=true; echo smarty_block_lang(array('author_name'=>$_smarty_tpl->tpl_vars['context']->value->getCreatedBy()->getDisplayName(),'url'=>$_smarty_tpl->tpl_vars['context_view_url']->value,'name'=>$_smarty_tpl->tpl_vars['context']->value->getName(),'link_style'=>$_smarty_tpl->tpl_vars['style']->value['link'],'language'=>$_smarty_tpl->tpl_vars['language']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
:author_name has just created "<a href=":url" style=":link_style" target="_blank">:name</a>" task<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('author_name'=>$_smarty_tpl->tpl_vars['context']->value->getCreatedBy()->getDisplayName(),'url'=>$_smarty_tpl->tpl_vars['context_view_url']->value,'name'=>$_smarty_tpl->tpl_vars['context']->value->getName(),'link_style'=>$_smarty_tpl->tpl_vars['style']->value['link'],'language'=>$_smarty_tpl->tpl_vars['language']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('notification_wrap_body', array()); $_block_repeat=true; echo smarty_block_notification_wrap_body(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['context']->value->getBody();?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_notification_wrap_body(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	<?php echo smarty_function_notification_task_responsibility(array('context'=>$_smarty_tpl->tpl_vars['context']->value,'recipient'=>$_smarty_tpl->tpl_vars['recipient']->value,'language'=>$_smarty_tpl->tpl_vars['language']->value),$_smarty_tpl);?>

<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_notification_wrapper(array('title'=>'Task Created','context'=>$_smarty_tpl->tpl_vars['context']->value,'context_view_url'=>$_smarty_tpl->tpl_vars['context_view_url']->value,'recipient'=>$_smarty_tpl->tpl_vars['recipient']->value,'sender'=>$_smarty_tpl->tpl_vars['sender']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>