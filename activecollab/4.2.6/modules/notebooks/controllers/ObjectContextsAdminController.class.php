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
     * Rebuild notebook entries
     */
    function rebuild_notebooks() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating notebook contexts @ ' . __CLASS__);
          
          $notebooks = DB::execute('SELECT id, project_id, visibility FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND state >= ?', 'Notebook', STATE_TRASHED);
          if($notebooks) {
            $notebooks->setCasting(array(
              'id' => DBResult::CAST_INT, 
              'project_id' => DBResult::CAST_INT, 
              'visibility' => DBResult::CAST_INT,  
            ));
            
            $batch = DB::batchInsert(TABLE_PREFIX . 'object_contexts', array('parent_type', 'parent_id', 'context'));
            
            foreach($notebooks as $notebook) {
              $visibility = $notebook['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
              
              $batch->insert('Notebook', $notebook['id'], "projects:projects/$notebook[project_id]/notebooks/$visibility/$notebook[id]");
              
              $notebook_page_ids = NotebookPages::getAllIdsByNotebook($notebook['id']);
              if($notebook_page_ids) {
                foreach($notebook_page_ids as $notebook_page_id) {
                  $batch->insert('NotebookPage', $notebook_page_id, "projects:projects/$notebook[project_id]/notebooks/$visibility/$notebook[id]/pages/$notebook_page_id");
                } // foreach
              } // if
            } // foreach
            
            $batch->done();
          } // if
          
          DB::commit('Notebook contexts updated @ ' . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to rebuild notebook contexts @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_notebooks
    
  }