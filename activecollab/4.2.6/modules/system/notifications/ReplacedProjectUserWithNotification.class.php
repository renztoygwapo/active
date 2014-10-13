<?php

  /**
   * Replacing project user with notification
   *
   * @package activeCollab.modules.system
   * @subpackage notifications
   */
  class ReplacedProjectUserWithNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang(":replaced_with will be replacing you on ':project' project", array(
        'replaced_with' => $this->getReplacedUser() instanceof User ? $this->getReplacedUser()->getDisplayName(true) : '',
        'project' => $this->getParent() instanceof Project ? $this->getParent()->getName() : '',
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * Return replaced user
     *
     * @return User
     */
    function getReplacedUser() {
      return DataObjectPool::get('User', $this->getAdditionalProperty('replaced_user_id'));
    } // getReplacedUser

    /**
     * Set replaced user
     *
     * @param User $user
     * @return ReplacedProjectUserWithNotification
     */
    function &setReplacedUser(User $user) {
      $this->setAdditionalProperty('replaced_user_id', $user->getId());

      return $this;
    } // setReplacedUser

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'replaced_with' => $this->getReplacedUser(),
      );
    } // getAdditionalTemplateVars

  }