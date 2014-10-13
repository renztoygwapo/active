<?php

  /**
   * Replacing project user notification
   *
   * @package activeCollab.modules.system
   * @subpackage notifications
   */
  class ReplacingProjectUserNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("You will be replacing :replacing_user on ':project' project", array(
        'replacing_user' => $this->getReplacingUser() instanceof User ? $this->getReplacingUser()->getDisplayName(true) : '',
        'project' => $this->getParent() instanceof Project ? $this->getParent()->getName() : '',
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * Return replacing user
     *
     * @return User
     */
    function getReplacingUser() {
      return DataObjectPool::get('User', $this->getAdditionalProperty('replacing_user_id'));
    } // getReplacingUser

    /**
     * Set replacing user
     *
     * @param User $user
     * @return ReplacingProjectUserNotification
     */
    function &setReplacingUser(User $user) {
      $this->setAdditionalProperty('replacing_user_id', $user->getId());

      return $this;
    } // setReplacingUser

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'replacing_user' => $this->getReplacingUser(),
        'project_assignments_url' => $this->getParent() instanceof Project ? Router::assemble('project_user_tasks', array('project_slug' => $this->getParent()->getSlug())) : '#',
      );
    } // getAdditionalTemplateVars

  }