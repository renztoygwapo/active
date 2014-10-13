<?php

  // We need admin controller
  AngieApplication::useController('admin');

  /**
   * Invoice note templates controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class InvoiceNoteTemplatesAdminController extends AdminController {
    
    /**
     * Active Invoice note
     *
     * @var InvoiceNoteTemplate
     */
    protected $active_note;
           
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->breadcrumbs->add('invoicing', lang('Invoicing'), Router::assemble('admin'));
      $this->wireframe->breadcrumbs->add('invoice_notes_templates', lang('Invoice Note Templates'), Router::assemble('admin_invoicing_notes'));
      
      $this->active_note = InvoiceNoteTemplates::findById($this->request->get('note_id'));
      if (!($this->active_note instanceof InvoiceNoteTemplate)) {
        $this->active_note = new InvoiceNoteTemplate();
      } // if
            
      $this->smarty->assign(array(
        'active_note' => $this->active_note,
        'add_note_url' => Router::assemble('admin_invoicing_notes_add'),
      ));
    } // __construct
    
    /**
     * Predefined items main page
     */
    function index() {
      $this->wireframe->actions->add('new_invoice_note_template', lang('New Note Template'), Router::assemble('admin_invoicing_notes_add'), array(
        'onclick' => new FlyoutFormCallback(array(
            'success_event' => 'invoice_note_template_created',
            'width' => 580
          )), 
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),        
      ));
      
      $items_per_page = 50;
    	
    	if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(InvoiceNoteTemplates::getSlice($items_per_page, $exclude, $timestamp));
    	} else {
    		$this->smarty->assign(array(
    		  'invoice_note_templates' => InvoiceNoteTemplates::getSlice($items_per_page), 
    		  'items_per_page' => $items_per_page, 
    		  'total_items' => InvoiceNoteTemplates::count(), 
    		));
    	} // if
      
    } // index
    
    /**
     * Add Note Page
     */
    function add() {
      $note_data = $this->request->post('note');
      if (!is_foreachable($note_data)) {
        $note_data = array();
      } // if
      
      $this->smarty->assign(array(
        'note_data' => $note_data
      ));
      
      if ($this->request->isSubmitted()) {
        try {
          $this->active_note->setAttributes($note_data);
          $this->active_note->save();
          $this->response->respondWithData($this->active_note,array('as' => 'invoice_notes'));
        } catch (Error $e) {
          $this->response->exception($e);
        }//try
      } // if
    } // add_note
    
    /**
     * Edit Note Page
     */
    function edit() {
      if ($this->active_note->isNew()) {
        $this->response->notFound();
      } // if
      
      $note_data = $this->request->post('note');
      if (!is_foreachable($note_data)) {
        $note_data = array(
          'name' => $this->active_note->getName(),
          'content' => $this->active_note->getContent()
        );
      } // if
      
      $this->smarty->assign(array(
        'note_data' => $note_data
      ));
      
      if ($this->request->isSubmitted()) {
        try {
          $this->active_note->setAttributes($note_data);
          $this->active_note->save();
          $this->response->respondWithData($this->active_note, array('as' => 'invoice_notes'));
        } catch (Error $e) {
          $this->response->exception($e);
        }
         
      } // if
    } // edit_note
    
    /**
     * Delete Note Page
     */
    function delete() {
      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if
      
      if ($this->active_note->isNew()) {
        $this->response->notFound();
      } // if
      
      try {
        $this->active_note->delete();
        $this->response->respondWithData($this->active_note,array('as' => 'invoice_notes'));
      } catch (Error $e) {
        $this->response->exception($e);
      }
    } // delete_note

    /**
     * Set invoice note template as default
     */
    function set_as_default() {
      if ($this->active_note->isNew()) {
        $this->response->notFound();
      } // if

      if (!$this->active_note->canEdit($this->logged_user)) {
        $this->response->forbidden();
      } // if

      try {
        $default_note = InvoiceNoteTemplates::getDefault();

        $this->active_note->setIsDefault(true);
        $this->active_note->save();

        if ($default_note instanceof InvoiceNoteTemplate) {
          $default_note->setIsDefault(false);
          $default_note->save();
        } // if

        $this->response->respondWithData($this->active_note, array('as' => 'invoice_note_template'));
      } catch (Exception $e) {
        $this->response->exception($e);
      } // if
    } // set_as_default

    /**
     * Remove invoice note template as default
     */
    function remove_default() {
      if ($this->active_note->isNew()) {
        $this->response->notFound();
      } // if

      if (!$this->active_note->canEdit($this->logged_user)) {
        $this->response->forbidden();
      } // if

      try {
        $this->active_note->setIsDefault(false);
        $this->active_note->save();

        $this->response->respondWithData($this->active_note, array('as' => 'invoice_note_template'));
      } catch (Exception $e) {
        $this->response->exception($e);
      } // if
    } // remove_default
  }