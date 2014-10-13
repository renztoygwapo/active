<?php

  /**
   * New file notification
   *
   * @package activeCollab.modules.files
   * @subpackage notifications
   */
  class NewFileNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("File ':object_name' has been uploaded", array(
        'object_name' => $this->getParent() instanceof File ? $this->getParent()->getName() : '',
      ), true, $user->getLanguage());
    } // getMessage

  }