<?php

  /**
   * Framework level incoming mail attachment
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  abstract class FwIncomingMailAttachment extends BaseIncomingMailAttachment {
    
    /**
     * Delete incoming mail attachment
     *
     * @return boolean
     */
    function delete() {
      parent::delete();
      @unlink(INCOMING_MAIL_ATTACHMENTS_FOLDER . '/' . $this->getTemporaryFilename());
    } // delete
    
  }
