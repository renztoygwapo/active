<?php

  /**
   * Email framework model definition
   *
   * @package angie.frameworks.email
   * @subpackage resources
   */
  class EmailFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct email framework model definition
     *
     * @param EmailFramework $parent
     */
    function __construct(EmailFramework $parent) {
      parent::__construct($parent);
     
      $this->addModel(DB::createTable('mailing_activity_logs')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('MailingActivityLog'),
        DBEnumColumn::create('direction', array('in', 'out'), 'out'), 
        DBUserColumn::create('from', false),
        DBUserColumn::create('to', false),  
        DBDateTimeColumn::create('created_on'), 
        DBAdditionalPropertiesColumn::create(), 
      ))->addIndices(array(
        DBIndex::create('created_on'), 
      )))->setTypeFromField('type');
      
      $this->addModel(DB::createTable('outgoing_messages')->addColumns(array(
        DBIdColumn::create(), 
        DBParentColumn::create(),
        DBStringColumn::create('decorator', 255, 'OutgoingMessageDecorator'),
        DBUserColumn::create('sender', false), 
        DBUserColumn::create('recipient', true), 
        DBIntegerColumn::create('recipient_id', 10, 0)->setUnsigned(true), 
        DBStringColumn::create('recipient_name', 100), 
        DBStringColumn::create('recipient_email', 150), 
        DBStringColumn::create('subject', 255), 
        DBTextColumn::create('body')->setSize(DBColumn::BIG), 
        DBStringColumn::create('context_id', 50),
        DBStringColumn::create('code', 25),
        DBStringColumn::create('mailing_method', 15, 'in_background'),  
        DBDateTimeColumn::create('created_on'), 
        DBIntegerColumn::create('send_retries', 5, 0)->setUnsigned(true), 
        DBStringColumn::create('last_send_error', 255), 
      ))->addIndices(array(
        DBIndex::create('created_on'), 
        DBIndex::create('recipient_email'), 
      )));
     
      $this->addModel(DB::createTable('incoming_mail_attachments')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('IncomingMailAttachment'), 
        DBIntegerColumn::create('mail_id', 10)->setUnsigned(true), 
        DBStringColumn::create('temporary_filename', 255), 
        DBStringColumn::create('original_filename', 255), 
        DBStringColumn::create('content_type', 255), 
        DBIntegerColumn::create('file_size', 10, 0)->setUnsigned(true), 
      )))->setTypeFromField('type');
      
      $this->addModel(DB::createTable('incoming_mail_filters')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('IncomingMailFilter'), 
        DBNameColumn::create(100), 
        DBTextColumn::create('description'), 
        DBTextColumn::create('subject'), 
        DBTextColumn::create('body'), 
        DBStringColumn::create('priority', 200), 
        DBStringColumn::create('attachments', 200), 
        DBTextColumn::create('sender')->setSize(DBColumn::BIG),
        DBTextColumn::create('to_email'),
        DBTextColumn::create('mailbox_id'), 
        DBStringColumn::create('action_name', 100), 
        DBTextColumn::create('action_parameters')->setSize(DBColumn::BIG), 
        DBIntegerColumn::create('position', 10, 0), 
        DBBoolColumn::create('is_enabled'), 
        DBBoolColumn::create('is_default'), 
      ))->addIndices(array(
        DBIndex::create('name'), 
      )))->setTypeFromField('type')->setOrderBy('position');
      
      $this->addModel(DB::createTable('incoming_mailboxes')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('IncomingMailbox'), 
        DBNameColumn::create(100), 
        DBStringColumn::create('email', 150), 
        DBStringColumn::create('mailbox', 100), 
        DBStringColumn::create('username', 50), 
        DBStringColumn::create('password', 50), 
        DBStringColumn::create('host', 255), 
        DBEnumColumn::create('server_type', array('POP3', 'IMAP'), 'POP3'), 
        DBIntegerColumn::create('port', 10)->setUnsigned(true), 
        DBEnumColumn::create('security', array('NONE', 'TLS', 'SSL'), 'NONE'), 
        DBIntegerColumn::create('last_status', 3, 0)->setUnsigned(true), 
        DBBoolColumn::create('is_enabled', false), 
        DBIntegerColumn::create('failure_attempts', 3, 0)->setSize(DBColumn::TINY), 
      )))->setTypeFromField('type')->setOrderBy('name');
      
      $this->addModel(DB::createTable('incoming_mails')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('IncomingMail'),
        DBIntegerColumn::create('incoming_mailbox_id', 10)->setUnsigned(true), 
        DBParentColumn::create(),
        DBBoolColumn::create('is_replay_to_notification'), 
        DBStringColumn::create('subject', 255), 
        DBTextColumn::create('body'), 
        DBTextColumn::create('to_email'), 
        DBTextColumn::create('cc_to'), 
        DBTextColumn::create('bcc_to'), 
        DBTextColumn::create('reply_to'), 
        DBStringColumn::create('priority', 200), 
        DBTextColumn::create('additional_data')->setSize(DBColumn::BIG), 
        DBTextColumn::create('headers')->setSize(DBColumn::BIG), 
        DBStringColumn::create('status',255), 
        DBActionOnByColumn::create('created'),
        DBAdditionalPropertiesColumn::create(),
      ))->addIndices(array(
        DBIndex::create('incoming_mailbox_id'), 
      )))->setTypeFromField('type')->setOrderBy('created_on DESC');
      
    } // __construct
    
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = '') {
      $this->addConfigOption('mailing', 'native');
      $this->addConfigOption('mailing_smtp_authenticate', false);
      $this->addConfigOption('mailing_smtp_host');
      $this->addConfigOption('mailing_smtp_username');
      $this->addConfigOption('mailing_smtp_password');
      $this->addConfigOption('mailing_smtp_port', 25);
      $this->addConfigOption('mailing_smtp_security', 25);
      $this->addConfigOption('mailing_method', 'instantly');
      $this->addConfigOption('mailing_method_override', true);
      $this->addConfigOption('mailing_native_options', '-oi -f %s');
      $this->addConfigOption('mailing_mark_as_bulk', true);
      
      $this->addConfigOption('notifications_from_force', true);
      $this->addConfigOption('notifications_from_name', '');
      $this->addConfigOption('notifications_from_email', '');
      
      $this->addConfigOption('conflict_notifications_delivery', 2);

      $this->addConfigOption('control_tower_check_reply_to_comment', true);
      $this->addConfigOption('control_tower_check_email_queue', true);
      $this->addConfigOption('control_tower_check_email_conflicts', true);
      
      $this->addConfigOption('disable_mailbox_on_successive_connection_failures', true);
      $this->addConfigOption('disable_mailbox_successive_connection_attempts', 3);
      $this->addConfigOption('disable_mailbox_notify_administrator', true);
      

      parent::loadInitialData($environment);
    } // loadInitialData
    
  }