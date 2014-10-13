<?php

  /**
   * New project request for representative notification
   *
   * @package activeCollab.modules.system
   * @subpackage notifications
   */
  class NewProjectRequestForRepresentativesNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang('New project request has been submitted', null, true, $user->getLanguage());
    } // getMessage

    /**
     * Return message for web interface
     *
     * @param IUser $user
     * @return string
     */
    function getMessageForWebInterface(IUser $user) {
      if($this->getParent() instanceof ProjectRequest) {
        return lang(':created_by (from :company_name) has requested a ":name" project', array(
          'created_by' => $this->getParent()->getCreatedByName(),
          'company_name' => $this->getParent()->getCompanyName(),
          'name' => $this->getParent()->getName(),
      ));
      } else {
        return parent::getMessage($user);
      } // if
    } // getMessageForWebInterface

  }