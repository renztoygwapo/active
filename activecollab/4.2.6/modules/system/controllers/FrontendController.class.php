<?php

	// Exted framework frontend controller
	AngieApplication::useController('fw_frontend', ENVIRONMENT_FRAMEWORK);

  /**
   * Frontend controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class FrontendController extends FwFrontendController {
    
    /**
     * Shared object controller delegate
     *
     * @var SharedObjectController
     */
    protected $shared_object_delegate;
    
    /**
     * Construct controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'frontend') {
        $this->shared_object_delegate = $this->__delegate('shared_object', SYSTEM_MODULE, 'default');
      } // if
    } // __construct
    
  }