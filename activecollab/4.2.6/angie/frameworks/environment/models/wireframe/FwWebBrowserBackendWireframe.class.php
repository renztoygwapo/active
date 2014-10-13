<?php

  /**
   * Web browser backend wireframe implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class FwWebBrowserBackendWireframe extends BackendWireframe {

    /**
     * Switch wireframe to list mode
     *
     * @var WireframeListMode
     */
    public $list_mode;

    /**
     * Construct backend wireframe
     *
     * @param Request $request
     * @return BackendWireframe
     */
    function __construct(Request $request) {
      parent::__construct($request);

      $this->list_mode = new WireframeListMode();
    } // __construct
  
  }