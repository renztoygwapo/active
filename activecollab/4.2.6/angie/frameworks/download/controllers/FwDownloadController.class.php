<?php

  /**
   * Framework level download controller implementation
   *
   * @package angie.frameworks.download
   * @subpackage controller
   */
  class FwDownloadController extends Controller {
    
    /**
     * Active parent object
     *
     * @var IDownload
     */
    protected $active_object;
    
    /**
     * Initialize controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_object instanceof IDownload) {
        if($this->active_object->isNew()) {
          $this->response->notFound();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // __before
    
    /**
     * Download file content
     */
    function download_content() {
      if($this->active_object->canView($this->logged_user)) {
        $this->active_object->download()->send($this->request->get('force'));
      } else {
        $this->response->forbidden();
      } // if
    } // download_content
    
  }