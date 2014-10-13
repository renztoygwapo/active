<?php

  /**
   * Test notification class
   *
   * @package angie.frameworks.notifications
   * @subpackage models
   */
  class TestNotification extends Notification {

    /**
     * Return message for a given user
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return 'This is a test notification';
    } // getMessage

    /**
     * Returns true if $user was mentioned in this notification
     *
     * @param IUser $user
     * @return bool
     */
    function isUserMentioned($user) {
      return $user->getEmail() == 'email@a51dev.com' ? true : parent::isUserMentioned($user);
    } // isUserMentioned

  }