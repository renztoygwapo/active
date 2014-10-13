<?php

  /**
   * SourceCommits class
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class SourceCommits extends BaseSourceCommits {
    
    /**
     * Return list of supported commit types
     * 
     * @return array
     */
    static function getCommitTypes() {
      return array('SvnCommit', 'GitCommit', 'MercurialCommit');
    } // getCommitTypes
    
     /**
     * Get recent activity for a repository
     *
     * @param SourceRepository $source_repository
      * @param string $branch_name
     * @param int $from_days_before
     * @return array or null
     */
    static function getRecentActivity($source_repository, $branch_name, $from_days_before = 15) {
      $from = new DateTimeValue(($from_days_before-1).' days ago');

      $beginning_of_day = $from->beginningOfDay();
  
      $max_commits = BaseSourceCommits::count(array("repository_id = ? AND branch_name = ? AND commited_on >= ? GROUP BY DAY(commited_on) ORDER BY row_count DESC LIMIT 1", $source_repository->getId(), $branch_name, $beginning_of_day));
  
      $from_days_before--;
      $activity = array();
      for ($i = $from_days_before; $i >= 0; $i--) {
        $date = new DateTimeValue($i . 'days ago');
        $this_date_beginning = $date->beginningOfDay();
        $this_date_end = $date->endOfDay();
        
        $commits_count = BaseSourceCommits::count(array("repository_id = ? AND branch_name = ? AND commited_on >= ? AND commited_on <= ?", $source_repository->getId(), $branch_name, $this_date_beginning, $this_date_end));
  
        $activity[$i]['commits'] = $commits_count;
        $activity[$i]['created_on'] = date('F d, Y', $date->getTimestamp());
        $activity[$i]['percentage'] = round($commits_count*100/$max_commits);
      } //for
  
      return $activity;
    } // get recent activity
  
    /**
     * Find commit by revision
     *
     * @param mixed $revision
     * @param SourceRepository $source_repository
     * @return SourceCommit
     */
    static function findByRevision($revision, $source_repository, $branch_name) {
      return BaseSourceCommits::find(array(
        'conditions'  => array('revision_number = ? AND repository_id = ? AND type = ? AND branch_name = ?', $revision, $source_repository->getId(), $source_repository->getCommitName(), $branch_name),
        'one'         => true
      )); 
    } //findByRevision 
    
    /**
     * Find all commits with $revision_ids ids in $repository
     *
     * @param array $revision_ids
     * @param SourceRepository $source_repository
     * @return array of SourceCommit
     */
    static function findByRevisionIds($revision_ids, $source_repository, $branch_name) {
  	  return BaseSourceCommits::find(array(
        'conditions' => array('revision_number IN (?) AND repository_id = ? AND type = ? AND branch_name = ?', $revision_ids, $source_repository->getId(), $source_repository->getCommitName(), $branch_name),
        'order'      => 'commited_on DESC, revision_number DESC',
      ));
    } // findByRevisionIds
  
    /**
     * Find last commit
     *
     * @param SourceRepository $source_repository
     * @return SourceCommit
     */
    static function findLastCommit($source_repository, $branch_name) {
      return BaseSourceCommits::find(array(
        'conditions'  => array('repository_id = ? AND type = ? AND branch_name = ?', $source_repository->getId(), $source_repository->getCommitName(), $branch_name),
        'order'       => 'revision_number DESC',
        'one'         => true
      ));
    } // findLastCommit
    
    /**
     * Sort commits by date desc
     *
     * @param SourceCommit[] $commits
     * @return SourceCommit[] $commits
     */
    static function sortCommitsByDate($commits) {
      $commit_number = count($commits);
      $sorted_commits = array();
      for ($i = 0; $i < $commit_number; $i++) {
        $temp = first($commits);
        $index = 0;
        foreach ($commits as $key => $commit) {
          if ($commit->getCommitedOn()->getTimestamp() > $temp->getCommitedOn()->getTimestamp()) {
            $temp = $commit;
            $index = $key;
          } //if
        } //foreach
        $sorted_commits[$i] = $temp;
        unset($commits[$index]);
      } //for
      return $sorted_commits;
    } //sortCommitsByDate
    
    /**
     * Delete commits by soruce repository
     * 
     * @param SourceRepository $repository
     * @return boolean
     */
    static function deleteByRepository(SourceRepository $repository) {
      $commits_table = TABLE_PREFIX . 'source_commits';
      $paths_table = TABLE_PREFIX . 'source_paths';
      
      $commit_ids = DB::executeFirstColumn("SELECT id FROM $commits_table WHERE repository_id = ?", $repository->getId());
      if($commit_ids) {
        try {
          DB::beginWork();
          
          DB::execute("DELETE FROM $paths_table WHERE commit_id IN (?)", $commit_ids);
          DB::execute("DELETE FROM $commits_table WHERE id IN (?)", $commit_ids);
          
          DB::commit();
        } catch (Exception $e) {
          DB::rollback();
          throw $e;
        } // try
      } // if
    } // deleteByRepository
    
  }