<?php

  /**
   * New task from public form notification that is sent to task author
   *
   * @package activeCollab.modules.tasks
   * @subpackage notifications
   */
  class NewTaskFromFormForAuthorNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("Thank You for Your Request", array(
        'name' => $this->getParent() instanceof Task ? $this->getParent()->getName() : ''
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * This notification should not be displayed in web interface
     *
     * @param NotificationChannel $channel
     * @param IUser $recipient
     * @return bool
     */
    function isThisNotificationVisibleInChannel(NotificationChannel $channel, IUser $recipient) {
      if($channel instanceof EmailNotificationChannel) {
        return true; // Always deliver notifications to author via email
      } elseif($channel instanceof WebInterfaceNotificationChannel) {
        return false; // Never deliver notifications to author in web interface
      } // if

      return parent::isThisNotificationVisibleInChannel($channel, $recipient);
    } // isThisNotificationVisibleInChannel

  }