<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_object_contexts_admin', ENVIRONMENT_FRAMEWORK);

  /**
   * Object contexts controller
   * 
   * @package activeCollab.modules.discussions
   * @subpackage controllers
   */
  class ObjectContextsAdminController extends FwObjectContextsAdminController {
    
    /**
     * Rebuild document entries
     */
    function rebuild_documents() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          DB::beginWork('Rebuilding documents contexts @ ' . __CLASS__);
          
          $documents = DB::execute('SELECT id, visibility FROM ' . TABLE_PREFIX . 'documents WHERE state >= ?', STATE_TRASHED);
          if($documents) {
            $batch = DB::batchInsert(TABLE_PREFIX . 'object_contexts', array('parent_type', 'parent_id', 'context'));
            
            foreach($documents as $document) {
              $batch->insert('Document', $document['id'], 'documents:documents/' . (VISIBILITY_PRIVATE ? 'private' : 'normal') . "/$document[id]");
            } // foreach
            
            $batch->done();
          } // if
          
          DB::commit('Documents contexts rebuilt @ ' . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to rebuild documents contexts @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_documents
    
  }