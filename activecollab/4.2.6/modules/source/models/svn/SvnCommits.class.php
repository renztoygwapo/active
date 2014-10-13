<?php

  /**
   * Subversion commits managament class
   * 
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class SvnCommits extends SourceCommits {
    
  	/**
     * Find subversion commit by revision
     *
     * @param int $revision
     * @param SvnRepository $source_repository
     * @return SvnCommit
     */
    function findByRevision($revision, $source_repository, $branch_name) {
      return BaseSourceCommits::find(array(
        'conditions'  => array('`revision_number` = ? AND `repository_id` = ? AND `type` = ? AND branch_name = ?', $revision, $source_repository->getId(), 'SvnCommit', $branch_name),
        'one'         => true
      ));
    } //findByRevision
    
    /**
     * Find all commits with $revision_ids ids in $repository
     *
     * @param array $revision_ids
     * @param SvnRepository $source_repository
     * @return array of SvnCommit
     */
    function findByRevisionIds($revision_ids, $source_repository, $branch_name) {
      return BaseSourceCommits::find(array(
        'conditions' => array('revision_number IN (?) AND repository_id = ? AND `type` = ? AND branch_name = ?', $revision_ids, $source_repository->getId(), 'SvnCommit', $branch_name),
        'order'      => 'commited_on DESC, revision_number DESC',
      ));
    } //findByRevisionIds
    
    /**
     * Find last commit
     *
     * @param SvnRepository $source_repository
     * @return SvnCommit
     */
    function findLastCommit($source_repository, $branch_name) {
      return BaseSourceCommits::find(array(
         'conditions'  => array('repository_id = ? AND type = ? AND branch_name = ?', $source_repository->getId(), 'SvnCommit', $branch_name),
         'order'       => 'revision_number DESC',
         'one'         => true
       ));
    } //findLastCommit
  } //SourceCommits