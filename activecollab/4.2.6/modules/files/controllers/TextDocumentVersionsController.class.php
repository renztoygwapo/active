<?php

  // Build on top of text documents controller
  AngieApplication::useController('text_documents', FILES_MODULE);

  /**
   * Text document versions controller
   *
   * @package activeCollab.modules.files
   * @subpackage controllers
   */
	class TextDocumentVersionsController extends TextDocumentsController {
		
		/**
		 * Active text document version
		 * 
		 * @var TextDocumentVersion
		 */
		protected $active_text_document_version;
		
		/**
		 * Prepare controller
		 */
		function __before() {
			parent::__before();
			
			if($this->active_asset instanceof TextDocument && $this->active_asset->isLoaded()) {
			  $text_version_num = $this->request->get('version_num');
			  $this->active_text_document_version = $this->active_asset->versions()->getVersion($text_version_num);
			  
			  if ($this->active_text_document_version instanceof TextDocumentVersion) {
  				$this->response->assign('active_text_document_version', $this->active_text_document_version);
			  } else {
			    $this->response->notFound();
  			} // if
			} else {
			  $this->response->notFound();
			} // if
		} // before
		
		/**
		 * View document version
		 */
		function view() {
		  if($this->active_text_document_version->canView($this->logged_user)) {
		  	if ($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
		  		$this->wireframe->setPageObject($this->active_text_document_version, $this->logged_user);
		  		$this->render();
		  	} else {
		  		$this->__forward('assets_list', get_view_path('index', 'assets', FILES_MODULE));
		  	} // if
		  } else {
		    $this->response->forbidden();
		  } // if			
		} // view
		
		/**
		 * Delete document version
		 */
		function delete() {
		  if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
		    if($this->active_text_document_version->canDelete($this->logged_user)) {
		      try {
    				$this->active_text_document_version->delete();
    				
    				$this->response->respondWithData($this->active_text_document_version, array('as' => 'text_document_version'));
    			} catch (Exception $e) {
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