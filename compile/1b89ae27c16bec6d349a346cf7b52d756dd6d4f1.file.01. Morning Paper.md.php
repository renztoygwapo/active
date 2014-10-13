<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:11
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\help\whats_new\4.2.5\01. Morning Paper.md" */ ?>
<?php /*%%SmartyHeaderCode:9445542fe1c3766124-70449445%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1b89ae27c16bec6d349a346cf7b52d756dd6d4f1' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\help\\whats_new\\4.2.5\\01. Morning Paper.md',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9445542fe1c3766124-70449445',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c38a27e3_44961565',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c38a27e3_44961565')) {function content_542fe1c38a27e3_44961565($_smarty_tpl) {?>*Title: Read the Morning Paper Email to Stay in the Loop
*Slug: morning-paper

================================================================

When your workday starts, you probably wish to see what has been going on the day before and learn what you should be working on today. Like reading a newspaper, but with news from your office.

This is exactly what activeCollab is introducing in the new release - the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Morning Paper<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 email. You will receive this email in your inbox every morning to help you keep track of recent developments.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
What will I see in this email?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


Here's how Morning Paper looks like:

<?php echo HelpElementHelpers::function_image(array('name'=>"morning_paper.png"),$_smarty_tpl);?>


The top part shows your assignments that are **late or due today**. This way, you will know which Tasks to focus on today.

The bottom part lists **completed Tasks and Subtasks, Files, and Discussions started the day before**. You will see what your team has been working on and what they have accomplished. These notifications are not about objects that you are assigned to. They are only related to Tasks, Files and Discussions of others. This will help you see the big picture of how projects that you are assigned to are progressing.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
What do I need to do to start receiving these emails?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


Nothing :) Morning Paper emails are activated by default and all your team members will receive daily news every morning at 7AM. Only users with the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Client<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 system role will not receive Morning Paper emails. 

In case you do not wish to receive Morning Paper any more, you can disable it by clicking <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Profile > Settings<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.<?php }} ?>