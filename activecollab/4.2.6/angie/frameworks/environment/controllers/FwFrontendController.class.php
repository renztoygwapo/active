<?php

  /**
   * Abstract public controller that application can extend
   *
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwFrontendController extends ApplicationController {
    
    /**
     * By default, login is not required for this controller and controllers 
     * that inherit it
     *
     * @var boolean
     */
    protected $login_required = false;
    
    /**
     * Wireframe instance
     *
     * @var FrontendWireframe
     */
    protected $wireframe;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(is_file(CUSTOM_PATH . '/layouts/frontend.tpl')) {
        $this->setLayout(CUSTOM_PATH . '/layouts/frontend.tpl');
      } else {
        $this->setLayout(array(
          'module' => ENVIRONMENT_FRAMEWORK_INJECT_INTO,
          'layout' => 'frontend',
        ));
      } // if
    } // __construct
    
    /**
     * Public index page
     */
    function index() {
      
    } // index
    
    // ---------------------------------------------------
    //  Internals
    // ---------------------------------------------------
    
    /**
     * Return response interface
     * 
     * @return Response
     */
    protected function getResponseInstance() {
      if($this->request->isApiCall()) {
        return new ApiResponse($this, $this->request);
      } else {
        return new FrontendWebInterfaceResponse($this, $this->request);
      } // if
    } // getResponseInstance
    
    /**
     * Return wireframe instance for this controller
     *
     * @return FrontendWireframe
     */
    protected function getWireframeInstance() {
      return new FrontendWireframe($this->request);
    } // getWireframeInstance
    
  }