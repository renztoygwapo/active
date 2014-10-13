<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:06:05
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\angie\frameworks\notifications\views\default\fw_notifications\popup.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9086542fe2ad4f2a17-50304260%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a54e8d76d1c278b962b96d14668c37f961c1eaf6' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\angie\\frameworks\\notifications\\views\\default\\fw_notifications\\popup.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9086542fe2ad4f2a17-50304260',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'notifications' => 0,
    'notification' => 0,
    'unread_notifications' => 0,
    'unseen_notifications' => 0,
    'logged_user' => 0,
    'show_only_unread' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe2ade70747_79008109',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe2ade70747_79008109')) {function content_542fe2ade70747_79008109($_smarty_tpl) {?><?php if (!is_callable('smarty_function_use_widget')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.use_widget.php';
if (!is_callable('smarty_modifier_ago')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\modifier.ago.php';
if (!is_callable('smarty_block_lang')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\block.lang.php';
if (!is_callable('smarty_block_link')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.link.php';
if (!is_callable('smarty_modifier_count')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\modifier.count.php';
if (!is_callable('smarty_modifier_json')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\modifier.json.php';
if (!is_callable('smarty_function_assemble')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.assemble.php';
?><?php echo smarty_function_use_widget(array('name'=>'notifications_popup','module'=>@NOTIFICATIONS_FRAMEWORK),$_smarty_tpl);?>


<div class="notifications_dialog" id="notifications_dialog">
  <div class="table_wrapper context_popup_scrollable">
    <div class="table_wrapper_inner">

      <div id="notifications_popup">
        <?php  $_smarty_tpl->tpl_vars['notification'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['notification']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['notifications']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['notification']->key => $_smarty_tpl->tpl_vars['notification']->value){
$_smarty_tpl->tpl_vars['notification']->_loop = true;
?>
        <a class="notification quick_view_item <?php if (in_array($_smarty_tpl->tpl_vars['notification']->value->getId(),$_smarty_tpl->tpl_vars['unread_notifications']->value)){?>unread<?php }else{ ?>read<?php }?> <?php if (in_array($_smarty_tpl->tpl_vars['notification']->value->getId(),$_smarty_tpl->tpl_vars['unseen_notifications']->value)){?>unseen<?php }?>" href="<?php echo clean($_smarty_tpl->tpl_vars['notification']->value->getVisitUrl($_smarty_tpl->tpl_vars['logged_user']->value),$_smarty_tpl);?>
">
          <span class="avatar"><?php if (($_smarty_tpl->tpl_vars['notification']->value->getSender() instanceof User)||($_smarty_tpl->tpl_vars['notification']->value->getSender() instanceof AnonymousUser)){?><img src="<?php echo clean($_smarty_tpl->tpl_vars['notification']->value->getSender()->avatar()->getUrl(@IUserAvatarImplementation::SIZE_BIG),$_smarty_tpl);?>
"><?php }?></span>
          <span class="timestamp"><span class="timestamp_wrapper"><?php echo smarty_modifier_ago($_smarty_tpl->tpl_vars['notification']->value->getCreatedOn());?>
</span></span>
          <span class="read_status"><span class="read_indicator"></span></span>
          <span class="sender_and_message">
            <span class="sender_name"><?php if (($_smarty_tpl->tpl_vars['notification']->value->getSender() instanceof User)||($_smarty_tpl->tpl_vars['notification']->value->getSender() instanceof AnonymousUser)){?><?php echo clean($_smarty_tpl->tpl_vars['notification']->value->getSender()->getDisplayName(),$_smarty_tpl);?>
<?php }else{ ?><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }?></span>
            <span class="message"><?php echo $_smarty_tpl->tpl_vars['notification']->value->getMessage($_smarty_tpl->tpl_vars['logged_user']->value);?>
</span>
          </span>
        </a>
        <?php } ?>
      </div>

    </div>
  </div>

  <div id="navigate_to_all_notifications">
    <span id="navigate_to_all_notifications_left">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('href'=>Router::assemble('notifications'),'id'=>"notification_center")); $_block_repeat=true; echo smarty_block_link(array('href'=>Router::assemble('notifications'),'id'=>"notification_center"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
See All Notifications<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('href'=>Router::assemble('notifications'),'id'=>"notification_center"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </span>
    <span id="navigate_to_all_notifications_right">
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('href'=>Router::assemble('notifications_mass_edit'),'id'=>"mark_all_notifications_as_read")); $_block_repeat=true; echo smarty_block_link(array('href'=>Router::assemble('notifications_mass_edit'),'id'=>"mark_all_notifications_as_read"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Mark All as Read<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('href'=>Router::assemble('notifications_mass_edit'),'id'=>"mark_all_notifications_as_read"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
&middot;<?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('href'=>Router::assemble('notifications_mass_edit'),'id'=>"delete_all_notifications")); $_block_repeat=true; echo smarty_block_link(array('href'=>Router::assemble('notifications_mass_edit'),'id'=>"delete_all_notifications"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Delete All<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('href'=>Router::assemble('notifications_mass_edit'),'id'=>"delete_all_notifications"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </span>
  </div>
</div>

<script type="text/javascript">
  $('#notifications_dialog').each(function() {
    var notifications_dialog = $(this);
    var notifications_count = <?php echo smarty_modifier_count($_smarty_tpl->tpl_vars['notifications']->value);?>
;
    var notifications_dialog_table_wrapper = notifications_dialog.find('.table_wrapper');
    var notifications_dialog_table_wrapper_inner = notifications_dialog.find('.table_wrapper_inner');
    var popup = notifications_dialog.parents('#context_popup:first');
    var trigger = popup.data('trigger');

    var show_only_unread = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['show_only_unread']->value);?>
;
    var show_read_and_unread_url = '<?php echo smarty_function_assemble(array('route'=>"notifications_popup_show_read_and_unread"),$_smarty_tpl);?>
';
    var show_only_unread_url = '<?php echo smarty_function_assemble(array('route'=>"notifications_popup_show_only_unread"),$_smarty_tpl);?>
';

    var mark_all_read_link = notifications_dialog.find('#mark_all_notifications_as_read');
    var delete_all_link = notifications_dialog.find('#delete_all_notifications');
    var notification_centar_link = notifications_dialog.find('#notification_center');

    var button_show_only_unread;
    var button_show_read_and_unread;

    // reset number of unseen notifications when dialog opens
    App.Wireframe.Statusbar.setItemBadge('statusbar_item_notifications', 0);

    // when you click on any notification, close the dialog
    $('div.notifications_dialog').each(function() {
      var wrapper = $(this);
      wrapper.on('click', 'a.notification', function(event) {
        if (trigger && trigger.length) {
          if ($.platform.mac && !event.metaKey || !$.platform.mac && !event.ctrlKey) {
            setTimeout(function () {
              trigger.contextPopup('close');
            }, 1);
          } // if
        } // if
      });
    });

    /**
     * Show empty slate
     */
    var show_empty_slate = function () {
      // empty table
      notifications_dialog_table_wrapper_inner.hide()
      notifications_dialog_table_wrapper_inner.find('p.empty_slate').remove();

      // append empty slate
      var empty_slate = $('<p class="empty_slate"><span class="empty_slate_icon"><img src="' + App.Wireframe.Utils.imageUrl('icons/32x32/popup-empty-slate.png', 'notifications') + '" /></span></p>').appendTo(notifications_dialog_table_wrapper);
      if (show_only_unread) {
        empty_slate.append(App.lang('There are no unread notifications at this moment'));
      } else {
        empty_slate.append(App.lang('There are no notifications at this moment'));
      } // if

      // disable links
      disable_link(mark_all_read_link);
      disable_link(delete_all_link);

      if (notifications_count == 0) {
        disable_link(notification_centar_link);
      } // if

      // reposition context popup
      if (trigger && trigger.length) {
        trigger.contextPopup('reposition');
      } // if
    }; // show_empty_slate

    /**
     * Disable link
     *
     * @param link
     */
    var disable_link = function (link) {
      if (link && link.length) {
        link.data('disabled', true).css({
          'opacity': '0.3',
          'text-decoration' : 'none !important'
        });
      } // if
    } // disable_link

    /**
     * Enable link
     *
     * @param link
     */
    var enable_link = function (link) {
      if (link && link.length) {
        link.data('disabled', false).css({
          'opacity': '1'
        });
      } // if
    } // disable_link

    // mark all read handler
    mark_all_read_link.click(function () {
      if (mark_all_read_link.data('disabled')) {
        return false;
      } // if

      disable_link(mark_all_read_link);

      $.ajax({
        'url' : mark_all_read_link.attr('href'),
        'type' : 'post',
        'data' : {
          'submitted' : 'submitted',
          'mass_edit_action' : 'mark_all_read'
        }
      });

      notifications_dialog_table_wrapper.find('a.notification').removeClass('unseen unread').addClass('read');
      App.Wireframe.Flash.success('All notifications marked as read');
      return false;
    });

    // delete all handler
    delete_all_link.click(function () {
      if ($(this).data('disabled')) {
        return false;
      } // if

      $.ajax({
        'url' : delete_all_link.attr('href'),
        'type' : 'post',
        'data' : {
          'submitted' : 'submitted',
          'mass_edit_action' : 'delete_all'
        }
      });

      show_empty_slate();
      disable_link(notification_centar_link);
      App.Wireframe.Flash.success('All notifications deleted');
      return false;
    });

    notification_centar_link.click(function () {
      if ($(this).data('disabled')) {
        return false;
      } // if

      if (trigger && trigger.length) {
        trigger.contextPopup('close');
      } // if
    })

    var update_popup_layout;

    /**
     * Update poppu layout
     */
    update_popup_layout = function () {
      if (button_show_read_and_unread && button_show_read_and_unread.length) {
        button_show_read_and_unread.remove();
      } // if

      if (button_show_only_unread && button_show_only_unread.length) {
        button_show_only_unread.remove();
      } // if

      // add buttons when dialog opens
      if (trigger && trigger.length) {
        if (show_only_unread) {
          trigger.contextPopup('addButton', 'show_read_and_unread', "<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Show only unread notifications<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
", show_read_and_unread_url, App.Wireframe.Utils.imageUrl('icons/16x16/checkbox-checked.png', 'complete'), function () {
            show_only_unread = false;
            update_popup_layout();

            $.ajax({
              'url' : show_read_and_unread_url,
              'type' : 'post',
              'data' : {
                'submitted': 'submitted'
              }
            });

            return false;
          }, true);

          button_show_read_and_unread = $('#context_popup_title_buttons .button_show_read_and_unread');
          button_show_only_unread = null;
        } else {
          trigger.contextPopup('addButton', 'show_only_unread', "<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Show only unread notifications<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
", show_only_unread_url, App.Wireframe.Utils.imageUrl('icons/16x16/checkbox-unchecked.png', 'complete'), function () {
            show_only_unread = true;
            update_popup_layout();

            $.ajax({
              'url' : show_only_unread_url,
              'type' : 'post',
              'data' : {
                'submitted': 'submitted'
              }
            });

            return false;
          }, true);

          button_show_only_unread = $('#context_popup_title_buttons .button_show_only_unread');
          button_show_read_and_unread = null;
        } // if
      } // if

      notifications_dialog_table_wrapper_inner.show();
      notifications_dialog_table_wrapper.find('a.notification').show();

      enable_link(mark_all_read_link);
      enable_link(delete_all_link);
      enable_link(notification_centar_link);

      notifications_dialog_table_wrapper.find('p.empty_slate').remove();

      if (show_only_unread) {
        notifications_dialog_table_wrapper.find('a.notification.read').hide();
      } // if

      if (!notifications_dialog_table_wrapper.find('a.notification:visible').length) {
        show_empty_slate()
      } // if

      if (trigger && trigger.length) {
        trigger.contextPopup('reposition');
      } // if
    } // update_popup_layout

    // perform inital update
    update_popup_layout();
  });
</script><?php }} ?>