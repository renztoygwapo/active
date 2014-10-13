<?php

  // Build on top of source module
  AngieApplication::useController('source_admin', SOURCE_MODULE);

  /**
   * Mercurial administration controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class MercurialSourceAdminController extends SourceAdminController {
    
    /**
     * Selected Mercurial repository instance
     *
     * @var MercurialRepository
     */
    protected $active_repository;
  
    /**
     * Execute before every action
     */
    function __before() {
      parent::__before();
      
      $repository_id = $this->request->get('source_repository_id');
      if($repository_id) {
        $this->active_repository = SourceRepositories::findById($repository_id);
        
        if($this->active_repository instanceof SourceRepository && !($this->active_repository instanceof MercurialRepository)) {
          $this->httpError(HTTP_ERR_CONFLICT);
        } // if
      } // if
      
      if(!($this->active_repository instanceof MercurialRepository)) {
        $this->active_repository = new MercurialRepository();
      } // if
      
      $this->smarty->assign('active_repository', $this->active_repository);
    } // __before
    
    /**
     * Show and process Mercurial settings page
     */
    function settings() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if ($this->logged_user->isAdministrator()) {
          $source_data = $this->request->post('source', array(
            'mercurial_path' => ConfigOptions::getValue('source_mercurial_path'),
          ));
          
          if ($this->request->isSubmitted()) {
            try {
              $mercurial_path = array_var($source_data, 'mercurial_path', null);
              $mercurial_path = $mercurial_path ? with_slash($mercurial_path) : null;
              ConfigOptions::setValue('source_mercurial_path', $mercurial_path);
              
              $this->flash->success("Source settings successfully saved");
              $this->response->respondWithData($source_data, array('as' => 'mercurial_source_data'));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } // if
          
          $this->smarty->assign(array(
            'test_mercurial_url' => Router::assemble('admin_source_mercurial_test'),
            'source_data' => $source_data,
          	'settings_source_url' => Router::assemble('admin_source_mercurial_settings'),
          ));
        } else {
          $this->response->forbidden();
        } //if
      } else {
        $this->response->badRequest();
      } //if
    } // settings
    
    /**
     * Add new repository
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if ($this->active_repository->canAdd($this->logged_user)) {
          if ($this->request->isSubmitted()) {
            try {
              $repository_data = $this->request->post('repository');
              DB::beginWork('Begin adding new repository to activeCollab @'. __CLASS__);
              
              $this->active_repository->setAttributes($repository_data);
              $this->active_repository->setType("MercurialRepository");
              $this->active_repository->setCreatedBy($this->logged_user);
              
              if (!$this->active_repository->engineIsUsable()) {
              	throw new Error(lang('Please configure the Mercurial extension prior to adding a repository'));
              } // if
              
              $result = $this->active_repository->testRepositoryConnection();
              // check validity of repository credentials
              if ($result !== true) {
                if ($result === false) {
                  $message = 'Please check URL or login parameters.';
                } else {
                  $message = $result;
                } //if
                throw new Error(lang('Failed to connect to repository: :message', array('message'=>$message)));
              } //if
              
              $this->active_repository->save();
              DB::commit('Successfully added new repository to activeCollab @'. __CLASS__);
              $this->response->respondWithData($this->active_repository, array(
                'as' => 'repository', 
                'detailed' => true, 
              ));
            } catch (Exception $e) {
          		DB::rollback('Failed to add repository @'. __CLASS__);
          		$this->response->exception($e);
          	} // try
          }//if
          $this->smarty->assign(array(
          	'repository_add_url' => Router::assemble('admin_source_mercurial_repositories_add'),
          	'repository_data' => $repository_data,
          	'disable_url' => false,
          	'aid_engine' => '',
          	'repository_test_connection_url' => Router::assemble('admin_source_mercurial_repository_test_connection')
          ));
        } else {
          $this->response->forbidden();
        } //if
      } else {
        $this->response->badRequest();
      } 
    } // add
    
    /**
     * Update selected repository
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_repository->isLoaded()) {
          if($this->active_repository->canEdit($this->logged_user)) {
            $repository_data = $this->request->post('repository', array(
              'name' => $this->active_repository->getName(),
              'repository_path_url'	=> $this->active_repository->getRepositoryPathUrl(),
              'username' => $this->active_repository->getUsername(),
              'password' => $this->active_repository->getPassword(),
              'updatetype' => $this->active_repository->getUpdateType(),
            ));
            
            if ($this->request->isSubmitted()) {
              try {
                DB::beginWork('Begin repository editing @'.__CLASS__);
                
                $this->active_repository->setAttributes($repository_data);
                $this->active_repository->setUpdatedBy($this->logged_user);
                
                if (!$this->active_repository->engineIsUsable()) {
                	throw new Error(lang('Please configure the Mercurial extension prior to editing a repository'));
                } // if
                
                $result = $this->active_repository->testRepositoryConnection();
                if (!$result) {
                	throw new Error(lang('Failed to connect to repository: Please check URL and login parameters.'));
                } // if
                
                $this->active_repository->save();
                DB::commit('Successfully edited repository @ '.__CLASS__);
                
                $this->response->respondWithData($this->active_repository, array(
                  'as' => 'repository', 
                  'detailed' => true, 
                ));
              } catch (Exception $e) {
            		DB::rollback('Failed to edit repository @ '.__CLASS__);
            		$this->response->exception($e);
          	  } // try
            } //if
            
            $this->smarty->assign(array(
            	'repository_edit_url' => $this->active_repository->getEditUrl(),
            	'repository_data' => $repository_data,
            	'disable_url' => true,
            	'aid_engine' => '',
            	'repository_test_connection_url' => Router::assemble('admin_source_mercurial_repository_test_connection',array('async' => '1'))
            ));
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // edit
    
    /**
     * Delete selected repository
     */
    function delete() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_repository->isLoaded()) {
          if($this->active_repository->canDelete($this->logged_user)) {
            try {
              $this->active_repository->delete();
              $this->response->respondWithData($this->active_repository, array(
                'as' => 'repository', 
                'detailed' => true, 
              ));
            } catch (Exception $e) {
              $this->response->exception($e);
            } // if
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // delete
    
    /**
     * Test repository connection (async)
     */
    function test_repository_connection() {
      if ($this->request->isAsyncCall()) {
        if (!(array_var($_GET, 'url'))) {
        	die(lang('Please fill in all the connection parameters'));     
        } //if
        
        $this->active_repository->setRepositoryPathUrl(array_var($_GET, 'url'));
        $this->active_repository->setType(array_var($_GET, 'engine'));
        
        if (!$this->active_repository->loadEngine()) {
          die(lang('Failed to load repository engine'));
        }//if
        
        if (($error = $this->active_repository->loadEngine()) !== true) {
          die($error);
        } // if
        
        $result = $this->active_repository->testRepositoryConnection();
        if ($result !== true) {
          die('Please check URL or login parameters.');
        } else {
          die('ok');
        } // if
      } else {
        $this->response->badRequest();
      }//if
    } // test_repository_connection
    
    /**
     * This will return in which projects is repository used
     */
    function usage() {
      $source_repository = SourceRepositories::findById($this->request->get('source_repository_id'));
      $projects = $source_repository->getProjects();
      
      $this->smarty->assign(array(
        'source_repository'	=> $source_repository,
        'projects' => $projects,
      ));
    } //repository_usage
    
    /**
     * Ajax that will return response from command line
     */
    function test_mercurial() {
      if ($this->request->isAsyncCall()) {
        $path = array_var($_GET, 'mercurial_path', null);
        $check_executable = MercurialRepositoryEngine::executableExists($path);
        
        echo $check_executable === true ? 'true' : $check_executable;
        die();
      } else {
        $this->response->badRequest();
      } //if
    } // test_mercurial
    
  }