<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_roles_info', AUTHENTICATION_FRAMEWORK);

  /**
   * Application level roles info controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class RolesInfoController extends FwRolesInfoController {
    
    /**
     * Actions that are available through API
     *
     * @var array
     */
    protected $api_actions = array('project_roles', 'project_role');
  
    /**
     * Show all available project roles
     */
    function project_roles() {
      if($this->logged_user->isPeopleManager() || $this->logged_user->isProjectManager()) {
        $this->response->respondWithData(ProjectRoles::find(), array(
          'as' => 'roles', 
        ));
      } else {
        $this->response->forbidden();
      } // if
    } // index
    
    /**
     * Show role details
     */
    function project_role() {
      $role_id = $this->request->getId('role_id');
      $role = $role_id ? ProjectRoles::findById($role_id) : null;

      if($role instanceof ProjectRole) {
        $this->response->respondWithData($role, array(
          'as' => 'role', 
        ));
      } else {
        $this->response->notFound();
      } // if
    } // role
    
    /**
     * Return true if $user can see role information
     * 
     * @param IUser $user
     * @return boolean
     */
    protected function canSeeRoleInfo($user) {
      return $user instanceof User && ($user->isPeopleManager() || $user->isProjectManager());
    } // canSeeRoleInfo
    
  }