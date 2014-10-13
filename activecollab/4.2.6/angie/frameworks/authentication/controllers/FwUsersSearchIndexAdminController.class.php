<?php

  // Extend search index admin controller
  AngieApplication::useController('search_index_admin', AUTHENTICATION_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level users search index admin controller implementation
   * 
   * @package angie.frameworks.authentication
   * @subpackage controllers
   */
  abstract class FwUsersSearchIndexAdminController extends SearchIndexAdminController {
    
    /**
     * Execute before other any controller action
     */
    function __before() {
      parent::__before();
      
      if(!($this->active_search_index instanceof UsersSearchIndex)) {
        $this->response->operationFailed();
      } // if
    } // __before
    
    /**
     * Build search index
     */
    function build() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          $users = DB::execute('SELECT id, first_name, last_name, email FROM ' . TABLE_PREFIX . 'users');
        
          if($users) {
            foreach($users as $user) {
              Search::set($this->active_search_index, array(
                'class' => 'User', 
                'id' => $user['id'], 
                'context' => 'users:users/' . $user['id'], 
                'name' => Users::getUserDisplayName($user), 
                'email' => $user['email'], 
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