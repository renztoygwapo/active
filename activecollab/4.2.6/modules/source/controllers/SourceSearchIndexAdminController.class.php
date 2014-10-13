<?php

  AngieApplication::useController('search_index_admin', SYSTEM_MODULE);

  class SourceSearchIndexAdminController extends SearchIndexAdminController {
  
    /**
     * Execute before other any controller action
     */
    function __before() {
      parent::__before();
      
      if(!($this->active_search_index instanceof SourceSearchIndex)) {
        $this->response->operationFailed();
      } // if
    } // __before
    
    /**
     * Build search index
     */
    function build() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          $commits = DB::execute('SELECT id, type, repository_id, message_body FROM ' . TABLE_PREFIX . 'source_commits');
          if($commits) {
            $repositories = SourceRepositories::getIdNameMap();
            foreach($commits as $commit) {  
              $repository_id = (integer) $commit['repository_id'];
              Search::set($this->active_search_index, array(
                'class' => $commit['type'], 
                'id' => $commit['id'],
                'context' => 'source/' . $repository_id, 
                'repository_id' => $repository_id, 
                'repository' => isset($repositories[$repository_id]) && $repositories[$repository_id] ? $repositories[$repository_id] : null, 
                'body' => $commit['message_body'],  
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