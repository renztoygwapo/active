<?php

  /**
   * Email framework intialization file
   *
   * @package angie.frameworks.email
   */

  const EMAIL_FRAMEWORK = 'email';
  const EMAIL_FRAMEWORK_PATH = __DIR__;
  
  // ---------------------------------------------------
  //  Overridable Settings
  // ---------------------------------------------------

  defined('TEST_SMTP_BY_SENDING_EMAIL_TO') or define('TEST_SMTP_BY_SENDING_EMAIL_TO', false); // Set to a particular address to send test email to that address

  $memory_limit = ini_get('memory_limit');
  if (!$memory_limit) {
    $attachment_size = 104857600; // 100MB
  } else {
    $attachment_size = round(php_size_to_bytes($memory_limit) * 0.3);
    if ($attachment_size < 512000) $attachment_size = 512000;
  } // if

  defined('FAIL_SAFE_IMAP_FUNCTIONS') or define('FAIL_SAFE_IMAP_FUNCTIONS', false);
  defined('FAIL_SAFE_IMAP_ATTACHMENT_SIZE_MAX') or define('FAIL_SAFE_IMAP_ATTACHMENT_SIZE_MAX', $attachment_size);
  
  defined('OBJECT_SOURCE_EMAIL') or define('OBJECT_SOURCE_EMAIL', 'email');
  
  defined('EMAIL_FRAMEWORK_INJECT_INTO') or define('EMAIL_FRAMEWORK_INJECT_INTO', 'system');
  defined('EMAIL_FRAMEWORK_ROUTE_BASE') or define('EMAIL_FRAMEWORK_ROUTE_BASE', 'admin');
  
  defined('EMAIL_ENCODING') or define('EMAIL_ENCODING', '8bit');
  defined('EMAIL_CHARSET') or define('EMAIL_CHARSET', 'utf-8');

  defined('MAILING_QUEUE_MAX_PER_REQUEST') or define('MAILING_QUEUE_MAX_PER_REQUEST', 50); // Max number of messages that can be sent per request
  defined('MAILING_QUEUE_MAX_SEND_RETRIES') or define('MAILING_QUEUE_MAX_SEND_RETRIES', 5); // Max number of retries for outgoing message before it gets ignored
  defined('EMAIL_SPLITTER') or define('EMAIL_SPLITTER', '-- REPLY ABOVE THIS LINE --'); // lang('-- REPLY ABOVE THIS LINE --')

  defined('INCOMING_MAIL_ATTACHMENTS_FOLDER') or define('INCOMING_MAIL_ATTACHMENTS_FOLDER', WORK_PATH);

  defined('INCOMING_MAIL_DEFAULT_MAILBOX') or define('INCOMING_MAIL_DEFAULT_MAILBOX', 'INBOX');

  defined('INCOMING_MAIL_INVALID_EMAIL_ADDRESS') or define('INCOMING_MAIL_INVALID_EMAIL_ADDRESS', 'invalid.email.address@unknown.com');
  
  // ---------------------------------------------------
  //  Constants used by framework
  // ---------------------------------------------------
  
  define('MAILING_DISABLED', 'disabled');
  define('MAILING_SILENT', 'silent');
  define('MAILING_NATIVE', 'native');
  define('MAILING_SMTP', 'smtp');
  
  define('MAILING_METHOD_INSTANTLY', 'instantly');
  define('MAILING_METHOD_IN_BACKGROUND', 'in_background');
  
  AngieApplication::setForAutoload(array(
    'AngieMailerDelegate' => EMAIL_FRAMEWORK_PATH . '/models/mailer/AngieMailerDelegate.class.php',
    'AngieIncomingMailDelegate' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mail/AngieIncomingMailDelegate.class.php',

    'ApplicationMailerAdapter' => EMAIL_FRAMEWORK_PATH . '/models/mailer_adapters/ApplicationMailerAdapter.class.php',  
    'SilentMailerAdapter' => EMAIL_FRAMEWORK_PATH . '/models/mailer_adapters/SilentMailerAdapter.class.php',  
    'DisabledMailerAdapter' => EMAIL_FRAMEWORK_PATH . '/models/mailer_adapters/DisabledMailerAdapter.class.php',  
      
    'SwiftMailerAdapter' => EMAIL_FRAMEWORK_PATH . '/models/mailer_adapters/SwiftMailerAdapter.class.php',
    'NativeSwiftMailerAdapter' => EMAIL_FRAMEWORK_PATH . '/models/mailer_adapters/NativeSwiftMailerAdapter.class.php',
    'SmtpSwiftMailerAdapter' => EMAIL_FRAMEWORK_PATH . '/models/mailer_adapters/SmtpSwiftMailerAdapter.class.php',
  
    // Outgoing messages
    'FwOutgoingMessage' => EMAIL_FRAMEWORK_PATH . '/models/outgoing_messages/FwOutgoingMessage.class.php',
    'FwOutgoingMessages' => EMAIL_FRAMEWORK_PATH . '/models/outgoing_messages/FwOutgoingMessages.class.php',

    'FwOutgoingMessageDecorator' => EMAIL_FRAMEWORK_PATH . '/models/FwOutgoingMessageDecorator.class.php',
  
    'IOutgoingMessageAttachmentsImplementation' => EMAIL_FRAMEWORK_PATH . '/models/IOutgoingMessageAttachmentsImplementation.class.php',

    // Notification channel
    'EmailNotificationChannel' => EMAIL_FRAMEWORK_PATH . '/models/EmailNotificationChannel.class.php',
    
    // Activity log
    'FwMailingActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/FwMailingActivityLog.class.php', 
    'FwMailingActivityLogs' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/FwMailingActivityLogs.class.php',
    
    'FwIncomingMailbox' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mailboxes/FwIncomingMailbox.class.php', 
    'FwIncomingMailboxes' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mailboxes/FwIncomingMailboxes.class.php', 
    'FwIncomingMail' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mails/FwIncomingMail.class.php', 
    'FwIncomingMails' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mails/FwIncomingMails.class.php', 
    'FwIncomingMailAttachment' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_attachments/FwIncomingMailAttachment.class.php', 
    'FwIncomingMailAttachments' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_attachments/FwIncomingMailAttachments.class.php',
    'FwIncomingMailActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_activity_logs/FwIncomingMailActivityLog.class.php', 
    'FwIncomingMailActivityLogs' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_activity_logs/FwIncomingMailActivityLogs.class.php',  
    'FwIncomingMailFilter' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_filters/FwIncomingMailFilter.class.php',  
    'FwIncomingMailFilters' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_filters/FwIncomingMailFilters.class.php',

    'IncomingMailAction' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_actions/IncomingMailAction.class.php',
    'IncomingMailIgnoreAction' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_actions/IncomingMailIgnoreAction.class.php',
    'IncomingMailMoveToTrashAction' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_actions/IncomingMailMoveToTrashAction.class.php',

    // Mailing activity logs
    'OutgoingMailingActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/outgoing/OutgoingMailingActivityLog.class.php',
    'MessageSentActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/outgoing/MessageSentActivityLog.class.php',
  	'SmtpFailedToConnectActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/SmtpFailedToConnectActivityLog.class.php', 
    'SmtpFailedToSendActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/SmtpFailedToSendActivityLog.class.php',
  
    'IncomingMailingActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/incoming/IncomingMailingActivityLog.class.php',
    'IncomingMessageActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/incoming/IncomingMessageActivityLog.class.php',
    'IncomingMessageImportErrorActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/incoming/IncomingMessageImportErrorActivityLog.class.php',
    'IncomingMessageReceivedActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/incoming/IncomingMessageReceivedActivityLog.class.php',
    'IncomingMessageServerErrorActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/incoming/IncomingMessageServerErrorActivityLog.class.php',
  	'IncomingMessageAutoRespondActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/incoming/IncomingMessageAutoRespondActivityLog.class.php',
  	'IncomingMessageDeleteActivityLog' => EMAIL_FRAMEWORK_PATH . '/models/mailing_activity_logs/incoming/IncomingMessageDeleteActivityLog.class.php',
  	'IncomingMailBodyProcessor' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_body_processors/IncomingMailBodyProcessor.class.php',

    // Notifications
  	'FwNotifyEmailSenderNotification' => EMAIL_FRAMEWORK_PATH . '/notifications/FwNotifyEmailSenderNotification.class.php',
  	'ConflictNotifyOnDailyNotification' => EMAIL_FRAMEWORK_PATH . '/notifications/ConflictNotifyOnDailyNotification.class.php',
  	'MailboxDisabledNotification' => EMAIL_FRAMEWORK_PATH . '/notifications/MailboxDisabledNotification.class.php',
  	'MailboxNotCheckedNotification' => EMAIL_FRAMEWORK_PATH . '/notifications/MailboxNotCheckedNotification.class.php',
  	'ConflictNotifyInstantlyNotification' => EMAIL_FRAMEWORK_PATH . '/notifications/ConflictNotifyInstantlyNotification.class.php',

    //interceptors
  	'IncomingMailInterceptor' => EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_interceptors/IncomingMailInterceptor.class.php',
  ));