<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_activity_logs_admin', ACTIVITY_LOGS_FRAMEWORK);

  /**
   * Activity logs controller
   * 
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class ActivityLogsAdminController extends FwActivityLogsAdminController {
    
    /**
     * Rebuild task entries
     */
    function rebuild_tracking() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        
        /**
         * Rebuild activity logs from given table
         *
         * @param string $table_name
         * @param string $item_class
         * @param string $action
         * @param string $context
         */
        $rebuild = function($table_name, $item_class, $action, $context) {
          $batch = DB::batchInsert(TABLE_PREFIX . 'activity_logs', array('subject_type', 'subject_id', 'subject_context', 'action', 'target_type', 'target_id', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email'));
          
          $records = DB::execute("SELECT id, parent_id, created_on, created_by_id, created_by_name, created_by_email FROM $table_name WHERE parent_type = 'Project' AND state >= ?", STATE_ARCHIVED);
        
          if($records) {
            foreach($records as $record) {
              $batch->insert($item_class, $record['id'], "projects:projects/$record[parent_id]/tracking/$context/$record[id]", $action, 'Project', $record['parent_id'], $record['created_on'], $record['created_by_id'], $record['created_by_name'], $record['created_by_email']);
            } // if
          } // if
          
          $project_objects_table = TABLE_PREFIX . 'project_objects';
          $subtasks_table = TABLE_PREFIX . 'subtasks';
          
          $records = DB::execute("SELECT $project_objects_table.project_id, $project_objects_table.visibility, $table_name.id, $table_name.parent_id, $table_name.created_on, $table_name.created_by_id, $table_name.created_by_name, $table_name.created_by_email FROM $project_objects_table, $table_name WHERE $table_name.parent_type = $project_objects_table.type AND $table_name.parent_id = $project_objects_table.id AND $table_name.parent_type = 'Task' AND $table_name.state >= ?", STATE_ARCHIVED);
          if($records) {
            foreach($records as $record) {
              $visibility = $record['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
              
              $batch->insert($item_class, $record['id'], "projects:projects/$record[project_id]/tasks/$visibility/$record[parent_id]/tracking/$context/$record[id]", $action, 'Task', $record['parent_id'], $record['created_on'], $record['created_by_id'], $record['created_by_name'], $record['created_by_email']);
            } // if
          } // if
          
          $records = DB::execute("SELECT id, parent_id, created_on, created_by_id, created_by_name, created_by_email FROM $table_name WHERE parent_type = 'ProjectObjectSubtask' AND state >= ?", STATE_ARCHIVED);
          if($records) {
            $subtask_ids = array();
            
            foreach($records as $record) {
              $subtask_ids[] = $record['parent_id'];
            } // foreach
            
            $subtask_ids = array_unique($subtask_ids);
            
            $subtask_parents = array();
            
            $rows = DB::execute("SELECT $project_objects_table.project_id, $project_objects_table.visibility, $subtasks_table.id, $subtasks_table.parent_type, $subtasks_table.parent_id FROM $subtasks_table, $project_objects_table WHERE $subtasks_table.parent_type = $project_objects_table.type AND $subtasks_table.parent_id = $project_objects_table.id AND $subtasks_table.id IN (?)", $subtask_ids);
            if($rows) {
              foreach($rows as $row) {
                $subtask_parents[$row['id']] = array($row['parent_type'], $row['parent_id'], $row['project_id'], $row['visibility']);
              } // foreach
            } // if
            
            foreach($records as $record) {
              $subtask_id = $record['parent_id'];
              
              if(isset($subtask_parents[$subtask_id])) {
                list($parent_type, $parent_id, $project_id, $parent_visibility) = $subtask_parents[$subtask_id];
                
                $visibility = $parent_visibility == VISIBILITY_PRIVATE ? 'private' : 'normal';
                
                if($parent_type == 'TodoList') {
                  $subtask_record_context = "projects:projects/$project_id/todo/$visibility/$parent_id/subtasks/$subtask_id/tracking/$context/$record[id]";
                } else {
                  $subtask_record_context = "projects:projects/$project_id/tasks/$visibility/$parent_id/subtasks/$subtask_id/tracking/$context/$record[id]";
                } // if
                
                $batch->insert($item_class, $record['id'], $subtask_record_context, $action, 'ProjectObjectSubtask', $subtask_id, $record['created_on'], $record['created_by_id'], $record['created_by_name'], $record['created_by_email']);
              } // if
            } // foreach
          } // if
          
          $batch->done();
        };
        
        try {
          DB::beginWork("Update tracking records @ " . __CLASS__);
          
          $rebuild(TABLE_PREFIX . 'time_records', 'TimeRecord', 'time_record/created', 'time');
          $rebuild(TABLE_PREFIX . 'expenses', 'Expense', 'expense/created', 'expenses');
          
          DB::commit("Tracking records updated @ " . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback("Failed to update tracking records form $table_name @ " . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_tracking
    
  }