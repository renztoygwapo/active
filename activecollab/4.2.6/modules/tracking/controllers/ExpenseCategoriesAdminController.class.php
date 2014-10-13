<?php

  // Build on top of administration controller
  AngieApplication::useController('admin', SYSTEM_MODULE);

  /**
   * Expense categories administration controller
   * 
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class ExpenseCategoriesAdminController extends AdminController {
    
    /**
     * Selected expense category
     *
     * @var ExpenseCategory
     */
    protected $active_expense_category;
    
    /**
     * Execute before action
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->breadcrumbs->add('expense_categories_admin', lang('Expense Categories'), Router::assemble('expense_categories_admin'));
      $this->wireframe->actions->add('add_expense_category_form', lang('New Expense Category'), Router::assemble('expense_categories_add'), array(
  	    'onclick' => new FlyoutFormCallback('expense_category_created', array(
          'width' => 400, 
        )), 
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()), 
  	  ));
      
      $expense_category_id = $this->request->getId('expense_category_id');
      if($expense_category_id) {
        $this->active_expense_category = ExpenseCategories::findById($expense_category_id);
      } // if
      
      if($this->active_expense_category instanceof ExpenseCategory) {
        $this->wireframe->breadcrumbs->add('expense_category', $this->active_expense_category->getName(), $this->active_expense_category->getViewUrl());
      } else {
        $this->active_expense_category = new ExpenseCategory();
      } // if
      
      $this->response->assign('active_expense_category', $this->active_expense_category);
    } // __before
  
    /**
     * Display list of defined expense categories
     */
    function index() {
      $expense_categories_per_page = 50;
    	
    	if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(ExpenseCategories::getSlice($expense_categories_per_page, $exclude, $timestamp));
    	} else {
    	  $this->smarty->assign(array(
    		  'expense_categories' => ExpenseCategories::getSlice($expense_categories_per_page), 
    			'expense_categories_per_page' => $expense_categories_per_page, 
    		  'total_expense_categories' => ExpenseCategories::count(), 
    		));
    	} // if
    } // index
    
    /**
     * Show details of a single expense category
     */
    function view() {
      if($this->request->isApiCall()) {
        if($this->active_expense_category->isLoaded()) {
          if($this->active_expense_category->canView($this->logged_user)) {
            $this->response->respondWithData($this->active_expense_category, array('as' => 'expense_category'));
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
     * Define a new expense category
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if(ExpenseCategories::canAdd($this->logged_user)) {
          $expense_category_data = $this->request->post('expense_category');
          $this->response->assign('expense_category_data', $expense_category_data);
          
          if($this->request->isSubmitted()) {
            try {
              $this->active_expense_category->setAttributes($expense_category_data);
              $this->active_expense_category->save();
              
              $this->response->respondWithData($this->active_expense_category, array('as' => 'expense_category'));
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
     * Update an existing expense category definition
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if($this->active_expense_category->isLoaded()) {
          if($this->active_expense_category->canEdit($this->logged_user)) {
            $expense_category_data = $this->request->post('expense_category', array(
              'name' => $this->active_expense_category->getName(),  
            ));
            $this->response->assign('expense_category_data', $expense_category_data);
            
            if($this->request->isSubmitted()) {
              try {
                $this->active_expense_category->setAttributes($expense_category_data);
                $this->active_expense_category->save();
                
                $this->response->respondWithData($this->active_expense_category, array('as' => 'expense_category'));
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
     * Set selected expense category as default
     */
    function set_as_default() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_expense_category->isLoaded()) {
          if($this->active_expense_category->canSetAsDefault($this->logged_user)) {
            try {
              ExpenseCategories::setDefault($this->active_expense_category);
              $this->response->respondWithData($this->active_expense_category, array('as' => 'expense_category'));
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
    } // set_as_default
    
    /**
     * Remove a specific expense category
     */
    function delete() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_expense_category->isLoaded()) {
          if($this->active_expense_category->canDelete($this->logged_user)) {
            try {
              $this->active_expense_category->delete();
              $this->response->respondWithData($this->active_expense_category, array('as' => 'expense_category'));
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