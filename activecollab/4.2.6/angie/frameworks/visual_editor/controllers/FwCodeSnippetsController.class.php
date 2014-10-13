<?php

  // Build on top backend controller
  AngieApplication::useController('backend', AUTHENTICATION_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level code snippets controller
   *
   * @package angie.frameworks.visual_editor
   * @subpackage controllers
   */
  abstract class FwCodeSnippetsController extends BackendController {
    
    /**
     * Selected category
     *
     * @var CodeSnippet
     */
    protected $active_code_snippet;
    
    /**
     * Execute code before action has been executed
     */
    function __before() {
      parent::__before();
      
      $snippet_id = $this->request->get('code_snippet_id');
      if($snippet_id) {      	
      	$this->active_code_snippet = CodeSnippets::findById($snippet_id);
      } // if
      
      if(!($this->active_code_snippet instanceof CodeSnippet)) {
        $this->active_code_snippet = new CodeSnippet();
      } // if
      
      $this->smarty->assign('active_code_snippet', $this->active_code_snippet);
    } // __before
    
    /**
     * View code snippet
     */
    function view() {
    	if ($this->request->isAsyncCall() && !$this->request->get('preview')) {
        $this->response->respondWithData($this->active_code_snippet);
      } // if
    } // view

    /**
     * Show preview for submitted code
     */
    function preview() {
      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if

      $this->response->assign(array(
        'code' => $this->request->post('code'),
        'syntax' => $this->request->post('syntax')
      ));
    } // preview submitted code
    
    /**
     * Add code snippet
     */
    function add() {
			if (CodeSnippets::canAdd($this->logged_user)) {
        $code_snippet_data = $this->request->post('code_snippet', array());

        $this->smarty->assign(array(
          'add_code_snippet_url' => Router::assemble('code_snippets_add'),
          'code_snippet_data' => $code_snippet_data,
        ));

        if ($this->request->isSubmitted()) {
          try {
            $this->active_code_snippet->setAttributes($code_snippet_data);
            $this->active_code_snippet->save();
            $this->response->respondWithData($this->active_code_snippet);
          } catch (Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
				$this->response->forbidden();
			} // if
    } // add
    
    /**
     * Edit code snippet
     */
    function edit() {
    	if ($this->active_code_snippet->isNew()) {
    		$this->response->notFound();
    	} // if
    	
    	if (!$this->active_code_snippet->canEdit($this->logged_user)) {
    		$this->response->forbidden();	
    	} // if
    	
    	$code_snippet_data = $this->request->post('code_snippet', array(
    		'syntax'	=> $this->active_code_snippet->getSyntax(),
    		'body'	=> $this->active_code_snippet->getBody()
    	));
    	
    	$this->smarty->assign(array(
    		'code_snippet_data' => $code_snippet_data,
    	));
    	
    	if ($this->request->isSubmitted()) {
    		try {
    			$this->active_code_snippet->setAttributes($code_snippet_data);
    			$this->active_code_snippet->save();
    			$this->response->respondWithData($this->active_code_snippet);
    		} catch (Exception $e) {
    			$this->response->exception($e);
    		} // try
    	} // if
    } // edit
    
    /**
     * Not implemented
     */
    function delete() {
      $this->response->notFound();
    } // delete
    
  }