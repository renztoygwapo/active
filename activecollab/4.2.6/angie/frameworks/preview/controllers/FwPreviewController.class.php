<?php

  /**
   * Framework level preview controller implementation
   *
   * @package angie.frameworks.preview
   * @subpackage controller
   */
  class FwPreviewController extends Controller {

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

      if($this->active_object instanceof IPreview) {
        if($this->active_object->isNew()) {
          $this->response->notFound();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // __before

    /**
     * Preview file content
     */
    function preview_content() {
      if ($this->active_object->canView($this->logged_user)) {
        $this->response->assign('active_object', $this->active_object);
      } else {
        $this->response->forbidden();
      } // if
    } // preview_content

  }