<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_object_contexts_admin', ENVIRONMENT_FRAMEWORK);

  /**
   * Object contexts controller
   * 
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class ObjectContextsAdminController extends FwObjectContextsAdminController {
    
    /**
     * Rebuild tracking entries
     */
    function rebuild_tracking() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          DB::beginWork('Rebulding tracking object contexts @ ' . __CLASS__);
          
          $time_records_table = TABLE_PREFIX . 'time_records';
          $expenses_table = TABLE_PREFIX . 'expenses';
          
          $min_state = DB::escape(STATE_ARCHIVED);
          
          $rows = DB::execute("(SELECT id, 'TimeRecord' AS type, parent_id FROM $time_records_table WHERE $time_records_table.parent_type = 'Project' AND $time_records_table.state >= $min_state) UNION ALL
          										 (SELECT id, 'Expense' AS type, parent_id FROM $expenses_table WHERE $expenses_table.parent_type = 'Project' AND $expenses_table.state >= $min_state)");
          
          if($rows) {
            $batch = DB::batchInsert(TABLE_PREFIX . 'object_contexts', array('parent_type', 'parent_id', 'context'));
            
            foreach($rows as $row) {
              if($row['type'] == 'TimeRecord') {
                $batch->insert($row['type'], $row['id'], "projects:projects/$row[parent_id]/tracking/time/$row[id]");
              } else {
                $batch->insert($row['type'], $row['id'], "projects:projects/$row[parent_id]/tracking/expenses/$row[id]");
              } // if
            } // foreach
            
            $batch->done();
          } // if
          
          DB::commit('Tracking object contexts rebuilt @ ' . __CLASS__);
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to rebuild tracking object contexts @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_tracking
    
  }