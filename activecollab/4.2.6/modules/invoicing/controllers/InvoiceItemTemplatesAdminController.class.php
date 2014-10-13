<?php

  // We need admin controller
  AngieApplication::useController('admin');

  /**
   * Invoice item templates controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class InvoiceItemTemplatesAdminController extends AdminController {
    
    /**
     * Currently active predefined invoice item
     *
     * @var InvoiceItemTemplate
     */
    protected $active_item_template = false;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->breadcrumbs->add('invoicing', lang('Invoicing'), Router::assemble('admin'));
      $this->wireframe->breadcrumbs->add('invoice_item_template', lang('Invoice Item Templates'), Router::assemble('admin_invoicing_items'));
      
      $item_template_id = $this->request->get('item_id');
      
      if($item_template_id) {
        $this->active_item_template = InvoiceItemTemplates::findById($item_template_id);
      } // if
      
      
      if (!($this->active_item_template instanceof InvoiceItemTemplate)) {
        $this->active_item_template = new InvoiceItemTemplate();
      } // if
            
      $this->smarty->assign(array(
        'active_item_template' => $this->active_item_template,
        'add_template_url' => Router::assemble('admin_invoicing_item_add'),
      ));
    } // __construct
    
    /**
     * Predefined items main page
     */
    function index() {
      $this->wireframe->actions->add('new_invoice_item', lang('New Invoice Item Template'), Router::assemble('admin_invoicing_item_add'), array(
        'onclick' => new FlyoutFormCallback(array(
        	'success_event' => 'invoice_item_template_created',
      		'width' => 500
         )), 
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),        
      ));
      
      $items_per_page = 50;
    	
    	if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(InvoiceItemTemplates::getSlice($items_per_page, $exclude, $timestamp));
    	} else {
    		$this->smarty->assign(array(
    		  'invoice_item_templates' => InvoiceItemTemplates::getSlice($items_per_page), 
    		  'items_per_page' => $items_per_page, 
    		  'total_items' => InvoiceItemTemplates::count(), 
    		));
    	} // if
    	
    } // index
    
    /**
     * Add Note Page
     *
     * @param void
     * @return void
     */
    function add() {
      $item_data = $this->request->post('item');
      if (!is_foreachable($item_data)) {
        $item_data = array(
          'quantity' => 1,
          'unit_cost' => 1
        );
      } // if
      
      $this->smarty->assign(array(
        'item_data' => $item_data
      ));
      
      if ($this->request->isSubmitted()) {
        try {
          if(isset($item_data['quantity']) && $item_data['quantity'] < 0) {
            throw new ValidationErrors(array(
              'quantity' => lang('Quantity cannot be a negative number')
            ));
          } // if

          $this->active_item_template->setAttributes($item_data);
          $this->active_item_template->save();
          $this->response->respondWithData($this->active_item_template, array('as' => 'invoice_item_templates'));
        } catch (Error $e) {
          $this->response->exception($e);
        }
      } // if
    } // add_note
    
    /**
     * Edit Note Page
     */
    function edit() {
      if($this->active_item_template->isNew()) {
        $this->response->notFound();
      } // if
      
      $item_data = $this->request->post('item');
      if (!is_foreachable($item_data)) {
        $item_data = array(
          'description' => $this->active_item_template->getDescription(),
          'unit_cost' => $this->active_item_template->getUnitCost(),
          'quantity' => $this->active_item_template->getQuantity(),
          'first_tax_rate_id' => $this->active_item_template->getFirstTaxRateId(),
          'second_tax_rate_id' => $this->active_item_template->getSecondTaxRateId(),
        );
      } // if
      
      $this->smarty->assign(array(
        'item_data' => $item_data,
      ));
      
      if ($this->request->isSubmitted()) {
        try {
          if(isset($item_data['quantity']) && $item_data['quantity'] < 0) {
            throw new ValidationErrors(array(
              'quantity' => lang('Quantity cannot be a negative number')
            ));
          } // if

          $this->active_item_template->setAttributes($item_data);
          $this->active_item_template->save();
          $this->response->respondWithData($this->active_item_template, array('as' => 'invoice_item_templates'));
        } catch (Error $e) {
          $this->response->exception($e);
        }//try
      } // if
      
    } // edit_note
    
    /**
     * Delete Invoice Item Template
     */
    function delete() {
      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if
      
      if ($this->active_item_template->isNew()) {
        $this->response->notFound();
      } // if
      
      try {
        $this->active_item_template->delete();
        $this->response->respondWithData($this->active_item_template,array('as' => 'invoice_item_templates'));
      } catch (Error $e) {
        $this->response->exception($e);
      }//try
    } // delete
    
    
  }