<?php

  /**
   * Framework level new announcement notification
   *
   * @package angie.frameworks.announcements
   * @subpackage notifications
   */
  abstract class FwNewAnnouncementNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('New Announcement Notification', null, true, $user->getLanguage());
    } // getMessage

    /**
     * Return notification subject
     *
     * @return string
     */
    function getSubject() {
      return $this->getAdditionalProperty('subject');
    } // getSubject

    /**
     * Set notification subject
     *
     * @param string $value
     * @return NewAnnouncementNotification
     */
    function &setSubject($value) {
      $this->setAdditionalProperty('subject', $value);

      return $this;
    } // setSubject

    /**
     * Return notification body
     *
     * @return string
     */
    function getBody() {
      return $this->getAdditionalProperty('body');
    } // getBody

    /**
     * Set notification body
     *
     * @param string $value
     * @return NewAnnouncementNotification
     */
    function &setBody($value) {
      $this->setAdditionalProperty('body', $value);

      return $this;
    } // setBody

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'subject' => $this->getSubject(),
        'body' => $this->getBody()
      );
    } // getAdditionalTemplateVars

  }