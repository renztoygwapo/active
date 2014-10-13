<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:12
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\help\whats_new\4.2.0\03. YouTube Video Support in Text Area.md" */ ?>
<?php /*%%SmartyHeaderCode:19427542fe1c48f0d94-62229315%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1dc740a1e99ebea5f8bc0b4985152fc877faba62' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\help\\whats_new\\4.2.0\\03. YouTube Video Support in Text Area.md',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19427542fe1c48f0d94-62229315',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c498d1b4_44808926',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c498d1b4_44808926')) {function content_542fe1c498d1b4_44808926($_smarty_tpl) {?>*Title: Improved YouTube Video and Bookmark Support
*Slug: youtube-video-bookmark-support

================================================================

For quite some time now, activeCollab has been able to display <a href="http://www.youtube.com/">YouTube</a> videos and Bookmarks, allowing you to post them in the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Files<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 section. 	

In the 4.2 release, we have simplified this feature by allowing **YouTube videos and Bookmarks in all text areas across activeCollab**. 

<?php $_smarty_tpl->smarty->_tag_stack[] = array('note', array('title'=>"Compatibility Note: Files Section Update")); $_block_repeat=true; echo HelpElementHelpers::block_note(array('title'=>"Compatibility Note: Files Section Update"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
YouTube Videos and Bookmarks used to be available in the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Files<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 section. Since the functionality update, this is no longer possible.

After upgrading to version 4.2, **users with such assets will find them in the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Discussions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 area**. All comments will be saved as a Discussion thread. So, if you find some YouTube videos or bookmarks missing from Files, no need to worry - you will find them in Discussions.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_note(array('title'=>"Compatibility Note: Files Section Update"), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Insert a YouTube Video<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


You can do this anywhere - in a description box or inside a comment. To add a video, click the <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Insert Image and Video > YouTube Video<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 icon in the text editor. Once inserted, you will be able to view the video within the text field in activeCollab.

<?php echo HelpElementHelpers::function_image(array('name'=>"youtube_video.png"),$_smarty_tpl);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Share a Bookmark<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


Click the <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
External Link<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 icon in the text editor to share a link with your team. activeCollab will apply the proper formatting and notify your team members about the new link.<?php }} ?>