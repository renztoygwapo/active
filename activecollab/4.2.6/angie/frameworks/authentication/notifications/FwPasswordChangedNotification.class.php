<?php

  /**
   * Password changed notification
   *
   * @package angie.frameworks.authentication
   * @subpackage notifications
   */
  abstract class FwPasswordChangedNotification extends Notification {

    /**
     * Return notification message in plain text
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('Your Password has been Changed');
    } // getMessage

    /**
     * Return new pessword value
     *
     * @return string
     */
    function getNewPassword() {
      return $this->getAdditionalProperty('new_password');
    } // getNewPassword

    /**
     * Set new pessword value
     *
     * @param string $value
     * @return PasswordChangedNotification
     */
    function &setNewPassword($value) {
      $this->setAdditionalProperty('new_password', $value);

      return $this;
    } // setNewPassword

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'password' => $this->getNewPassword(),
      );
    } // getAdditionalTemplateVars

  }