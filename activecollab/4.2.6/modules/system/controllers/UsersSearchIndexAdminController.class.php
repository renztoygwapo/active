<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_users_search_index_admin', AUTHENTICATION_FRAMEWORK);

  /**
   * Application level users search index administration controller implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class UsersSearchIndexAdminController extends FwUsersSearchIndexAdminController {
  
    /**
     * Build search index
     */
    function build() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          $users = DB::execute('SELECT id, company_id, first_name, last_name, email FROM ' . TABLE_PREFIX . 'users WHERE state > ?', STATE_DELETED);
        
          if($users) {
            $companies = Companies::getIdNameMap();
            
            foreach($users as $user) {
              $company_id = (integer) $user['company_id'];
              
              Search::set($this->active_search_index, array(
                'class' => 'User', 
                'id' => $user['id'], 
                'context' => 'people:companies/' . $user['company_id'] . '/users/' . $user['id'], 
                'name' => Users::getUserDisplayName($user), 
                'email' => $user['email'], 
                'group_id' => $company_id, 
                'group' => $companies && isset($companies[$company_id]) && $companies[$company_id] ? $companies[$company_id] : null, 
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