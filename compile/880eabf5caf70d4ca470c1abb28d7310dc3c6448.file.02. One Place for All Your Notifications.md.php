<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:13
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\help\whats_new\4.0.0\02. One Place for All Your Notifications.md" */ ?>
<?php /*%%SmartyHeaderCode:5645542fe1c5e6b7e1-63969037%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '880eabf5caf70d4ca470c1abb28d7310dc3c6448' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\help\\whats_new\\4.0.0\\02. One Place for All Your Notifications.md',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5645542fe1c5e6b7e1-63969037',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c5efc094_99636539',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c5efc094_99636539')) {function content_542fe1c5efc094_99636539($_smarty_tpl) {?>* Title: All Your Notifications in One Place
* Slug: notifications-center

================================================================

activeCollab has a new way of delivering notifications. In addition to good old <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Email Notifications<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
, we are now introducing the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Notification Center<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Choose the Notification Channel<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


You can now choose how you wish to be informed about the latest events in your activeCollab. If you are overwhelmed by emails, you can disable email notifications and use **in-line notifications only **. They will be visible after logging in to activeCollab - you will see the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Notifications<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 tool at the bottom part of the application displaying the number of new notifications.

Notifications within the interface are designed so that they do not interrupt your work. On top of that, you can also configure how you wish to have them delivered:

- Number of unread notifications in the status bar and in-line notifications in the lower right corner of the interface.
- Only number of unread notifications.
- No indicators of new notifications.

<?php echo HelpElementHelpers::function_image(array('name'=>'notifications.png'),$_smarty_tpl);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Who can configure their notifications?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Administrators<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 have one additional permission -  to choose who can configure the way that notifications will be delivered. Administrators can set the users with specific <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System Roles<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 to enable or disable email notifications and set how they are displayed . 

If you feel that a specific group of users can have benefits from a specific notification delivery type, you can disable the ability to configure this option for them.

<?php echo HelpElementHelpers::function_related_video(array('name'=>'notifications'),$_smarty_tpl);?>
    

Having all notifications in one place makes it easier to keep track of recent project developments and to stay informed. The end result will be increased efficiency and more completed tasks.<?php }} ?>