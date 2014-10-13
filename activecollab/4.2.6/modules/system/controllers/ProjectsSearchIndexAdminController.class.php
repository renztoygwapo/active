<?php

  // Build on top of search index controller
  AngieApplication::useController('search_index_admin', SEARCH_FRAMEWORK_INJECT_INTO);

  /**
   * Project search index administration controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectsSearchIndexAdminController extends SearchIndexAdminController {
  
    /**
     * Execute before other any controller action
     */
    function __before() {
      parent::__before();
      
      if(!($this->active_search_index instanceof ProjectsSearchIndex)) {
        $this->response->operationFailed();
      } // if
    } // __before
    
    /**
     * Build search index
     */
    function build() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          $projects = DB::execute('SELECT id, category_id, company_id, name, slug, overview, completed_on FROM ' . TABLE_PREFIX . 'projects WHERE state > ?', STATE_DELETED);
        
          if($projects) {
            $categories = Categories::getIdNameMap(null, 'ProjectCategory');
            $companies = Companies::getIdNameMap();
            
            foreach($projects as $project) {
              $category_id = (integer) $project['category_id'];
              $company_id = (integer) $project['company_id'];
              
              Search::set($this->active_search_index, array(
                'class' => 'Project', 
                'id' => $project['id'], 
                'context' => 'projects:projects/' . $project['id'], 
                'category_id' => $category_id, 
                'category' => $categories && isset($categories[$category_id]) && $categories[$category_id] ? $categories[$category_id] : null, 
                'company_id' => $company_id,
                'company' => $companies && isset($companies[$company_id]) && $companies[$company_id] ? $companies[$company_id] : null,  
                'name' => $project['name'], 
                'slug' => $project['slug'],
                'overview' => $project['overview'], 
                'completed_on' => $project['completed_on'], 
              ));
            } // foreach
          } // if
          
          $this->response->ok();
        } catch(Exception $e) {
          print $e->getMessage();

          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // build
    
  }