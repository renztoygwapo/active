<?php

  /**
   * Email framework definition
   *
   * @package angie.frameworks.email
   */
  class EmailFramework extends AngieFramework {
    
    /**
     * Short name of the framework
     *
     * @var string
     */
    protected $name = 'email';
    
    /**
     * Define email framework routes
     */
    function defineRoutes() {
      Router::map('email_admin', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing', array('controller' => 'email_admin', 'action' => 'index', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
    	
      Router::map('email_admin_logs', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/log', array('controller' => 'email_admin', 'action' => 'log', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      
      Router::map('email_admin_log_entry', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/log/:log_entry_id', array('controller' => 'email_admin', 'action' => 'log_entry', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('email_admin_log_entry_edit', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/log/:log_entry_id/edit', array('controller' => 'email_admin', 'action' => 'log_entry_edit', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('email_admin_log_entry_delete', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/log/:log_entry_id/delete', array('controller' => 'email_admin', 'action' => 'log_entry_delete', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));

      Router::map('email_admin_reply_to_comment', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/reply-to-comment', array('controller' => 'email_to_comment', 'action' => 'index', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('email_admin_reply_to_comment_change_from_address', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/reply-to-comment/change-from-address', array('controller' => 'email_to_comment', 'action' => 'change_from_address', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('email_admin_reply_to_comment_install_imap', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/reply-to-comment/install-imap', array('controller' => 'email_to_comment', 'action' => 'install_imap', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('email_admin_reply_to_comment_update_mailbox', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/reply-to-comment/update-mailbox', array('controller' => 'email_to_comment', 'action' => 'update_mailbox', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));

      Router::map('outgoing_email_admin_settings', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/outgoing/settings', array('controller' => 'outgoing_email_admin', 'action' => 'settings', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('outgoing_email_admin_test_smtp_connection', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/outgoing/test-smtp-connection', array('controller' => 'outgoing_email_admin', 'action' => 'test_smtp_connection', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('outgoing_email_admin_send_test_message', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/outgoing/send-test-message', array('controller' => 'outgoing_email_admin', 'action' => 'send_test_message', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      
      // Outgoing messages admin
      Router::map('outgoing_messages_admin', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/outgoing/messages', array('controller' => 'outgoing_messages_admin', 'action' => 'index', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      
      Router::map('outgoing_messages_admin_message', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/outgoing/messages/:message_id', array('controller' => 'outgoing_messages_admin', 'action' => 'view', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('message_id' => '\d+'));
      Router::map('outgoing_messages_admin_message_raw_body', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/outgoing/messages/:message_id/raw-body', array('controller' => 'outgoing_messages_admin', 'action' => 'view_raw_body', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('message_id' => '\d+'));
      Router::map('outgoing_messages_admin_message_send', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/outgoing/messages/:message_id/send', array('controller' => 'outgoing_messages_admin', 'action' => 'send', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('message_id' => '\d+'));
      Router::map('outgoing_messages_admin_message_delete', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/outgoing/messages/:message_id/delete', array('controller' => 'outgoing_messages_admin', 'action' => 'delete', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('message_id' => '\d+'));
      
      AngieApplication::getModule('attachments')->defineAttachmentsRoutesFor('outgoing_messages_admin_message', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/outgoing/messages/:message_id', 'outgoing_messages_admin', EMAIL_FRAMEWORK, array('message_id' => '\d+'));
      
      // Incoming mailboxes
      Router::map('incoming_email_admin_mailboxes', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mailboxes', array('controller' => 'incoming_mailboxes_admin', 'action' => 'index', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('incoming_email_admin_mailbox_test_connection', EMAIL_FRAMEWORK_ROUTE_BASE . 'mailing/incoming/test-mailbox-connection', array('controller' => 'incoming_mailboxes_admin', 'action' => 'test_mailbox_connection', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('incoming_email_admin_mailbox_add', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mailboxes/add', array('controller' => 'incoming_mailboxes_admin', 'action' => 'add', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('incoming_email_admin_mailbox_enable', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mailboxes/:mailbox_id/enable', array('controller' => 'incoming_mailboxes_admin', 'action' => 'enable', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('incoming_email_admin_mailbox_disable', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mailboxes/:mailbox_id/disable', array('controller' => 'incoming_mailboxes_admin', 'action' => 'disable', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('incoming_email_admin_mailbox', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mailboxes/:mailbox_id', array('controller' => 'incoming_mailboxes_admin', 'action' => 'view', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('mailbox_id' => '\d+'));
      Router::map('incoming_email_admin_mailbox_edit', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mailboxes/:mailbox_id/edit', array('controller' => 'incoming_mailboxes_admin', 'action' => 'edit', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('mailbox_id' => '\d+'));
      Router::map('incoming_email_admin_mailbox_delete', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mailboxes/:mailbox_id/delete', array('controller' => 'incoming_mailboxes_admin', 'action' => 'delete', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('mailbox_id' => '\d+'));
      Router::map('incoming_email_admin_mailbox_list_messages', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mailboxes/:mailbox_id/list', array('controller' => 'incoming_mailboxes_admin', 'action' => 'list_messages', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('mailbox_id' => '\d+'));
      Router::map('incoming_email_admin_mailbox_delete_messages', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mailboxes/:mailbox_id/delete_messages', array('controller' => 'incoming_mailboxes_admin', 'action' => 'delete_message_from_server', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('mailbox_id' => '\d+'));
      
      Router::map('incoming_email_admin_change_settings', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/settings', array('controller' => 'incoming_mail_admin', 'action' => 'settings', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      
      Router::map('incoming_email_admin_mailbox_show_more_results', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mailboxes/:mailbox_id/show/more/results', array('controller' => 'incoming_mailboxes_admin', 'action' => 'show_more_results', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('mailbox_id' => '\d+'));
      
      // Incoming filters
      Router::map('incoming_email_admin_filters', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/filters', array('controller' => 'incoming_mail_filter_admin', 'action' => 'index', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('incoming_email_admin_filter_add', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/filter/add', array('controller' => 'incoming_mail_filter_admin', 'action' => 'add', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('incoming_email_admin_filter_enable', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/filter/:filter_id/enable', array('controller' => 'incoming_mail_filter_admin', 'action' => 'enable', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('incoming_email_admin_filter_disable', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/filter/:filter_id/disable', array('controller' => 'incoming_mail_filter_admin', 'action' => 'disable', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('incoming_email_admin_filter_view', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/filter/:filter_id/view', array('controller' => 'incoming_mail_filter_admin', 'action' => 'view', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('filter_id' => '\d+'));
      Router::map('incoming_email_admin_filter_edit', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/filter/:filter_id/edit', array('controller' => 'incoming_mail_filter_admin', 'action' => 'edit', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('filter_id' => '\d+'));
      Router::map('incoming_email_admin_filter_delete', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/filter/:filter_id/delete', array('controller' => 'incoming_mail_filter_admin', 'action' => 'delete', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('filter_id' => '\d+'));
      
      Router::map('incoming_email_filter_reorder', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/filter/reorder', array('controller' => 'incoming_mail_filter_admin', 'action' => 'reorder_position', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      
      Router::map('incoming_mail', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mail', array('controller' => 'incoming_mail_conflict', 'action' => 'index', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      
      Router::map('incoming_email_admin_conflict', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mail/conflict', array('controller' => 'incoming_mail_conflict', 'action' => 'index', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('incoming_email_delete_mail', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/:incoming_mail_id/delete', array('controller' => 'incoming_mail_conflict', 'action' => 'delete', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('incoming_mail_id' => '\d+'));
      Router::map('incoming_email_import_mail', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/:incoming_mail_id/import', array('controller' => 'incoming_mail_conflict', 'action' => 'conflict', 'module' => EMAIL_FRAMEWORK_INJECT_INTO), array('incoming_mail_id' => '\d+'));
      Router::map('incoming_mail_mass_conflict_resolution', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/mass-conflict-resolution', array('controller' => 'incoming_mail_conflict', 'action' => 'mass_conflict_resolution', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('incoming_mail_remove_all_conflicts', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/remove/all', array('controller' => 'incoming_mail_conflict', 'action' => 'remove_all_conflicts', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
      Router::map('incoming_mail_remove_selected_conflicts', EMAIL_FRAMEWORK_ROUTE_BASE . '/mailing/incoming/remove/selected', array('controller' => 'incoming_mail_conflict', 'action' => 'remove_selected_conflicts', 'module' => EMAIL_FRAMEWORK_INJECT_INTO));
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
      
      EventsManager::listen('on_incoming_mail_actions', 'on_incoming_mail_actions');
      EventsManager::listen('on_load_control_tower', 'on_load_control_tower');
      EventsManager::listen('on_load_control_tower_badge', 'on_load_control_tower_badge');
      EventsManager::listen('on_load_control_tower_settings', 'on_load_control_tower_settings');
      EventsManager::listen('on_used_disk_space', 'on_used_disk_space');
      EventsManager::listen('on_notification_channels', 'on_notification_channels');

      EventsManager::listen('on_frequently', 'on_frequently');
      EventsManager::listen('on_hourly', 'on_hourly');
      EventsManager::listen('on_daily', 'on_daily');
    } // defineHandlers
    
  }