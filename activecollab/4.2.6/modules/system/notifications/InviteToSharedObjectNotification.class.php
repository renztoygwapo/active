<?php

  /**
   * Invite to shared object notification
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class InviteToSharedObjectNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string|void
     */
    function getMessage(IUser $user) {
      $parent = $this->getParent();

      return lang("You have been Invited to Collaborate on ':name' :type", array(
        'name' => $parent instanceof ApplicationObject ? $parent->getName() : '',
        'type' => $parent instanceof ApplicationObject ? $parent->getVerboseType(true, $user->getLanguage()) : '',
      ), true, $user->getLanguage());
    } // getMessage

  }