<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);
  
  /**
   * Source Repository controller
   * 
   * @package activeCollab.modules.source
   * @subpackage controllers
   */
  class RepositoryUsersController extends BackendController {
  
    /**
     * Active source repository
     *
     * @var SourceRepository
     */
    protected $active_source_repository = null;
    
    /**
     * Active project source repository
     * 
     * @var SourceRepository
     */
    protected $active_project_repository = null;
    
    /**
     * Active project
     * 
     * @var Project
     */
    protected $active_project = null;
        
    /**
     * Prepare controller
     * 
     * @param void
     * @return null
     */
    function __before() {
    	parent::__before();

    	$this->active_source_repository = SourceRepositories::findById($this->request->get('source_repository_id'));
    	if (!$this->active_source_repository instanceof SourceRepository) {
    		$this->response->notFound();
    	} // if
    	$add_user_url = Router::assemble('repository_user_add', array('source_repository_id' => $this->active_source_repository->getId()));
    	
    	// load project_repository
    	$project_slug = $this->request->get('project_slug');
    	if ($project_slug) {
	    	$this->active_project = Projects::findBySlug($project_slug);
	    	if (!$this->active_project instanceof Project) {
	    		$this->response->notFound();
	    	} // if
	    	
	    	$this->active_project_repository = ProjectSourceRepositories::findBySourceRepositoryId($this->active_project->getId(), $this->active_source_repository->getId());
	    	if (!$this->active_project_repository instanceof ProjectSourceRepository) {
	    		$this->response->notFound();
	    	} // if
	    	
	    	$add_user_url = Router::assemble('repository_user_add', array('source_repository_id' => $this->active_source_repository->getId(), 'project_slug' => $this->active_project->getSlug()));
    	} // if
    	    	
    	if ($this->active_project_repository && ($this->active_project_repository instanceof SourceRepository)) {
    		if (!$this->active_project_repository->canEdit($this->logged_user)) {
    			$this->response->forbidden();
    		} // if
    	} else {
        if(!ProjectSourceRepositories::canManage($this->logged_user, $this->active_project)) {
          $this->response->forbidden();
        } // if
    	} // if
    	    	
    	$this->smarty->assign(array(
    		'active_source_repository' => $this->active_source_repository,
    		'active_project_repository' => $this->active_project_repository,
    		'active_project' => $this->active_project,
    		'add_mapping_url' => $add_user_url 
    	));
    } // __construct
    
    
    /**
     * Manage repository users
     * 
     * @param void
     * @return null
     */
    function index() {    	    
      $source_users = SourceUsers::findBySourceRepository($this->active_source_repository);
      $distinct_repository_users = $this->active_source_repository->getDistinctUsers();
      
      // loop through already mapped users and remove them from repository users
      foreach ($source_users as $source_user) {
        $mapped_user_key = array_search($source_user->getRepositoryUser(), $distinct_repository_users);
      	if ($mapped_user_key !== false) {
      	  unset($distinct_repository_users[$mapped_user_key]);
      	} // if
      } // foreach
      
      $this->smarty->assign(array(
        'source_users' => $source_users,
        'repository_users' => $distinct_repository_users,
      ));
    } // index
    
    /**
     * Add mapping repository_user -> activecollab user
     * 
     * @param void
     * @return null 
     */
    function add() {
      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } //if
      
      if (!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } //if
            
      try {
      	$source_user = new SourceUser();
      	$source_user->setRepositoryId($this->active_source_repository->getId());
      	$source_user->setRepositoryUser($this->request->post('repository_user'));
      	$source_user->setUserId($this->request->post('user_id'));
      	$source_user->save();
      	$this->response->respondWithData($source_user, array('as' => 'source_user'));
      } catch (Exception $e) {
      	$this->response->exception($e);
      } // if
    } // repository_user_add
    
    /**
     * Delete user mapping
     * 
     * @param void
     * @return null
     */
    function delete() {    	
      if (!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if
      
      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if
      
      try {
      	$repository_user = $this->request->post('repository_user');
				$source_user = SourceUsers::findByRepositoryUser($repository_user, $this->active_source_repository->getId());
				if (!($source_user instanceof SourceUser)) {
					throw new Error(lang('Mapping not found'));
				} // if
				$source_user->delete();
				
				$this->response->respondWithData($source_user, array('as' => 'source_user'));
      } catch (Exception $e) {
      	$this->response->exception($e);
      } // try
    } // repository_user_delete    
    
  } // SourceRepositoryController