<?php

  /**
   * SourcePaths class
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class SourcePaths extends BaseSourcePaths {
    
    protected $active_commit = null;
    
    
    /**
     * Get SourcePaths that belong to forwarded commit
     *
     * @param int $source_commit_id
     * @return SourcePath[]
     */
    static function getPathsForCommit($source_commit_id, $offset = null, $limit = FILE_COUNT_IN_DIFF_COMMIT) {
      if ($offset) {
        return parent::find(array(
          'conditions' => array("`commit_id` = ?", $source_commit_id),
          'order'	=> 'action ASC',
          'limit' => $limit,
          'offset' => $offset
        ));
      } else {
        return parent::find(array(
          'conditions' => array("`commit_id` = ?", $source_commit_id),
          'order'	=> 'action ASC'
        ));
      } //if
    }//getPathsForCommit
    
    /**
     * Get SourcePaths for the path
     *
     * @param string $path
     * @param string $branch
     * @return SourcePath[]
     */
    static function findSourcePathsForPath($path, $branch) {
      $paths =  parent::find(array(
        'conditions' => array("`path` = ?", $path),
      ));
      $return_paths = array();
      foreach ($paths as $path) {
        /**
         * @var SourcePath $path
         */
        $commit = SourceCommits::findById($path->getCommitId());
        if ($commit->getBranchName() == $branch) {
          $return_paths[] = $path;
        } //if
      } // foreach
      return $return_paths;
    }//findSourcePathsForPath
  } //SourcePaths