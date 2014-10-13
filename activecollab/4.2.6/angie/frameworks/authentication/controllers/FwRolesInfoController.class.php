<?php

  // Build on top of API controller
  AngieApplication::useController('api', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Roles information controller
   * 
   * @package angie.frameworks.authentication
   * @subpackage controllers
   */
  abstract class FwRolesInfoController extends ApiController {
    
    /**
     * Actions that are available through API
     *
     * @var array
     */
    protected $api_actions = array('index', 'role');
    
    /**
     * Execute before any action is called
     */
    function __before() {
      parent::__before();

      if(!$this->canSeeRoleInfo($this->logged_user) && ($this->request->getAction() != 'index')) {
        $this->response->forbidden();
      } // if
    } // __before
  
    /**
     * List all available system roles
     */
    function index() {
      if($this->logged_user instanceof User) {
        $result = array();

        foreach(Users::getAvailableUserClasses() as $available_user_class) {
          $result[] = $available_user_class;
        } // if

        $this->response->respondWithData($result, array(
          'as' => 'roles',
        ));
      } else {
        $this->response->forbidden();
      } // if
    } // index
    
    /**
     * Show role details
     */
    function role() {
      $this->response->notFound(); // Legacy
    } // role
    
    /**
     * Return true if $user can see role information
     * 
     * @param IUser $user
     * @return boolean
     */
    protected function canSeeRoleInfo($user) {
      return $user instanceof User && $user->isAdministrator();
    } // canSeeRoleInfo
    
  }