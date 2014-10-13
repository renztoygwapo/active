<?php

  /**
   * Web interface notification channel
   *
   * @package angie.frameworks.notifications
   * @subpackage models
   */
  class WebInterfaceNotificationChannel extends NotificationChannel {

    // Short channel name
    const CHANNEL_NAME = 'web_interface';

    /**
     * Return channel short name
     *
     * @return string
     */
    function getShortName() {
      return WebInterfaceNotificationChannel::CHANNEL_NAME;
    } // getShortName

    /**
     * Return verbose name of the channel
     *
     * @return string
     */
    function getVerboseName() {
      return lang('Web Interface Notifications');
    } // getVerboseName

    /**
     * Returns true if this channel is enabled by default
     *
     * @return boolean
     */
    function isEnabledByDefault() {
      return true; // Always enabled
    } // isEnabledByDefault

    /**
     * Returns true if this channel is enabled for this user
     *
     * @param User $user
     * @return boolean
     */
    function isEnabledFor(User $user) {
      return true; // Always enabled
    } // isEnabledFor

    // ---------------------------------------------------
    //  Open / Close
    // ---------------------------------------------------

    /**
     * Open channel for sending
     */
    function open() {
      DB::beginWork('Saving notifications for recipients @ ' . __CLASS__);
      parent::open();
    } // open

    /**
     * Close channel after notifications have been sent
     */
    function close($sending_interupted = false) {
      if($sending_interupted) {
        DB::rollback('Failed to save notifications for recipients @ ' . __CLASS__);
      } else {
        DB::commit('Notifications saved for recipients @ ' . __CLASS__);
      } // if

      parent::close($sending_interupted);
    } // close

    /**
     * Send notification via this channel
     *
     * @param Notification $notification
     * @param boolean $skip_sending_queue
     * @param IUser $recipient
     */
    function send(Notification &$notification, IUser $recipient, $skip_sending_queue = false) {
      if($recipient instanceof User) {
        $notification->addRecipient($recipient);
      } // if
    } // send

  }