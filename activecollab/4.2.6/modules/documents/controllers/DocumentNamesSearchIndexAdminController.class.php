<?php

  // Build on top of base names search index admin controller
  AngieApplication::useController('names_search_index_admin', SYSTEM_MODULE);

  /**
   * Document names search index admin controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class DocumentNamesSearchIndexAdminController extends NamesSearchIndexAdminController {
  
    /**
     * Build search index
     */
    function build() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          $documents = DB::execute('SELECT id, name, visibility FROM ' . TABLE_PREFIX . 'documents');
        
          if($documents) {
            foreach($documents as $document) {
              Search::set($this->active_search_index, array(
                'class' => 'Document', 
                'id' => $document['id'], 
                'context' => "documents:documents/" . ($document['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal') . "/$document[id]",
                'name' => $document['name'], 
              ));
            } // foreach
          } // if
          
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // build
    
  }