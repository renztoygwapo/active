<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:06:09
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\status\views\default\status\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:26670542fe2b1219036-03904916%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fc185998a5e162ffb9a5b61def7a7056ce2a0e09' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\status\\views\\default\\status\\index.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '26670542fe2b1219036-03904916',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'request' => 0,
    'dialog_id' => 0,
    'logged_user' => 0,
    'pagination' => 0,
    'pagination_url' => 0,
    'add_status_message_url' => 0,
    'status_updates' => 0,
    'rss_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe2b1741661_61951273',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe2b1741661_61951273')) {function content_542fe2b1741661_61951273($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.use_widget.php';
if (!is_callable('smarty_block_lang')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\block.lang.php';
if (!is_callable('smarty_block_pagination')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.pagination.php';
if (!is_callable('smarty_modifier_json')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\modifier.json.php';
if (!is_callable('smarty_function_assemble')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.assemble.php';
if (!is_callable('smarty_function_image_url')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.image_url.php';
?><?php if (!$_smarty_tpl->tpl_vars['request']->value->isAsyncCall()){?>
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Status Updates Archive<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Archive<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>
<?php echo smarty_function_use_widget(array('name'=>"status_update",'module'=>"status"),$_smarty_tpl);?>


<?php $_smarty_tpl->tpl_vars['dialog_id'] = new Smarty_variable(HTML::uniqueId('status_updates_dialog'), null, 0);?>

<div id="<?php echo clean($_smarty_tpl->tpl_vars['dialog_id']->value,$_smarty_tpl);?>
" class="status_updates_dialog">

  <div class="table_wrapper context_popup_scrollable"><div class="table_wrapper_inner">
    <table class="status_updates" id="status_updates_table" cellspacing="0">
      <tbody class="first_level">

      </tbody>
    </table>
  </div></div>

  <div id="add_status_message"><div id="add_status_message_wrapper">
    <div class="author_avatar">
      <img src="<?php echo clean($_smarty_tpl->tpl_vars['logged_user']->value->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG),$_smarty_tpl);?>
" alt="" />
    </div>

    <div class="author_message">
      <textarea placeholder="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Type your message and hit Enter to post it<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" maxlength="255"></textarea>
      <p class="details status_counter"></p>
    </div>
  </div></div>

<?php if (!$_smarty_tpl->tpl_vars['request']->value->isAsyncCall()&&$_smarty_tpl->tpl_vars['pagination']->value){?>
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('pagination', array('pager'=>$_smarty_tpl->tpl_vars['pagination']->value)); $_block_repeat=true; echo smarty_block_pagination(array('pager'=>$_smarty_tpl->tpl_vars['pagination']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo clean($_smarty_tpl->tpl_vars['pagination_url']->value,$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_pagination(array('pager'=>$_smarty_tpl->tpl_vars['pagination']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>

</div>

<script type="text/javascript">
  var status_update_dialog = $("#<?php echo clean($_smarty_tpl->tpl_vars['dialog_id']->value,$_smarty_tpl);?>
");

  // initialize status update dialog
  status_update_dialog.statusUpdate({
    'add_message_url' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['add_status_message_url']->value);?>
,
    'logged_user'     : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['logged_user']->value);?>
,
    'status_updates'  : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['status_updates']->value);?>

  });

  // do popup specific stuff
  var popup = status_update_dialog.parents('#context_popup:first');
  if (popup.length) {

    var trigger = popup.data('trigger');
    if (trigger.length) {
      // add popup buttons
      trigger.contextPopup('addButton', 'browse_archives', "<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Browse Archive<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
", '<?php echo smarty_function_assemble(array('route'=>'status_updates'),$_smarty_tpl);?>
', '<?php echo smarty_function_image_url(array('name'=>"icons/16x16/archive-mono.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
', false, true);
      //trigger.contextPopup('addButton', 'rss_subscribe', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Track Using RSS<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php echo clean($_smarty_tpl->tpl_vars['rss_url']->value,$_smarty_tpl);?>
', '<?php echo smarty_function_image_url(array('name'=>"icons/16x16/rss.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
');
    } // if

    // set trigger badge to 0
    App.Wireframe.Statusbar.setItemBadge(trigger.attr('id'), 0);
  } // if

</script><?php }} ?>