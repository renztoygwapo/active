<?php

  /**
   * New Commits notification
   *
   * @package activeCollab.modules.source
   * @subpackage notifications
   */
  class NewCommitsNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("':repository' Repository has been Updated", array(
        'repository' => $this->getRepository() instanceof ProjectSourceRepository ? $this->getRepository()->getName() : '',
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * Return Repository
     *
     * @return mixed
     */
    function getRepository() {
      return DataObjectPool::get('ProjectSourceRepository', $this->getAdditionalProperty('repository_id'));
    } //getRepository

    /**
     * Sets additional property detailed_notifications
     *
     * @param boolean $detailed_notifications
     * @return NewCommitsNotification
     */
    function setDetailedNotifications($detailed_notifications) {
      $this->setAdditionalProperty('detailed_notifications', $detailed_notifications);

      return $this;
    } // setDetailedNotifications

    /**
     * Sets additional property last_update_commits_count
     *
     * @param integer $last_update_commits_count
     * @return NewCommitsNotification
     */
    function setLastUpdateCommitsCount($last_update_commits_count) {
      $this->setAdditionalProperty('last_update_commits_count', $last_update_commits_count);

      return $this;
    } // setLastUpdateCommitsCount

    /**
     * Sets additional property active_branch
     *
     * @param string $active_branch
     * @return NewCommitsNotification
     */
    function setActiveBranch($active_branch) {
      $this->setAdditionalProperty('active_branch', $active_branch);

      return $this;
    } // setActiveBranch

    /**
     * Set repository
     *
     * @param ProjectSourceRepository $repository
     * @return NewCommitsNotification
     */
    function &setRepository(ProjectSourceRepository $repository) {
      $this->setAdditionalProperty('repository_id', $repository->getId());

      return $this;
    } // setRepository

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'detailed_notifications'    => $this->getAdditionalProperty('detailed_notifications'),
        'last_update_commits_count' => $this->getAdditionalProperty('last_update_commits_count'),
        'active_branch'             => $this->getAdditionalProperty('active_branch')
      );
    } // getAdditionalTemplateVars

  }