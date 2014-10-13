<?php

  /**
   * Base recuring profile notification
   *
   * @package activeCollab.modules.invoicing
   * @subpackage notifications
   */
  abstract class RecurringProfileNotification extends Notification {

    /**
     * Return parent recurring profile
     *
     * @return RecurringProfile
     */
    function getProfile() {
      return DataObjectPool::get('RecurringProfile', $this->getAdditionalProperty('profile_id'));
    } // getProfile

    /**
     * Set parent profile
     *
     * @param RecurringProfile $profile
     * @return RecurringProfileNotification
     */
    function &setProfile(RecurringProfile $profile) {
      $this->setAdditionalProperty('profile_id', $profile->getId());

      return $this;
    } // setProfile

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'profile' => $this->getProfile(),
      );
    } // getAdditionalTemplateVars

  }