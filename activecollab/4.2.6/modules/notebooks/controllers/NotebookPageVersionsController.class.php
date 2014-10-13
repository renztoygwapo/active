<?php

	// Extend notebook pages controller  
	AngieApplication::useController('notebook_pages', NOTEBOOKS_MODULE);

  /**
   * Notebook page versions controller
   *
   * @package activeCollab.modules.notebooks
   * @subpackage controllers
   */
  class NotebookPageVersionsController extends NotebookPagesController {
    
    /**
     * Active module
     *
     * @var string
     */
    protected $active_module = NOTEBOOKS_MODULE;
    
    /**
     * Selected notebook page version
     *
     * @var NotebookPageVersion
     */
    protected $active_notebook_page_version;
    
    /**
     * Construct notebook page versions controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_notebook_page->isLoaded()) {
        $version = $this->request->getId('version');
        if($version) {
          $this->active_notebook_page_version = NotebookPageVersions::findByPageAndVersion($this->active_notebook_page, $version);
        } // if
        
        if($this->active_notebook_page_version instanceof NotebookPageVersion) {
          $this->smarty->assign('active_notebook_page_version', $this->active_notebook_page_version);
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // __before

    /**
     * View version
     */
    function view() {
      if($this->request->isAsyncCall()) {
        if($this->active_notebook_page_version->isLoaded()) {
          if($this->active_notebook_page_version->canView($this->logged_user)) {
            $this->response->respondWithContent($this->active_notebook_page_version, array(
              'die' => false
            ));
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // view
    
    /**
     * Delete version
     */
    function delete() {
      if($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        if($this->active_notebook_page_version->canDelete($this->logged_user)) {
          try {
            $this->active_notebook_page_version->delete();
            $this->response->respondWithData($this->active_notebook_page_version);
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // delete
    
  }