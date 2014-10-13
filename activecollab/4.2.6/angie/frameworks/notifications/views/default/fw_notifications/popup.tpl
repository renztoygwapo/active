{use_widget name='notifications_popup' module=$smarty.const.NOTIFICATIONS_FRAMEWORK}

<div class="notifications_dialog" id="notifications_dialog">
  <div class="table_wrapper context_popup_scrollable">
    <div class="table_wrapper_inner">

      <div id="notifications_popup">
        {foreach $notifications as $notification}
        <a class="notification quick_view_item {if in_array($notification->getId(), $unread_notifications)}unread{else}read{/if} {if in_array($notification->getId(), $unseen_notifications)}unseen{/if}" href="{$notification->getVisitUrl($logged_user)}">
          <span class="avatar">{if ($notification->getSender() instanceof User) || ($notification->getSender() instanceof AnonymousUser)}<img src="{$notification->getSender()->avatar()->getUrl($smarty.const.IUserAvatarImplementation::SIZE_BIG)}">{/if}</span>
          <span class="timestamp"><span class="timestamp_wrapper">{$notification->getCreatedOn()|ago nofilter}</span></span>
          <span class="read_status"><span class="read_indicator"></span></span>
          <span class="sender_and_message">
            <span class="sender_name">{if ($notification->getSender() instanceof User) || ($notification->getSender() instanceof AnonymousUser)}{$notification->getSender()->getDisplayName()}{else}{lang}System{/lang}{/if}</span>
            <span class="message">{$notification->getMessage($logged_user) nofilter}</span>
          </span>
        </a>
        {/foreach}
      </div>

    </div>
  </div>

  <div id="navigate_to_all_notifications">
    <span id="navigate_to_all_notifications_left">
      {link href=Router::assemble('notifications') id="notification_center"}See All Notifications{/link}
    </span>
    <span id="navigate_to_all_notifications_right">
      {link href=Router::assemble('notifications_mass_edit') id="mark_all_notifications_as_read"}Mark All as Read{/link}&middot;{link href=Router::assemble('notifications_mass_edit') id="delete_all_notifications"}Delete All{/link}
    </span>
  </div>
</div>

<script type="text/javascript">
  $('#notifications_dialog').each(function() {
    var notifications_dialog = $(this);
    var notifications_count = {$notifications|@count nofilter};
    var notifications_dialog_table_wrapper = notifications_dialog.find('.table_wrapper');
    var notifications_dialog_table_wrapper_inner = notifications_dialog.find('.table_wrapper_inner');
    var popup = notifications_dialog.parents('#context_popup:first');
    var trigger = popup.data('trigger');

    var show_only_unread = {$show_only_unread|json nofilter};
    var show_read_and_unread_url = '{assemble route="notifications_popup_show_read_and_unread"}';
    var show_only_unread_url = '{assemble route="notifications_popup_show_only_unread"}';

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
          trigger.contextPopup('addButton', 'show_read_and_unread', "{lang}Show only unread notifications{/lang}", show_read_and_unread_url, App.Wireframe.Utils.imageUrl('icons/16x16/checkbox-checked.png', 'complete'), function () {
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
          trigger.contextPopup('addButton', 'show_only_unread', "{lang}Show only unread notifications{/lang}", show_only_unread_url, App.Wireframe.Utils.imageUrl('icons/16x16/checkbox-unchecked.png', 'complete'), function () {
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
</script>