<?php

  /**
   * CommitProjectObjects class
   * 
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class CommitProjectObjects extends BaseCommitProjectObjects {
    
    /**
     * Return number of commits related to a project object
     *
     * @param ProjectObject $object
     * @return integer
     */
    function countByObject($object) {
      $object_commits_count = CommitProjectObjects::count(array('parent_id = ?', $object->getId()));
      
      if($object instanceof ISubtasks) {
        $subtask_ids = DB::executeFirstColumn("SELECT id FROM " . TABLE_PREFIX . "subtasks WHERE parent_type = ? AND parent_id = ?", get_class($object), $object->getId());
        
        if ($subtask_ids) {
          $object_commits_count += (integer) DB::executeFirstCell("SELECT COUNT(*) AS row_count FROM " . TABLE_PREFIX . "commit_project_objects WHERE parent_type = ? AND parent_id IN (?)", 'ProjectObjectSubtask', $subtask_ids);
        } // if
      } //if
      
      return $object_commits_count;
    } // countByObject
    
    /**
     * Get all commits related to a project object
     *
     * @param ProjectObject $object
     * @return array
     */
    static function findCommitsByObject($object) {

      $parent_object_ids = array();
      $parent_object_ids[] = $object->getId();
      
      /**
       * Try to find commits related to children objects
       */
      $subtask_ids = array();
      if($object instanceof Task) {
        $subtasks = DB::execute("SELECT id FROM ".TABLE_PREFIX."subtasks WHERE parent_id = ".$object->getid()." AND type = 'ProjectObjectSubtask'");
        if (is_foreachable($subtasks)) {
          foreach ($subtasks as $subtask) {
            $subtask_ids[] = $subtask['id'];
          } // foreach
        } // if
      } // if
      
      $objects_ids = array_merge($parent_object_ids, $subtask_ids);
      
      $commit_project_objects = CommitProjectObjects::find(array(
        'conditions' => array("parent_id IN(".implode(',', $objects_ids).")"),
        'order' => 'repository_id ASC, revision DESC'
      ));
      if (is_foreachable($commit_project_objects)) {
        $commits = array();
        $revisions = array();
        foreach ($commit_project_objects as $commit_project_object) {
          /**
           * @var CommitProjectObject $commit_project_object
           */
          if (!in_array($commit_project_object->getRevision(), $revisions)) { // prevent commits from showing more than once
            $revisions[] = $commit_project_object->getRevision();
            $repository = SourceRepositories::findById($commit_project_object->getRepositoryId());
            if ($repository instanceof SourceRepository) {
              $commit = $repository->getCommitByRevision($commit_project_object->getRevision(), $commit_project_object->getBranchName());
              if($commit instanceof SourceCommit) {
                $commits[] = $commit;
              } // if
            } // if
          } // if
        } // foreach

        return $commits;
      } else {
        return false;
      } // if
      
    } // findCommitsByObjectId
  
  }