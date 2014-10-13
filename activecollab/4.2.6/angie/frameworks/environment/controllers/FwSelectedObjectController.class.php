<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Selected object controller implementation
   * 
   * Controller that implements foundation for controllers that do something to 
   * a particular object (like flag / unflag, subscribe / unsubscribe, list 
   * tasks etc), but require that object to be loaded
   *
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwSelectedObjectController extends BackendController {
    
    /**
     * Selected object
     *
     * @var ApplicationObject
     */
    protected $active_object;
    
    /**
     * Active user (if not explicitelly defined, logged user is ussed)
     *
     * @var User
     */
    protected $active_user;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $parent_type = $this->request->get('parent_type');
      $parent_id = $this->request->getId('parent_id');
      
      if($parent_type && $parent_id) {
        $this->active_object = new $parent_type($parent_id);
      } // if
      
      if($this->active_object->isLoaded()) {
        $this->smarty->assign('active_object', $this->active_object);
      } else {
        $this->response->notFound();
      } // if
      
      $user_id = $this->request->get('user_id');
      if($user_id !== null) {
        $user_id = (integer) $user_id;
        
        if($user_id) {
          $this->active_user = Users::findById($user_id);
        
          if(!($this->active_user instanceof User)) {
            $this->response->notFound();
          } // if
        } else {
          $this->active_user = new AnonymousUser($this->request->get('user_name'), $this->request->get('user_email'));
        } // if
      } else {
        $this->active_user = $this->logged_user;
      } // if
    } // __construct
    
  }