<?php

  /**
   * Mercurial commits managament class
   * 
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class MercurialCommits extends SourceCommits {
    /**
     * Find mercurial commit by revision
     *
     * @param int $revision
     * @param MercurialRepository $source_repository
     * @return MercurialCommit
     */
    function findByRevision($revision, $source_repository, $branch_name) {
      return BaseSourceCommits::find(array(
        'conditions'  => array('`revision_number` = ? AND `repository_id` = ? AND `type` = ? AND branch_name = ?', $revision, $source_repository->getId(), 'MercurialCommit', $branch_name),
        'one'         => true
      ));
    } //findByRevision
    
    /**
     * Find all commits with $revision_ids ids in $repository
     *
     * @param array $revision_ids
     * @param MercurialRepository $source_repository
     * @return array of MercurialCommit
     */
    function findByRevisionIds($revision_ids, $source_repository, $branch_name) {
      return BaseSourceCommits::find(array(
        'conditions' => array('revision_number IN (?) AND repository_id = ? AND `type` = ? AND branch_name = ?', $revision_ids, $source_repository->getId(), 'MercurialCommit', $branch_name),
        'order'      => 'commited_on DESC, revision_number DESC',
      ));
    } //findByRevisionIds
    
    /**
     * Find last commit
     *
     * @param MercurialRepository $source_repository
     * @return MercurialCommit
     */
    function findLastCommit($source_repository, $branch_name) {
      return BaseSourceCommits::find(array(
         'conditions'  => array('repository_id = ? AND type = ? AND branch_name = ?', $source_repository->getId(), 'MercurialCommit', $branch_name),
         'order'       => 'revision_number DESC',
         'one'         => true
       ));
    } //findLastCommit
  } //MercurialCommits