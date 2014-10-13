<?php

	/**
   * SvnCommit record class
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class SvnCommit extends SourceCommit {
    
    /**
     * Repository that commit belongs to
     *
     * @var SourceRepository
     */
    protected $source_repository = null;
    
    /**
     * Set commitedBy info
     *
     * @param User $commited_by
     */
    function setCommitedBy($commited_by) {
      $this->setCommitedByName($commited_by->getName());
      $this->setCommitedByEmail($commited_by->getEmail());
    } // setCommitedBy
    
    /**
     * Get commitedBy information
     *
     * @param SourceRepository $source_repository
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
     * Get authoredBy information
     *
     * @param SourceRepository $source_repository
     * @return User
     */
    function getAuthoredBy($source_repository = null) {
      return $this->getCommitedBy($source_repository);
    } // getAuthoredBy

    /**
     * Checks if this path is accessible (if it is in the sub-folder of the repository if repository is showing to sub-folder instead of root)
     *
     * @param string $path
     * @return bool
     */
    function checkPathAvailability($path) {
      $this->source_repository = $this->getSourceRepository();
      $full_path = without_slash($this->source_repository->getAdditionalProperty('svn_repository_root_path')) . $path;

      return strpos($full_path, $this->source_repository->getRepositoryPathUrl()) === 0;
    } //checkPathAvailability

    /**
     * Return verbose commit name
     *
     * @param Language $language
     * @return string
     */
    function getVerboseName($language = null) {
      return lang('Commit #:num', array(
        'num' => $this->getName(),
      ), true, $language);
    } // getVerboseName
  
  }