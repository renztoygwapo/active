<?php

  /**
   * ProjectSourceRepositories class
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class ProjectSourceRepositories extends ProjectObjects {
    
    /**
     * Returns true if $user can access source section of $project
     *
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAccess(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAccess($user, $project, 'repository', ($check_tab ? 'source' : null));
    } // canAccess
    
    /**
     * Returns true if $user can add a new repository to $project
     *
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAdd(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAdd($user, $project, 'repository', ($check_tab ? 'source' : null));
    } // canAdd
    
    /**
     * Returns true if $user can manage repository connections in $project
     * 
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canManage(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canManage($user, $project, 'repository', ($check_tab ? 'source' : null));
    } // canManage
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Get repositories by project id and add last commit info
     *
     * @param int $project_id
     * @return array of objects
     */
    static function findByProjectId($project_id, $visibility = VISIBILITY_PUBLIC) {
      $project_source_repositories = ProjectObjects::find(array(
        'conditions'  => array("project_id = ? AND type = ? AND state >=? AND visibility >=?", $project_id, 'ProjectSourceRepository', STATE_VISIBLE,  $visibility),
        'order' => 'name asc'
      ));
      $project_source_repositories_array = array();
      if (is_foreachable($project_source_repositories)) {
        foreach ($project_source_repositories as $project_source_repository) {
        	$project_source_repositories_array[] = $project_source_repository;
        } //foreach
      } //if
      $source_repositories = ProjectSourceRepositories::getSourceRepositories($project_source_repositories_array);
      return $source_repositories;
    } // find source repositories by project id
    
    
    /**
     * Get repository by project id and parent id
     *
     * @param int $project_id
     * @param int $source_repository_id
     * @return ProjectSourceRepository
     */
    static function findBySourceRepositoryId($project_id,$source_repository_id) {
      $project_source_repository = ProjectObjects::find(array(
        'conditions'  => array("project_id = ? AND type = ? AND integer_field_1 = ? AND state > ?", $project_id, 'ProjectSourceRepository', $source_repository_id, STATE_TRASHED),
        'one' => true
      ));
      
      return $project_source_repository;
    } // find project repositories by project id
    
    /**
     * Remove all project relations based on repository
     * 
     * @param SourceRepository $repository
     * @return boolean
     */
    static function deleteByRepository(SourceRepository $repository) {
    	return ProjectSourceRepositories::delete(array('integer_field_1 = ? AND type = ?', $repository->getId(), 'ProjectSourceRepository'));
    } // deleteByRepository
    
    /**
     * Return array of project repositories with source repositories in them
     *
     * @param ProjectSourceRepository[]
     * @return ProjectSourceRepository[]
     */
    static function getSourceRepositories($project_source_repositories) {
      $return_repositories = array();
      if (is_foreachable($project_source_repositories)) {
        foreach ($project_source_repositories as $project_source_repository) {
          if ($project_source_repository instanceof ProjectSourceRepository) {
            $project_source_repository->source_repository = SourceRepositories::findById($project_source_repository->getParentId());
            $return_repositories[] = $project_source_repository;
          } //if
        } //foreach
      } //if
      return $return_repositories;
    }//getSourceRepositories
    
    
    /**
     * List all repositories that are not already in $project
     * 
     * @param Project $project
     * @return array
     */
    static function getForProjectRepositorySelect($project) {
      $existing_repositories = array();
      $source_repositories = SourceRepositories::find();
      foreach ($source_repositories as $source_repository) {
        /* @var SourceRepository $source_repository */

        if (! ProjectSourceRepositories::findBySourceRepositoryId($project->getId(),$source_repository->getId()) instanceof ProjectSourceRepository) {
          $existing_repositories[$source_repository->getId()] = $source_repository->getName(); 
        } //if
      } //foreach
      return $existing_repositories;
    } // getForSelect
    
    /**
     * Find subitems by parent object, first level only, with option to return only the STATE_VISIBLE
     *
     * @param SourceRepository $parent
     * @param bool $only_visible
     * @return DBResult
     */
    static function findByParent($parent, $only_visible = true) {
      return ProjectObjects::find(array(
        'conditions' => $only_visible ?
            array('integer_field_1 = ? AND type = ? AND state = ?', $parent->getId(), 'ProjectSourceRepository', STATE_VISIBLE)
            :
            array('integer_field_1 = ? AND type = ?', $parent->getId(), 'ProjectSourceRepository'),
      ));
    } // findByParent

    /**
     * Sends notifications to the subscribers of the last update
     *
     * @param ProjectSourceRepository $project_source_repository
     * @return boolean
     */
    public static function sendCommitNotificationsToSubscribers($project_source_repository) {
      $user = null;
      foreach ($project_source_repository->source_repository->getLastCommit($project_source_repository->active_branch, $project_source_repository->last_update_commits_count, true) as $commit) {
        /**
         * @var $commit SourceCommit
         */
        if (is_null($user)) {
          $user = $commit->getCommitedByName();
        } elseif ($user !== $commit->getCommitedByName()) {
          $user = null;
          break;
        } //if
      } //foreach

      if (!is_null($user)) {
        $source_user = SourceUsers::findByRepositoryUser($user, $project_source_repository->source_repository->getId());
        if ($source_user instanceof SourceUser) {
          $user = $source_user->system_user;
        } //if
      } //if

      if ($user instanceof User) {
        AngieApplication::notifications()
            ->notifyAbout('source/new_commits', $project_source_repository, $user)
            ->setDetailedNotifications($project_source_repository->detailed_notifications)
            ->setLastUpdateCommitsCount($project_source_repository->last_update_commits_count)
            ->setActiveBranch($project_source_repository->active_branch)
            ->setRepository($project_source_repository)
            ->sendToSubscribers();
      } else {
        AngieApplication::notifications()
            ->notifyAbout('source/new_commits', $project_source_repository)
            ->setDetailedNotifications($project_source_repository->detailed_notifications)
            ->setLastUpdateCommitsCount($project_source_repository->last_update_commits_count)
            ->setActiveBranch($project_source_repository->active_branch)
            ->setRepository($project_source_repository)
            ->sendToSubscribers();
      } //if
    } //sendCommitNotificationsToSubscribers
    
  }