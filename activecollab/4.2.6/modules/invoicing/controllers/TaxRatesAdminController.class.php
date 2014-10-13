<?php

  // We need admin controller
  AngieApplication::useController('admin');
  
  /**
   * Tax Rates admin controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class TaxRatesAdminController extends AdminController {

    /**
     * Selected tax rate
     *
     * @var TaxRate
     */
    protected $active_tax_rate;

    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      $tax_rate_id = $this->request->getId('tax_rate_id');
      if($tax_rate_id) {
        $this->active_tax_rate = TaxRates::findById($tax_rate_id);
      } // if

      if(!($this->active_tax_rate instanceof TaxRate)) {
        $this->active_tax_rate = new TaxRate();
      } // if

      $add_tax_rate_url = Router::assemble('admin_tax_rates_add');

      $this->wireframe->breadcrumbs->add('tax_rates_admin', lang('Tax Rates'), Router::assemble('admin_tax_rates'));
      
      $this->smarty->assign(array(
        'active_tax_rate' => $this->active_tax_rate,
        'add_tax_rate_url' => $add_tax_rate_url,
      ));
    } // __construct

    /**
     * Show all available currencies
     */
    function index() {
      $this->wireframe->actions->add('new_tax_rate', lang('New Tax Rate'), Router::assemble('admin_tax_rates_add'), array(
        'onclick' => new FlyoutFormCallback(array(
        	'success_event' => 'tax_rate_created',
      		'width' => 500
         )), 
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),        
      ));
      
      $items_per_page = 50;
    	
    	if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(TaxRates::getSlice($items_per_page, $exclude, $timestamp));
    	} else {
    		$this->smarty->assign(array(
    		  'tax_rates'           => TaxRates::getSlice($items_per_page),
    		  'items_per_page'      => $items_per_page,
    		  'total_items'         => TaxRates::count(),
    		));
    	} // if
    	
    } // index

    /**
     * Create new tax rate
     */
    function add() {
      $tax_rate_data = $this->request->post('tax_rate');
      $this->smarty->assign('tax_rate_data', $tax_rate_data);

      if($this->request->isSubmitted()) {
        try {
          $this->active_tax_rate->setAttributes($tax_rate_data);
          $this->active_tax_rate->save();
          $this->response->respondWithData($this->active_tax_rate, array('as' => 'tax_rates'));
        } catch (Error $e) {
          $this->response->exception($e);
        }
       } // if
    } // add

    /**
     * Update existing route
     */
    function edit() {
      if($this->active_tax_rate->isNew()) {
        $this->response->notFound();
      } // if
      
      if(!$this->active_tax_rate->canEdit($this->logged_user)) {
        $this->response->forbidden();
      } // if

      $tax_rate_data = $this->request->post('tax_rate');
      if(!is_array($tax_rate_data)) {
        $tax_rate_data = array(
          'name' => $this->active_tax_rate->getName(),
          'percentage' => $this->active_tax_rate->getPercentage(),
        );
      } // if
      $this->smarty->assign('tax_rate_data', $tax_rate_data);

      if($this->request->isSubmitted()) {
        try {
          $this->active_tax_rate->setAttributes($tax_rate_data);
          $this->active_tax_rate->save();
          $this->response->respondWithData($this->active_tax_rate, array('as' => 'tax_rates'));
        } catch (Error $e) {
          $this->response->exception($e);
        }
      } // if
    } // edit

    /**
     * Delete existing tax rate
     */
    function delete() {
      if($this->active_tax_rate->isNew()) {
        $this->response->notFound();
      } // if
      
      if(!$this->active_tax_rate->canDelete($this->logged_user)) {
        $this->response->forbidden();
      } // if

      if($this->request->isSubmitted()) {
        try {
          $this->active_tax_rate->delete();
          $this->response->respondWithData($this->active_tax_rate, array('as' => 'tax_rates'));
        } catch (Error $e) {
          $this->response->exception($e);
        }
      } else {
        $this->response->badRequest();
      } // if
    } // delete

    /**
     * Set as default
     */
    function set_as_default() {
      if ($this->active_tax_rate->isNew()) {
        $this->response->notFound();
      } // if

      if (!$this->active_tax_rate->canModifyDefaultState($this->logged_user)) {
        $this->response->forbidden();
      } // if

      try {
        $default_tax_rate = TaxRates::getDefault();

        $this->active_tax_rate->setIsDefault(true);
        $this->active_tax_rate->save();

        if ($default_tax_rate instanceof TaxRate) {
          $default_tax_rate->setIsDefault(false);
          $default_tax_rate->save();
        } // if

        $this->response->respondWithData($this->active_tax_rate, array('as' => 'tax_rates'));
      } catch (Exception $e) {
        $this->response->badRequest();
      } // try
    } // set_as_default

    /**
     * Remove default
     */
    function remove_default() {
      if ($this->active_tax_rate->isNew()) {
        $this->response->notFound();
      } // if

      if (!$this->active_tax_rate->canModifyDefaultState($this->logged_user)) {
        $this->response->forbidden();
      } // if

      try {
        $this->active_tax_rate->setIsDefault(false);
        $this->active_tax_rate->save();

        $this->response->respondWithData($this->active_tax_rate, array('as' => 'tax_rates'));
      } catch (Exception $e) {
        $this->response->badRequest();
      } // try
    } // remove_default

  }