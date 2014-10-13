<?php

  /**
   * Notify new assignee notification
   *
   * @package angie.frameworks.assignee
   * @subpackage notifications
   */
  abstract class FwNotifyNewAssigneeNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      $parent = $this->getParent();

      return lang("You are now Responsible for ':name' :type", array(
        'name' => $parent instanceof ApplicationObject ? $parent->getName() : '',
        'type' => $parent instanceof ApplicationObject ? $parent->getVerboseType(true, $user->getLanguage()) : '',
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * Get is reassigned
     *
     * @return boolean
     */
    function getIsReassigned() {
      return $this->getAdditionalProperty('is_reassigned');
    } // getIsReassigned

    /**
     * Set is reassigned
     *
     * @param boolean $value
     * @return NotifyNewAssigneeNotification
     */
    function &setIsReassigned($value) {
      $this->setAdditionalProperty('is_reassigned', (boolean) $value);

      return $this;
    } // setIsReassigned

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'title_lang' => $this->getIsReassigned() ? ':type Reassigned' : ':type Assigned',
      );
    } // getAdditionalTemplateVars

  }