<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:11
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\help\whats_new\4.2.4\02. Basecamp Importer.md" */ ?>
<?php /*%%SmartyHeaderCode:31339542fe1c3acd378-47688455%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f123d97c943306ba1bd87f2f0683723420952e44' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\help\\whats_new\\4.2.4\\02. Basecamp Importer.md',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '31339542fe1c3acd378-47688455',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c3b55f15_74845305',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c3b55f15_74845305')) {function content_542fe1c3b55f15_74845305($_smarty_tpl) {?>*Title: Import Your Projects from Basecamp to activeCollab
*Slug: import-from-basecamp-to-activecollab

================================================================

The latest feature from our development team is the Basecamp Importer, which helps you import all your projects from <a href="https://basecamp.com/">Basecamp</a> to activeCollab. You can now **import all your projects, files, users and TODOs** to activeCollab and continue working where you left off.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('note', array('title'=>"Where can I use the importer?")); $_block_repeat=true; echo HelpElementHelpers::block_note(array('title'=>"Where can I use the importer?"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
The Basecamp Importer is available **only on our Free Trial and Cloud platforms**. Because of the relatively high system and server resource requirements, we have decided to make this tool available on our servers only. That is the only way that we could guarantee that the data is imported successfully.

**Basecamp data import is not available for self-hosted licenses**.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_note(array('title'=>"Where can I use the importer?"), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
How can I import my Basecamp Projects?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


The import process is quite simple - all you have to do is open the <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Administration > Data Sources<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 page and enter your Basecamp details. 

You will be able to choose the projects that you wish to import. This way, you can transfer only the projects that you are currently working on and later add others if necessary.

<?php echo HelpElementHelpers::function_image(array('name'=>"import_projects.png"),$_smarty_tpl);?>


You can also import user accounts that you have set up in Basecamp. Set the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Company<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 where these users will be created, as well as their <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System Role<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 to determine the level of access these users will have in your activeCollab.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
What will happen to my TODOs?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


activeCollab and Basecamp handle Task management differently. Basecamp uses TODO lists, while in activeCollab uses <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 to determine what needs to be done.

When importing your projects you can select how you wish to import the TODOs:

- as Task Categories containing Tasks.
- as Tasks containing Subtask.

This choice gives you space to organize your work in a way that suits you best.<?php }} ?>