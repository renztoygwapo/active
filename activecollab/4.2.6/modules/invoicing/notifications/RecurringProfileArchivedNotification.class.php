<?php

  /**
   * Recurring profile archived notification
   *
   * @package activeCollab.modules.invoicing
   * @subpackage notification
   */
  class RecurringProfileArchivedNotification extends RecurringProfileNotification {

    /**
     * Return notification mesasge
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('":name" Profile has been Archived', array(
        'name' => $this->getProfile() instanceof RecurringProfile ? $this->getProfile()->getName() : '',
      ), true, $user->getLanguage());
    } // getMessage

  }