<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:11
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\help\whats_new\4.2.4\01. Mention your Coworker.md" */ ?>
<?php /*%%SmartyHeaderCode:11194542fe1c39deeb1-60337901%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '660e0daa7b09b2a5e100534b8bf8a1023bc11b49' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\help\\whats_new\\4.2.4\\01. Mention your Coworker.md',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11194542fe1c39deeb1-60337901',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c3a40944_42951139',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c3a40944_42951139')) {function content_542fe1c3a40944_42951139($_smarty_tpl) {?>*Title: Mention your Co-worker
*Slug: mentions

================================================================

We all use the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
@<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 symbol in our online communication when we wish to notify or mention someone. Our team has also been using this symbol in our everyday communication, and we have come up with the idea of creating a new simple feature called <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Mentions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
How to use Mentions?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


You can use Mentions in any text area in activeCollab. Simply type <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
@<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 followed by the name of the team member you wish to notify. That person will **receive an email notification** with a link to the item where the Mention has been posted. When you are mentioned, you will receive an email about it, even if you have disabled email notifications in activeCollab. We have designed this feature to let you know that **your attention is required on a specific issues instantly**, and this is why we decided to activate email notifications, too.

<?php echo HelpElementHelpers::function_image(array('name'=>"mention.png"),$_smarty_tpl);?>


Once mentioned, the person will automatically be subscribed to the item (eg. Task) and will receive notifications about all future updates.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Who can be Mentioned?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


You will be able to mention only people who are working on that Project.

If you wish to mention someone who is not on that Project you will have to invite that person first, and then mention them.

We hope that you will enjoy using this new feature. For us, it makes working in activeCollab much easier and more fun.<?php }} ?>