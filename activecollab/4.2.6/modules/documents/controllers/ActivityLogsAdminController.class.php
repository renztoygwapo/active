<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_activity_logs_admin', ACTIVITY_LOGS_FRAMEWORK);

  /**
   * Activity logs controller
   * 
   * @package activeCollab.modules.documents
   * @subpackage controllers
   */
  class ActivityLogsAdminController extends FwActivityLogsAdminController {
    
    /**
     * Rebuild document entries
     */
    function rebuild_documents() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          DB::beginWork('Rebuilding documents activity logs @ ' . __CLASS__);
          
          $documents = DB::execute('SELECT id, category_id, visibility, created_on, created_by_id, created_by_name, created_by_email FROM ' . TABLE_PREFIX . 'documents WHERE state >= ?', STATE_ARCHIVED);
          if($documents) {
            $batch = DB::batchInsert(TABLE_PREFIX . 'activity_logs', array('subject_type', 'subject_id', 'subject_context', 'action', 'target_type', 'target_id', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email'));
            
            foreach($documents as $document) {
              $target_type = $document['category_id'] ? 'DocumentCategory' : null;
              $target_id = $document['category_id'] ? $document['category_id'] : null;
              
              $batch->insert('Document', $document['id'], 'documents:documents/' . (VISIBILITY_PRIVATE ? 'private' : 'normal') . "/$document[id]", 'document/created', $target_type, $target_id, $document['created_on'], $document['created_by_id'], $document['created_by_name'], $document['created_by_email']);
            } // foreach
            
            $batch->done();
          } // if
          
          DB::commit('Documents activity log rebuilt @ ' . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to rebuild documents activity log @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_documents
    
  }