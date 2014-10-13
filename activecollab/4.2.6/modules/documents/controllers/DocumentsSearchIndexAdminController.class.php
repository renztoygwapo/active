<?php

  // Build on top of search index controller
  AngieApplication::useController('search_index_admin', SEARCH_FRAMEWORK_INJECT_INTO);

  /**
   * Documents search index admin
   * 
   * @package activeCollab.modules.documents
   * @subpackage controllers
   */
  class DocumentsSearchIndexAdminController extends SearchIndexAdminController {
  
    /**
     * Execute before other any controller action
     */
    function __before() {
      parent::__before();
      
      if(!($this->active_search_index instanceof DocumentsSearchIndex)) {
        $this->response->operationFailed();
      } // if
    } // __before
    
    /**
     * Build search index
     */
    function build() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          $documents = DB::execute('SELECT id, category_id, type, name, body FROM ' . TABLE_PREFIX . 'documents WHERE state > ?', STATE_DELETED);
        
          if($documents) {
            $categories = Categories::getIdNameMap(null, 'DocumentCategory');
            
            foreach($documents as $document) {
              $category_id = (integer) $document['category_id'];
              
              Search::set($this->active_search_index, array(
                'class' => 'Document', 
                'id' => $document['id'], 
                'context' => 'documents:documents/' . $document['id'], 
                'category_id' => $document['category_id'],
              	'category' => $categories && isset($categories[$category_id]) && $categories[$category_id] ? $categories[$category_id] : null,
                'name' => $document['name'],  
                'body' => $document['type'] == Document::TEXT ? $document['body'] : null, 
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