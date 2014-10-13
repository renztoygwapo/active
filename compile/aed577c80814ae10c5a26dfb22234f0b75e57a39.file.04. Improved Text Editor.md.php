<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:13
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\help\whats_new\4.1.0\04. Improved Text Editor.md" */ ?>
<?php /*%%SmartyHeaderCode:21595542fe1c57994d9-62802260%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aed577c80814ae10c5a26dfb22234f0b75e57a39' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\help\\whats_new\\4.1.0\\04. Improved Text Editor.md',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21595542fe1c57994d9-62802260',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c5825ef8_15312872',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c5825ef8_15312872')) {function content_542fe1c5825ef8_15312872($_smarty_tpl) {?>*Title: Improved Text Editor
* Slug: redactor

================================================================

Communication is the one of the most important aspects of activeCollab. To be able to communicate with your team, you need to write down your thoughts and ideas in form of a comment or item description. We have simplified the process with our **new and improved text editor**.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Goodbye TinyMCE, Welcome Redactor<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


We have been using <a href="http://www.tinymce.com/">TinyMCE</a> for a long time and it served us well, but it is time to move on. As activeCollab is evolving, the text editor has to grow with it. This is why we have decided to give <a href="http://imperavi.com/redactor/">Redactor</a> a try. 

So far, Redactor has proved to work like a charm. It supports all the functionalities of TinyMCE had and adds a few more...

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Drag and Drop Images<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


A very practical feature! Instead of browsing for the image, you can now simply drop it in place. Thr new text editor will do the rest:

<?php echo HelpElementHelpers::function_image(array('name'=>"drag_and_drop.png"),$_smarty_tpl);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
General Performance Boost<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


Since the new text editor is light-weight (only 112KB, compared to 328KB of the old ), you can expect a general performance boost after the upgrade. You will notice that pages in your activeCollab load faster and that comment and description forms are more accessible. 

We hope the new text editor makes working in activeCollab even more enjoyable!<?php }} ?>