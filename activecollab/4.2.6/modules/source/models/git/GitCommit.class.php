<?php

/**
   * GitCommit record class
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
class GitCommit extends SourceCommit {
  
  
  /**
   * Repository that commit belongs to
   *
   * @var Repository
   */
  protected $source_repository = null;

  /**
   * Construct a new GitCommit
   *
   * @param mixed $id
   * @return GitCommit
   */
  function __construct($id = null) {
    parent::__construct();
  } // __construct
  
  
  /**
   * Set commitedBy info
   *
   * @param User $commited_by
   * @return null
   */
  function setCommitedBy($commited_by) {
    $this->setCommitedByName($commited_by->getName());
    $this->setCommitedByEmail($commited_by->getEmail());
  } // setCommitedBy
  
  /**
   * Get commitedBy information
   *
   * @param SourceRepositoriy $source_repository
   * @return User
   */
  function getCommitedBy($source_repository = null) {
    if (is_null($source_repository)) {
      $source_repository = SourceRepositories::findById($this->getRepositoryId());
      $source_repository->mapped_users = SourceUsers::findBySourceRepository($source_repository);
    } // if
    
    if (isset($source_repository->mapped_users[$this->getCommitedByName()]) && $source_repository->mapped_users[$this->getCommitedByName()] instanceof SourceUser) {
      $source_user = $source_repository->mapped_users[$this->getCommitedByName()];
      if($source_user->system_user instanceof User) {
        return $source_user->system_user;
      } // if
    } // if
    
    return parent::getCommitedByName();
  } // getCommitedBy
  
	/**
   * Set authoredBy info
   *
   * @param User $authored_by
   * @return null
   */
  function setAuthoredBy($authored_by) {
    $this->setAuthoredByName($authored_by->getName());
    $this->setAuthoredByEmail($authored_by->getEmail());
  } // setAuthoredBy
  
  /**
   * Get authoredBy information
   *
   * @param SourceRepository $source_repository
   * @return User
   */
  function getAuthoredBy($source_repository = null) {
    if (is_null($source_repository)) {
      $source_repository = SourceRepositories::findById($this->getRepositoryId());
      $source_repository->mapped_users = SourceUsers::findBySourceRepository($source_repository);
    } // if
    
    if (isset($source_repository->mapped_users[$this->getAuthoredByName()]) && $source_repository->mapped_users[$this->getAuthoredByName()] instanceof SourceUser) {
      $source_user = $source_repository->mapped_users[$this->getAuthoredByName()];
      if($source_user->system_user instanceof User) {
        return $source_user->system_user;
      } // if
    } // if
    
    return parent::getAuthoredByName();
  } // getAuthoredBy

  /**
   * Return verbose commit name
   *
   * @param Language $language
   * @return string
   */
  function getVerboseName($language = null) {
    return lang('Commit #:num', array(
      'num' => substr($this->getName(), 0, 8),
    ), true, $language);
  } // getVerboseName

}