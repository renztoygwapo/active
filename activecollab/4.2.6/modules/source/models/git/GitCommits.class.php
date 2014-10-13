<?php

  /**
   * GIT commits managament class
   * 
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class GitCommits extends SourceCommits {
    
    /**
     * Find git commit by revision
     *
     * @param int $revision
     * @param GitRepository $source_repository
     * @param string $branch_name
     * @return GitCommit
     */
    static function findByRevision($revision, $source_repository, $branch_name) {
      return BaseSourceCommits::find(array(
        'conditions'  => array('`revision_number` = ? AND `repository_id` = ? AND `type` = ? AND branch_name = ?', $revision, $source_repository->getId(), 'GitCommit', $branch_name),
        'one'         => true
      ));
    } //findByRevision

    /**
     * Find git commit by git sha number
     *
     * @param string $sha_number
     * @param GitRepository $source_repository
     * @param string $branch_name
     * @return GitCommit
     */
    static function findByGitShaNumber($sha_number, $source_repository, $branch_name) {
      return BaseSourceCommits::find(array(
        'conditions'  => array('name = ? AND repository_id= ? AND type = ? AND branch_name = ?', $sha_number, $source_repository->getId(), 'GitCommit', $branch_name),
        'one'         => true
      ));
    } //findByGitShaNumber
    
    /**
     * Find all commits with $revision_ids ids in $repository
     *
     * @param array $revision_ids
     * @param GitRepository $source_repository
     * @param string $branch_name
     * @return array of GitCommit
     */
    static function findByRevisionIds($revision_ids, $source_repository, $branch_name) {
      return BaseSourceCommits::find(array(
        'conditions' => array('revision_number IN (?) AND repository_id = ? AND `type` = ? AND branch_name = ?', $revision_ids, $source_repository->getId(), 'GitCommit', $branch_name),
        'order'      => 'commited_on DESC, revision_number DESC',
      ));
    } //findByRevisionIds
    
    /**
     * Find last commit
     *
     * @param GitRepository $source_repository
     * @param string $branch_name
     * @return GitCommit
     */
    static function findLastCommit($source_repository, $branch_name) {
      return BaseSourceCommits::find(array(
         'conditions'  => array('repository_id = ? AND type = ? AND branch_name = ?', $source_repository->getId(), 'GitCommit', $branch_name),
         'order'       => 'revision_number DESC',
         'one'         => true
       ));
    } //findLastCommit
    
  } //GitCommits