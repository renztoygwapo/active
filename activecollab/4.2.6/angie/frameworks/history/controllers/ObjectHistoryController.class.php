<?php

  // We need backend controller
  AngieApplication::useController('backend', SYSTEM_MODULE);

  /**
   * Object history controller
   *
   * @package angie.frameworks.history
   * @subpackage controllers
   */
  class ObjectHistoryController extends BackendController {
    
    /**
     * Active task
     *
     * @var Task
     */
    protected $active_object;
  
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      $object_id = $this->request->get('object_id');
      $object_class = $this->request->get('object_class');
      
      // we need to have both params
      if (!$object_id || !$object_class) {
      	$this->response->badRequest();
      } // if
      
      // object in question has to be subclass of application object
      if (!is_subclass_of($object_class, 'ApplicationObject')) {
      	$this->response->badRequest();
      } // if
      
      // try to get the instance of object in question
      try {
      	$this->active_object = new $object_class($object_id);
      } catch (Exception $e) {
      	$this->response->badRequest();
      } // if
      
      // check if active object supports history
      if (!($this->active_object instanceof IHistory)) {
      	$this->response->badRequest();
      } // if
      
      // check if class has isnew method and check if object is found
      if (method_exists($this->active_object, 'isNew') && $this->active_object->isNew()) {
      	$this->response->notFound();
      } // if
      
      // check if there is a method for permission to see this object
      if (method_exists($this->active_object, 'canView') && !$this->active_object->canView($this->logged_user)) {
      	$this->response->forbidden();
      } // if
      
      $this->response->assign('active_object', $this->active_object);
    } // __construct
    
    /**
     * Show tasks index page
     */
    function index() {
    	if (!$this->request->isAsyncCall()) {
    		$this->response->badRequest();
    	} // if
    	
			$this->smarty->assign(array(
				'modifications' => $this->active_object->history()->render($this->logged_user, $this->smarty)
			));
    } // index
  }