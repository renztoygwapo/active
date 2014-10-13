<?php

  // Build on top of administration controller
  AngieApplication::useController('admin', GLOBALIZATION_FRAMEWORK_INJECT_INTO);

  /**
   * Currencies administration controller
   * 
   * @package angie.frameworks.globalization
   * @subpackage controllers
   */
  class FwCurrenciesAdminController extends AdminController {
  
    /**
     * Selected currency
     *
     * @var Currency
     */
    protected $active_currency;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $currency_id = $this->request->getId('currency_id');
      if($currency_id) {
        $this->active_currency = Currencies::findById($currency_id);
      } // if
      
      $this->wireframe->breadcrumbs->add('currencies', lang('Currencies'), Router::assemble('admin_currencies'));
      
      if($this->active_currency instanceof Currency) {
        $this->wireframe->breadcrumbs->add('currency', $this->active_currency->getName(), $this->active_currency->getViewUrl());
      } else {
        $this->active_currency = new Currency();
      } // if
      
      $this->smarty->assign('active_currency', $this->active_currency);
    } // __construct
    
    /**
     * Show all available currencies
     */
    function index() {
      $this->wireframe->actions->add('new_currency', lang('New Currency'), Router::assemble('admin_currencies_add'), array(
        'onclick' => new FlyoutFormCallback('currency_created', array(
          'width'   => 'narrow'
        )),
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
      ));
      
      $currencies_per_page = 50;
    	
    	if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(Currencies::getSlice($currencies_per_page, $exclude, $timestamp));
    	} else {
    	  JSON::encode(Currencies::getSlice($currencies_per_page));
    	  
    	  $this->smarty->assign(array(
    		  'currencies' => Currencies::getSlice($currencies_per_page), 
    			'currencies_per_page' => $currencies_per_page, 
    		  'total_currencies' => Currencies::count(), 
    		));
    	} // if
    } // index
    
    /**
     * Display currency details
     */
    function view() {
      if($this->request->isApiCall()) {
        if($this->active_currency->isLoaded()) {
          if($this->active_currency->canView($this->logged_user)) {
            $this->response->respondWithData($this->active_currency, array('as' => 'currency'));
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
     * Create new currency
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if(Currencies::canAdd($this->logged_user)) {
          $currency_data = $this->request->post('currency');
          $this->smarty->assign('currency_data', $currency_data);
          
          if($this->request->isSubmitted()) {
            try {
              $this->active_currency->setAttributes($currency_data);
              $this->active_currency->save();
              
              $this->response->respondWithData($this->active_currency, array('as' => 'currency'));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add
    
    /**
     * Update existing currency
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_currency->isLoaded()) {
          if($this->active_currency->canEdit($this->logged_user)) {
            $currency_data = $this->request->post('currency', array(
              'name'                => $this->active_currency->getName(),
              'code'                => $this->active_currency->getCode(),
              'decimal_spaces'      => $this->active_currency->getDecimalSpaces(),
              'decimal_rounding'    => $this->active_currency->getDecimalRounding(),
            ));
            $this->smarty->assign('currency_data', $currency_data);
            
            if($this->request->isSubmitted()) {
              try {
                $this->active_currency->setAttributes($currency_data);
                $this->active_currency->save();
                
                $this->response->respondWithData($this->active_currency, array('as' => 'currency'));
              } catch(Exception $e) {
                $this->response->exception($e);
              } // try
            } // if
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // edit
    
    /**
     * Set currency as default
     */
    function set_as_default() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_currency->isLoaded()) {
          if($this->active_currency->canEdit($this->logged_user)) {
            if($this->request->isSubmitted()) {
              try {
                Currencies::setDefault($this->active_currency);
                
                $this->response->respondWithData($this->active_currency, array('as' => 'currency'));
              } catch(Exception $e) {
                $this->response->exception($e);
              } // try
            } // if
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // set_as_default
    
    /**
     * Delete existing currency
     */
    function delete() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_currency->isLoaded()) {
          if($this->active_currency->canDelete($this->logged_user)) {
            try {
              $this->active_currency->delete();
              $this->response->respondWithData($this->active_currency, array('as' => 'currency'));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // delete
    
  }