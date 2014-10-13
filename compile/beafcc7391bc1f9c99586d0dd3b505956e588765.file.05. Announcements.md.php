<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:14
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\help\whats_new\4.0.0\05. Announcements.md" */ ?>
<?php /*%%SmartyHeaderCode:14542fe1c673bc53-95608417%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'beafcc7391bc1f9c99586d0dd3b505956e588765' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\help\\whats_new\\4.0.0\\05. Announcements.md',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14542fe1c673bc53-95608417',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c67b4de1_46414753',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c67b4de1_46414753')) {function content_542fe1c67b4de1_46414753($_smarty_tpl) {?>* Title: Announcements
* Slug: announcements

================================================================

How often do you need to pass on an important piece of information to your team and make sure everyone sees it? Wouldn't it be great notify everyone right from activeCollab?

This has now become possible with the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Announcement<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 tool. Sharing an important piece of information is now as easy as **writing a message and posting it on everyone's dashboard**. 

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Posting New Announcements<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


To make an announcement, open <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Administration > Announcements<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
. Enter the announcement title and text body. You can also **choose to notify only a selected group of users**. This can be useful when, for example, sending a message to all managers without having to select individual users. You can also send a welcome message to new users or provide necessary information, which will be available on the dashboard as soon as they log in. 

To characterize the nature or the importance of the message, apply different kinds of icons.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Displaying Announcements<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


By setting the expiry time, you can choose whether employees will be able to dismiss an announcement from their <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Dashboard<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 manually or it will disappear automatically after a period of time.

<?php echo HelpElementHelpers::function_image(array('name'=>'announcements_zoom.png'),$_smarty_tpl);?>


Besides displaying a message on the Dashboard, notifying the recipients via email can also be enabled. This replaces and improves the <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Mass Mailer<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 option, a feature familiar to users who have used older versions of activeCollab (we have dropped support for this feature with activeCollab 3.3). 

This way, you can make sure that your team members will never miss an important announcement. 

<?php echo HelpElementHelpers::function_related_video(array('name'=>'my-activecollab'),$_smarty_tpl);?>
<?php }} ?>