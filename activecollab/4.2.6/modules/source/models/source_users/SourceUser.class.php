<?php

  /**
   * SourceUser class
   * 
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class SourceUser extends BaseSourceUser {
    
    /**
     * System user object
     *
     * @var User
     */
    public $system_user = null;
    
    /**
     * Set user object from the system
     *
     * @param void
     * @return null
     */
    function setSystemUser() {
      if (is_null($this->system_user)) {
        $this->system_user = Users::findById($this->getUserId());
      } // if
    } // getSystemUser
    
    /**
     * Get delete URL for repository user
     *
     * @param Project $project
     * @return string
     */
    function getDeleteUrl(Project $active_project = null) {
    	if ($active_project instanceof Project) {
    		return Router::assemble('repository_user_delete', array('source_repository_id' => $this->getRepositoryId(), 'project_slug' => $active_project->getSlug()));
    	} else {
      	return Router::assemble('repository_user_delete', array('source_repository_id' => $this->getRepositoryId()));
    	} // if
    } // getDeleteUrl
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('user_id')) {
        $errors->addError(lang('User ID is not provided'), 'user_id');
      } // if
      
      parent::validate($errors, true);
    } // validate
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);
            
			$repository = SourceRepositories::findById($this->getRepositoryId());
			$user = Users::findById($this->getUserId());
			
			$result['user'] = $user->describe($user, false, $for_interface);
			$result['repository_user'] = $this->getRepositoryUser();
			$result['repository'] = $repository->describe($user, false, $for_interface);
      $result['urls']['delete'] = $this->getDeleteUrl();
      
      unset($result['name']);
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
  }