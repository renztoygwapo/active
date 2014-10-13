<?php

  /**
   * Invoice based on controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class InvoiceBasedOnController extends Controller {
    
    /**
     * Selected object
     *
     * @var Object|IInvoiceBasedOn
     */
    protected $active_object;
    
    /**
     * Active invoice object
     * 
     * @var Invoice
     */
    protected $active_invoice;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $this->response->assign(array(
        'active_object' => $this->active_object,
        'is_based_on_quote' => $this->active_object instanceof Quote,
        'is_based_on_tracking_report' => $this->active_object instanceof TrackingReport,
        'on_invoice_based_on' => ConfigOptions::getValue('on_invoice_based_on'),
      	'invoice_notes' => InvoiceNoteTemplates::find(),
        'js_company_details_url' => Router::assemble('people_company_details'),
      ));
    } // __before
    
    /**
     * Create invoice based on active_object
     */
    function add_invoice() {
      $this->wireframe->actions->add('change_description_formats', lang('Change Description Formats'), Router::assemble('invoicing_settings_change_description_formats'), array(
        'onclick' => new FlyoutFormCallback('', array(
          'title' => lang('Change Formats'),
          'width' => 700,
          'success_message' => lang('Settings updated'),
          'success_event' => 'description_formats_updated',
        ))
      ));

      $invoice_data = $this->request->post('invoice_data');

      if(empty($invoice_data)) {
        $invoice_data = array();
      } // if

      $preview_items_url = $this->active_object->invoice()->getPreviewItemsUrl();

      if($this->active_object instanceof Project || $this->active_object instanceof Task) {
        if(!isset($invoice_data['company_id'])) {
          $invoice_data['company_id'] = $this->active_object->getCompany()->getId();
        } // if

        if(!isset($invoice_data['currency_id'])) {
          $invoice_data['currency_id'] = $this->active_object instanceof Task ? $this->active_object->getProject()->getCurrencyId() : $this->active_object->getCurrencyId();
        } // if
      } // if

      if($this->active_object instanceof Quote) {
        if(!isset($invoice_data['note'])) {
          $invoice_data['note'] = $this->active_object->getNote();
        } // if

        if($this->active_object->getCompany() instanceof Company && !isset($invoice_data['company_id'])) {
          $invoice_data['company_id'] = $this->active_object->getCompany()->getId();
        } // if

        if($this->active_object->getProject() instanceof Project && !isset($invoice_data['project_id'])) {
          $invoice_data['project_id'] = $this->active_object->getProject()->getId();
        } // if
      } // if

      if($this->active_object instanceof TrackingReport) {
        $filter_data = $this->request->get('filter') ? $this->request->get('filter') : unserialize($this->request->post('filter_data'));

        if($filter_data) {
          foreach($filter_data as $k => $v) {
            $preview_items_url = extend_url($preview_items_url, array("filter[$k]" => $v));
          } // foreach
        } // if

        $this->active_object->setAttributes($filter_data);

        if(!isset($invoice_data['company_id'])) {
          $report_company = $this->active_object->getCompany($this->logged_user);

          if($report_company) {
            $invoice_data['company_id'] = $report_company->getId();
          } // if
        } // if
        
        $this->response->assign('filter_data', serialize($filter_data));
      } // if

      // Set default PO value object have Purcahse Order number in custom fields
      if($this->active_object instanceof ICustomFields) {
        $custom_field_name = $this->active_object->customFields()->getFieldNameForLabel(array(
          'po',
          'purchase order',
          'purchase order number'
        ));

        if($custom_field_name && !isset($invoice_data['purchase_order_number'])) {
          $invoice_data['purchase_order_number'] = $this->active_object->customFields()->getValue($custom_field_name);
        } // if
      } // if

      if(!$this->active_object instanceof Quote && !isset($invoice_data['project_id'])) {
        list($time_records, $expenses, $project) = $this->active_object->invoice()->queryRecords($this->logged_user);
        $invoice_data['project_id'] = $project instanceof Project ? $project->getId() : $project;
      } // if


      $this->response->assign(array(
        'invoice_data' => $invoice_data,
        'preview_items_url' => $preview_items_url,
      )); 
      
      if($this->request->isSubmitted()) {
        if($this->active_object->invoice()->canAdd($this->logged_user)) {
          try {
            $errors = new ValidationErrors();

            $company = isset($invoice_data['company_id']) && $invoice_data['company_id'] ? Companies::findById($invoice_data['company_id']) : null;
            if(empty($company)) {
              $errors->addError(lang('Client is required'), 'company_id');
            } // if

            $company_address = isset($invoice_data['company_address']) && $invoice_data['company_address'] ? trim($invoice_data['company_address']) : '';
            if(empty($company_address)) {
              $errors->addError(lang('Client address is required'), 'company_address');
            } // if

            if($errors->hasErrors()) {
              throw $errors;
            } // if

            if(empty($invoice_data['currency_id'])) {
              $invoice_data['currency_id'] = Currencies::getDefault()->getId();
            } // if

            $allow_payments = (boolean) ConfigOptions::getValue('allow_payments');
            if($allow_payments && !isset($invoice_data['allow_payments'])) {
              $invoice_data['allow_payments'] = -1;
            } // if
            
            $invoice = $this->active_object->invoice()->create($company, $company_address, $invoice_data, $this->logged_user);
            
            $this->response->respondWithData($invoice, array(
  	          'as' => 'invoice', 
  	        ));  
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->badRequest();
        } // if
      } // if
    } // add_invoice

    /**
     * Preview items based on provided data
     */
    function preview_items() {
      if($this->request->isSubmitted() && $this->request->isAsyncCall()) {
        if($this->active_object instanceof TrackingReport && is_array($this->request->get('filter'))) {
          $this->active_object->setAttributes($this->request->get('filter'));
          $this->active_object->setSumByUser(false);
        } // if

        $this->response->respondWithMap($this->active_object->invoice()->previewItems($this->request->post('invoice_data'), $this->logged_user));
      } else {
        $this->response->badRequest();
      } // if
    } // preview_items
     
  }