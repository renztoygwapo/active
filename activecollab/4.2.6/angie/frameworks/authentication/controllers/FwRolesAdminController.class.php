<?php

  // Build on top of administration controller
  AngieApplication::useController('admin', GLOBALIZATION_FRAMEWORK_INJECT_INTO);

  /**
   * Roles administration controller implementation
   *
   * @package angie.frameworks.authentication
   * @subpackage controller
   */
  abstract class FwRolesAdminController extends AdminController {

    /**
     * Execute before any of the controller actions
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->breadcrumbs->add('system_roles_admin', lang('Roles and Permissions'), Router::assemble('admin_roles'));
    } // __before
    
    /**
     * Display roles administration
     */
    function index() {
      $roles = array();
      $user_counts = Users::countByRoles();

      foreach(Users::getAvailableUserInstances() as $user) {
        $user_class = get_class($user);

        $roles[$user_class] = array(
          'name' => $user->getRoleName(),
          'users_count' => isset($user_counts[$user_class]) ? $user_counts[$user_class] : 0,
          'icon' => $user->getRoleIconUrl(),
          'url' => Router::assemble('admin_role', array('user_role_name' => Inflector::underscore($user_class))),
        );
      } // foreach

      $this->response->assign('roles', $roles);
    } // index

    /**
     * Show show role users
     */
    function view() {
      if($this->request->isAsyncCall()) {
        $role_name = $this->request->get('user_role_name');

        if($role_name) {
          $role_name = Inflector::camelize($role_name);
        } // if

        if(Users::isAvailableUserClass($role_name)) {
          $this->response->assign('users', Users::findByType($role_name, null, STATE_ARCHIVED));
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // view
    
  }