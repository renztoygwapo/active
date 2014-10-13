<?php

  /**
   * Attachment framework definition
   *
   * @package angie.frameworks.attachments
   */
  
  const ATTACHMENTS_FRAMEWORK = 'attachments';
  const ATTACHMENTS_FRAMEWORK_PATH = __DIR__;
  
  defined('ATTACHMENTS_FRAMEWORK_INJECT_INTO') or define('ATTACHMENTS_FRAMEWORK_INJECT_INTO', 'system');
  
  AngieApplication::setForAutoload(array(
    'IAttachments' => ATTACHMENTS_FRAMEWORK_PATH . '/models/IAttachments.class.php', 
    'IAttachmentsImplementation' => ATTACHMENTS_FRAMEWORK_PATH . '/models/IAttachmentsImplementation.class.php', 
    
    'FwAttachment' => ATTACHMENTS_FRAMEWORK_PATH . '/models/attachments/FwAttachment.class.php', 
    'FwAttachments' => ATTACHMENTS_FRAMEWORK_PATH . '/models/attachments/FwAttachments.class.php', 
    
    'IAttachmentDownloadImplementation' => ATTACHMENTS_FRAMEWORK_PATH . '/models/IAttachmentDownloadImplementation.class.php', 
    
    'UploadError' => ATTACHMENTS_FRAMEWORK_PATH . '/models/errors/UploadError.class.php', 
  ));