<?php

  /**
   * mailboxmanager initialization file
   * 
   * @package angie.library.mailboxmanager
   */
  
  define('MAILBOX_MANAGER_LIB_PATH', ANGIE_PATH . '/classes/mailboxmanager');
  
  /**
   * Constants
   */ 
  define('CAN_USE_MAILBOX_MANAGER', extension_loaded('php_imap'));
  
  defined('FAIL_SAFE_IMAP_FUNCTIONS') or define('FAIL_SAFE_IMAP_FUNCTIONS', false);
  
  if (!function_exists('imap_savebody') || FAIL_SAFE_IMAP_FUNCTIONS) {
    define('MM_CAN_DOWNLOAD_LARGE_ATTACHMENTS', false);  
  } else {
    define('MM_CAN_DOWNLOAD_LARGE_ATTACHMENTS', true);
  } // if
  
  // server types
  const MM_SERVER_TYPE_POP3 = 'POP3';
  const MM_SERVER_TYPE_IMAP = 'IMAP';
  
  // server security
  const MM_SECURITY_NONE = 'NONE';
  const MM_SECURITY_TLS = 'TLS';
  const MM_SECURITY_SSL = 'SSL';
  
  const MM_DEFAULT_MAILBOX = 'INBOX';
  
  // functions
  require_once(MAILBOX_MANAGER_LIB_PATH . '/functions.php');
  
  AngieApplication::setForAutoload(array(
    'MailboxManagerEmail' => MAILBOX_MANAGER_LIB_PATH . '/MailboxManagerEmail.class.php', 
    'PHPImapMailboxManager' => MAILBOX_MANAGER_LIB_PATH . '/PHPImapMailboxManager.class.php',
  ));