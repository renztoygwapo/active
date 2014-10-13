<?php

  /**
   * Class description
   *
   * @package
   * @subpackage
   */
  class MorningPaperNotification extends Notification {

    /**
     * Return notification message in plain text
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('Morning Paper');
    } // getMessage

    /**
     * Return paper day
     *
     * @return DateValue
     */
    function getPaperDay() {
      return DateValue::makeFromString($this->getAdditionalProperty('day'));
    } // getPaperDay

    /**
     * Set paper day
     *
     * @param DateValue $day
     * @return MorningPaperNotification
     */
    function &setPaperDay(DateValue $day) {
      $this->setAdditionalProperty('day', $day);

      return $this;
    } // setPaperDay

    /**
     * Return previous business day
     *
     * @return DateValue
     */
    function getPreviousDay() {
      return DateValue::makeFromString($this->getAdditionalProperty('previous_day'));
    } // getPreviousDay

    /**
     * Set previous business day
     *
     * @param DateValue $day
     * @return MorningPaperNotification
     */
    function &setPreviousDay(DateValue $day) {
      $this->setAdditionalProperty('previous_day', $day);

      return $this;
    } // setPreviousDay

    /**
     * Return previous data data
     *
     * @return array|null
     */
    function getPrevousDayData() {
      return $this->getAdditionalProperty('prev_data');
    } // getPrevousDayData

    /**
     * Return today data
     *
     * @return array|null
     */
    function getTodayData() {
      return $this->getAdditionalProperty('today_data');
    } // getTodayData

    /**
     * Return true if this is the first time recipient is receiving morning paper
     *
     * @return boolean
     */
    function getIsFirstMorningPaper() {
      return $this->getAdditionalProperty('first_morning_paper');
    } // getIsFirstMorningPaper

    /**
     * Set paper data for a given user
     *
     * @param array|null $prev_data
     * @param array|null $today_data
     * @param boolean $first_morning_paper
     * @return MorningPaperNotification
     */
    function &setPaperData($prev_data, $today_data, $first_morning_paper) {
      $this->setAdditionalProperty('prev_data', $prev_data);
      $this->setAdditionalProperty('today_data', $today_data);
      $this->setAdditionalProperty('first_morning_paper', $first_morning_paper);

      return $this;
    } // setPaperData

    /**
     * Return true if this notification should be visible in a given notificaiton channel
     *
     * @param NotificationChannel $channel
     * @param IUser $recipient
     * @return bool
     */
    function isThisNotificationVisibleInChannel(NotificationChannel $channel, IUser $recipient) {
      if($channel instanceof WebInterfaceNotificationChannel) {
        return false; // Never show in web interface
      } // if

      if($channel instanceof EmailNotificationChannel) {
        return true; // Always send an email
      } // if

      return parent::isThisNotificationVisibleInChannel($channel, $recipient);
    } // isThisNotificationVisibleInChannel

    /**
     * Return subscription code
     *
     * @param IUser $user
     * @return string
     */
    function getSubscriptionCode(IUser $user) {
      if($user instanceof User) {
        return MorningPaper::getSubscriptionCode($user);
      } else {
        return null;
      } // if
    } // getSubscriptionCode

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      if($channel instanceof EmailNotificationChannel) {
        return array(
          'paper_day' => $this->getPaperDay(),
          'previous_day' => DateValue::makeFromString($this->getPreviousDay()),
          'prev_data' => $this->getPrevousDayData(),
          'today_data' => $this->getTodayData(),
          'first_morning_paper' => $this->getIsFirstMorningPaper(),
        );
      } // if
    } // getAdditionalTemplateVars

  }