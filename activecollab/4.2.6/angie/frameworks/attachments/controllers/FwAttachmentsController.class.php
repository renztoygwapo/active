<?php

  // Build on top of selected object framework
  AngieApplication::useController('selected_object', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level attachments controller
   *
   * @package angie.frameworks.attachments
   * @subpackage controllers
   */
  abstract class FwAttachmentsController extends Controller {
    
    /**
     * Parent object instance
     *
     * @var IAttachments
     */
    protected $active_object;
    
    /**
     * Selected attachment
     *
     * @var Attachment
     */
    protected $active_attachment;
    
    /**
     * API actions
     *
     * @var array
     */
    protected $api_actions = array('view');
    
    /**
     * State controller deleage
     *
     * @var StateController
     */
    protected $state_delegate;
    
    /**
     * Download controller delegate
     *
     * @var DownloadController
     */
    protected $download_delegate;

    /**
     * Preview controller delegate
     *
     * @var PreviewController
     */
    protected $preview_delegate;
    
    /**
     * Construct attachments controller
     *
     * @param mixed $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
      
      $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, "{$context}_attachment");
      $this->download_delegate = $this->__delegate('download', DOWNLOAD_FRAMEWORK_INJECT_INTO, "{$context}_attachment");
      $this->preview_delegate = $this->__delegate('attachments_preview', ATTACHMENTS_FRAMEWORK_INJECT_INTO, "{$context}_attachment");
    } // __construct
    
    /**
     * Execute code before action
     */
    function __before() {
      if($this->active_object instanceof IAttachments) {
        $attachment_id = $this->request->getId('attachment_id');
        if($attachment_id) {
          $this->active_attachment = Attachments::findById($attachment_id);
        } // if

        if($this->active_attachment instanceof Attachment) {
          if(!$this->active_object->is($this->active_attachment->getParent())) {
            $this->response->notFound();
          } // if
        } else {
          $this->active_attachment = $this->active_object->attachments()->newAttachment();
        } // if
        
        $this->smarty->assign(array(
          'active_object' => $this->active_object, 
          'active_attachment' => $this->active_attachment,
        ));
        
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_attachment, 
        ));
        
        $this->download_delegate->__setProperties(array(
          'active_object' => &$this->active_attachment, 
        ));

        $this->preview_delegate->__setProperties(array(
          'active_object' => &$this->active_attachment,
        ));
      } else {
        $this->response->notFound();
      } // if
    } // __before
    
    /**
     * List parent attachments
     */
    function attachments() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData($this->active_object->attachments()->get($this->logged_user), array(
          'as' => 'attachments', 
          'detailed' => true, 
        ));
      } else {
        if($this->request->isMobileDevice()) {
          $this->smarty->assign('attachments', $this->active_object->attachments()->get($this->logged_user));
        } else {
          $this->response->notFound();
        } // if
      } // if
    } // attachments
    
    /**
     * View single attachment (basically, load it and forward it to the user)
     */
    function view_attachment() {
      if($this->active_attachment->isNew()) {
        $this->response->notFound();
      } // if
      
      if(!$this->active_attachment->canView($this->logged_user)) {
        $this->response->forbidden();
      } // if
      
      $this->active_attachment->send($this->request->get('disposition', 'attachment') == 'attachment', true);
    } // view_attachment

  }