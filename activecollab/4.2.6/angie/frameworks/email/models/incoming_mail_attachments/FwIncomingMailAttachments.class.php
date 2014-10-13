<?php

  /**
   * Framework level incoming mail attachments manager
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  abstract class FwIncomingMailAttachments extends BaseIncomingMailAttachments {
    
    /**
     * Delete incoming mail attachements by parent
     * 
     */
    static function deleteByMailIds($mail_ids) {
      
      $attachments = self::find(array(
        "conditions" => array('mail_id IN (?)', $mail_ids),
      ));
      
      if(is_foreachable($attachments)) {
        foreach ($attachments as $attachment) {
          $file = INCOMING_MAIL_ATTACHMENTS_FOLDER . '/' . $attachment->getTemporaryFilename();
          unset($file);
          $attachment->delete();
        }//foreach
      }//if
      return true;
    }//deleteByParent
    
    /**
     * Delete all incoming mail attachements
     * 
     */
    static function deleteAll() {
      
      $attachments = self::find();
      if(is_foreachable($attachments)) {
        foreach ($attachments as $attachment) {
          $file = INCOMING_MAIL_ATTACHMENTS_FOLDER . '/' . $attachment->getTemporaryFilename();
          unset($file);
          $attachment->delete();
        }//foreach
      }//if
      return true;
    }//deleteByParent
    
  }
