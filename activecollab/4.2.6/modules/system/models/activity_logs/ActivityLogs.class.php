<?php

  /**
   * ActivityLogs class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ActivityLogs extends FwActivityLogs {

    /**
     * activeCollab specific related data loader for activities
     *
     * @param ActivityLog[] $activity_logs
     * @param IUser $user
     * @param Closure $populate_authors_callback
     * @return array
     */
    static function loadRelatedDataFromActivities($activity_logs, IUser $user, $populate_authors_callback = null) {
      return parent::loadRelatedDataFromActivities($activity_logs, $user, function($author_ids) {
        $authors = count($author_ids) ? Users::getIdDetailsMap($author_ids, array('id', 'company_id', 'first_name', 'last_name', 'email')) : null;

        if($authors) {
          foreach($authors as $k => $v) {
            $authors[$k]['display_name'] = Users::getUserDisplayName($v, true);
            $authors[$k]['urls'] = array(
              'view' => Router::assemble('people_company_user', array('company_id' => $v['company_id'], 'user_id' => $k)),
            );
          } // foreach
        } // if

        return $authors;
      });
    } // loadRelatedDataFromActivities
    
    /**
     * Rebuild activity logs for specific project object type(s)
     * 
     * @param array $types
     * @param string $context
     * @param array $creation_action
     * @param array $completion_action
     * @param array $reopening_action
     * @param boolean $update_comments
     * @param boolean $update_subtasks
     * @throws Exception
     */
    static function rebuildProjectObjectActivityLogs($types, $context, $creation_action, $completion_action = null, $reopening_action = null, $update_comments = false, $update_subtasks = false) {
      $activity_logs_table = TABLE_PREFIX . 'activity_logs';
      $projects_table = TABLE_PREFIX . 'projects';
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      try {
        DB::beginWork('Updating project object activity logs @ ' . __CLASS__);
        
        $objects = DB::execute("SELECT $project_objects_table.id, $project_objects_table.type, $project_objects_table.project_id, $project_objects_table.visibility, $project_objects_table.created_on, $project_objects_table.created_by_id, $project_objects_table.created_by_name, $project_objects_table.created_by_email FROM $project_objects_table, $projects_table WHERE $project_objects_table.project_id = $projects_table.id AND $project_objects_table.type IN (?) AND $projects_table.completed_on IS NULL", $types);
        if($objects instanceof DBResult) {
          $objects->setCasting(array(
            'id' => DBResult::CAST_INT, 
            'project_id' => DBResult::CAST_INT, 
            'visibility' => DBResult::CAST_INT, 
            'created_by_id' => DBResult::CAST_INT,  
          ));
          
          $parents = array(); // Cached parents
          $object_visibilities = array();
          
          $batch = DB::batchInsert($activity_logs_table, array('subject_type', 'subject_id', 'subject_context', 'action', 'target_type', 'target_id', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email'));
          
          foreach($objects as $object) {
            $visibility = $object['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
            
            if(is_array($creation_action)) {
              $action = isset($creation_action[$object['type']]) ? $creation_action[$object['type']] : Inflector::camelize($object['type']) . '/created';
            } else {
              $action = $creation_action;
            } // if
            
            $batch->insert($object['type'], $object['id'], "projects:projects/$object[project_id]/$context/$visibility/$object[id]", $action, 'Project', $object['project_id'], $object['created_on'], $object['created_by_id'], $object['created_by_name'], $object['created_by_email']);
            
            ActivityLogs::rebuildStateChangeActivityLogs($object['type'], $object['id'], "projects:projects/$object[project_id]/$context/$visibility/$object[id]", 'Project', $object['project_id'], substr($action, 0, strpos($action, '/')));
            
            if($completion_action && $reopening_action) {
              if(is_array($completion_action)) {
                $completion_action_name = isset($completion_action[$object['type']]) ? $completion_action[$object['type']] : Inflector::camelize($object['type']) . '/completed';
              } else {
                $completion_action_name = $completion_action;
              } // if
              
              if(is_array($reopening_action)) {
                $reopening_action_name = isset($reopening_action[$object['type']]) ? $reopening_action[$object['type']] : Inflector::camelize($object['type']) . '/reopened';
              } else {
                $reopening_action_name = $reopening_action;
              } // if
              
              ActivityLogs::rebuildCompletionActivityLogs($object['type'], $object['id'], "projects:projects/$object[project_id]/$context/$visibility/$object[id]", 'Project', $object['project_id'], $completion_action_name, $reopening_action_name);
            } // if
            
            if($update_comments || $update_subtasks) {
              if(!isset($parents[$object['type']])) {
                $parents[$object['type']] = array();
              } // if
              
              $parents[$object['type']][] = (integer) $object['id'];
            } // if
          } // foreach
          
          if(($update_comments || $update_subtasks) && count($parents)) {
            if($update_comments) {
              $comments_table = TABLE_PREFIX . 'comments';
              
              $conditions = array();
            
              foreach($parents as $type => $ids) {
                $conditions[] = DB::prepare("($comments_table.parent_type = ? AND $comments_table.parent_id IN (?))", $type, array_unique($ids));
              } // foreach
              
              $conditions = '(' . implode(' AND ', $conditions) . ')';
              
              $comments = DB::execute("SELECT $project_objects_table.project_id, $project_objects_table.visibility, $comments_table.id, $comments_table.type, $comments_table.parent_type, $comments_table.parent_id, $comments_table.created_on, $comments_table.created_by_id, $comments_table.created_by_name, $comments_table.created_by_email FROM $comments_table, $project_objects_table WHERE ($comments_table.parent_type = $project_objects_table.type AND $comments_table.parent_id = $project_objects_table.id) AND $conditions AND $comments_table.state >= ?", STATE_ARCHIVED);
              if($comments) {
                foreach($comments as $comment) {
                  $visibility = $comment['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
                  
                  $batch->insert($comment['type'], $comment['id'], "projects:projects/$comment[project_id]/$context/$visibility/$comment[parent_id]/comments/$comment[id]", 'comment/created', $comment['parent_type'], $comment['parent_id'], $comment['created_on'], $comment['created_by_id'], $comment['created_by_name'], $comment['created_by_email']);
                  ActivityLogs::rebuildStateChangeActivityLogs($comment['type'], $comment['id'], "projects:projects/$comment[project_id]/$context/$visibility/$comment[parent_id]/comments/$comment[id]", $comment['parent_type'], $comment['parent_id'], 'comment');
                } // foreach
              } // if
            } // if
            
            if($update_subtasks) {
              $subtasks_table = TABLE_PREFIX . 'subtasks';
              
              $conditions = array();
            
              foreach($parents as $type => $ids) {
                $conditions[] = DB::prepare("($subtasks_table.parent_type = ? AND $subtasks_table.parent_id IN (?))", $type, array_unique($ids));
              } // foreach
              
              $conditions = '(' . implode(' AND ', $conditions) . ')';
              
              $subtasks = DB::execute("SELECT $project_objects_table.project_id, $project_objects_table.visibility, $subtasks_table.id, $subtasks_table.type, $subtasks_table.parent_type, $subtasks_table.parent_id, $subtasks_table.created_on, $subtasks_table.created_by_id, $subtasks_table.created_by_name, $subtasks_table.created_by_email FROM $subtasks_table, $project_objects_table WHERE ($subtasks_table.parent_type = $project_objects_table.type AND $subtasks_table.parent_id = $project_objects_table.id) AND $conditions AND $subtasks_table.state >= ?", STATE_ARCHIVED);
              if($subtasks) {
                foreach($subtasks as $subtask) {
                  $visibility = $subtask['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
                  
                  $batch->insert($subtask['type'], $subtask['id'], "projects:projects/$subtask[project_id]/$context/$visibility/$subtask[parent_id]/subtasks/$subtask[id]", 'subtask/created', $subtask['parent_type'], $subtask['parent_id'], $subtask['created_on'], $subtask['created_by_id'], $subtask['created_by_name'], $subtask['created_by_email']);
                  
                  ActivityLogs::rebuildStateChangeActivityLogs($subtask['type'], $subtask['id'], "projects:projects/$subtask[project_id]/$context/$visibility/$subtask[parent_id]/subtasks/$subtask[id]", $subtask['parent_type'], $subtask['parent_id'], 'subtask');
                  ActivityLogs::rebuildCompletionActivityLogs($subtask['type'], $subtask['id'], "projects:projects/$subtask[project_id]/$context/$visibility/$subtask[parent_id]/subtasks/$subtask[id]", $subtask['parent_type'], $subtask['parent_id'], 'subtask/completed', 'subtask/reopened');
                } // foreach
              } // if
            } // if
          } // if
          
          $batch->done();
        } // if
        
        DB::commit('Project object activity logs updated @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to update project object activity logs');
        throw $e;
      } // try
    } // rebuildProjectObjectActivityLogs
  
  }