<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:12
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\help\whats_new\4.2.0\02. Calendar Filter.md" */ ?>
<?php /*%%SmartyHeaderCode:23156542fe1c4664770-11834466%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7eb55f5a98743639fc5725202f34d6cedbb6e1ca' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\help\\whats_new\\4.2.0\\02. Calendar Filter.md',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '23156542fe1c4664770-11834466',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c46f8e99_66091976',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c46f8e99_66091976')) {function content_542fe1c46f8e99_66091976($_smarty_tpl) {?>*Title: Calendar Filter
*slug: calendar-filter

================================================================

The latest improvement to our <?php $_smarty_tpl->smarty->_tag_stack[] = array('page', array('name'=>"schedule",'book'=>"calendar")); $_block_repeat=true; echo HelpElementHelpers::block_page(array('name'=>"schedule",'book'=>"calendar"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Calendar<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_page(array('name'=>"schedule",'book'=>"calendar"), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 is the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Calendar Filter<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
. This feature will allow you to narrow down calendar entries and display only the information relevant to you.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Filter Options<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


To fine tune the view in your Calendar, use the <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Filter<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 dropdown menu. The following options are available:  

- See **everything in my Projects**, which includes all Tasks, Subtasks and Milestones from all the projects where you are assigned will be displayed.
- See only **your Assignments** in Projects that you are working on.

<?php echo HelpElementHelpers::function_image(array('name'=>"calendar-filter.png"),$_smarty_tpl);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Additional Filter Options for Administrators and Managers<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


In case you are an <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Administrator<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 or <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Project Manager<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
, you will be able to use two additional options of the Calendar Filter: 

- to see **calendar entries from all projects** 
- to see **the assignments of another user**.

These options offer you better insight into what your team is working on and how exactly their workload is distributed. You can easily reschedule Milestones and set different due days for Tasks by dragging and dropping them into place. It's as simple as that!<?php }} ?>