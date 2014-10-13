<?php

  /**
   * Object expense tracking controller delegate
   *
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class ObjectTrackingExpensesController extends Controller {
    
    /**
     * Selected object
     *
     * @var ITracking
     */
    protected $active_tracking_object;
    
    /**
     * Loaded expense
     *
     * @var Expense
     */
    protected $active_expense;
    
    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;
    
    /**
     * Construct object tracking controller delegate
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
      
      $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, "{$context}_tracking_expense");
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_tracking_object instanceof ITracking && $this->active_tracking_object->isLoaded()) {
        $expense_id = $this->request->getId('expense_id');
        if($expense_id) {
          $this->active_expense = Expenses::findById($expense_id);
        } // if

        if($this->active_expense instanceof Expense) {
          $this->state_delegate->__setProperties(array(
            'active_object' => &$this->active_expense,
          ));
        } else {
          $this->active_expense = new Expense();
          $this->active_expense->setParent($this->active_tracking_object);
        } // if

        $active_project = $this->active_tracking_object instanceof Project ? $this->active_tracking_object : $this->active_expense->getProject();

        // Assign variables
        $this->response->assign(array(
          'active_expense' => $this->active_expense,
          'can_track_for_others' => TrackingObjects::canTrackForOthers($this->logged_user, $active_project)
        ));
      } else {
        $this->response->notFound();
      } // if
    } // __before
    
    /**
     * Show single expense information (mobile devices only)
     */
    function view_expense() {
      if($this->active_expense->isLoaded()) {
        if($this->active_expense->canView($this->logged_user)) {

          // Phone call
          if($this->request->isPhone()) {
            $this->wireframe->setPageObject($this->active_expense, $this->logged_user);
            $this->wireframe->actions->remove(array('archive'));

          // Browser call
          } elseif($this->request->isWebBrowser()) {
            $this->response->assign('expense_category', $this->active_expense->getCategory());

          // API call
          } elseif($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_expense, array(
              'as' => 'expense',
              'detailed' => true,
            ));
          } // if

        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view_expense
    
    /**
     * Serve and process add expense form
     */
    function add_expense() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_tracking_object->tracking()->canAdd($this->logged_user)) {
          $expense_data = $this->request->post('expense', array(
            'category_id' => ExpenseCategories::getDefaultId(), 
            'user_id' => $this->logged_user->getId(), 
            'record_date' => DateTimeValue::now()->getForUser($this->logged_user),
            'billable_status' => $this->active_tracking_object->tracking()->getDefaultBillableStatus(),
          ));
          
          $this->response->assign('expense_data', $expense_data);
          
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Creating expense @ ' . __CLASS__);
              
              $this->active_expense->setAttributes($expense_data);
              $this->active_expense->setCreatedBy($this->logged_user);
              
              $this->active_expense->setState(STATE_VISIBLE);
              
              if($this->active_expense->getParent() == null) {
                $this->active_expense->setParent($this->active_tracking_object);
              } // if
              
              $this->active_expense->save();
              
              DB::commit('Expense created @ ' . __CLASS__);

              AngieApplication::cache()->removeByObject($this->active_tracking_object, 'describe');
              
              if($this->request->isPageCall()) {
		            $this->response->redirectToUrl($this->active_expense->getViewUrl());
		          } else {
		            $this->response->respondWithData($this->active_expense, array(
	                'as' => 'expense', 
		              'detailed' => true, 
	              ));
		          } // if
            } catch(Exception $e) {
              DB::rollback('Failed to create expense @ ' . __CLASS__);
              
              if($this->request->isPageCall()) {
		            $this->smarty->assign('errors', $e);
		          } else {
		            $this->response->exception($e);
		          } // if
            } // try
          } // if
        } else {
          $this->response->forbidden();
        }
      } else {
        $this->response->notFound();
      } // if
    } // add_expense
    
    /**
     * Upate expense
     */
    function edit_expense() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_expense->isLoaded()) {
          if($this->active_expense->canEdit($this->logged_user)) {
            $expense_data = $this->request->post('expense', array(
              'category_id' => $this->active_expense->getCategoryId(), 
              'user_id' => $this->active_expense->getUserId(),
              'value' => $this->active_expense->getValue(),
              'summary' => $this->active_expense->getSummary(),
              'record_date' => $this->active_expense->getRecordDate(),
              'billable_status' => $this->active_expense->getBillableStatus()
            ));
            $this->response->assign('expense_data', $expense_data);
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating expense @ ' . __CLASS__);
                
                $this->active_expense->setAttributes($expense_data);
                $this->active_expense->save();
                
                DB::commit('Expense updated @ ' . __CLASS__);

                AngieApplication::cache()->removeByObject($this->active_tracking_object, 'describe');
                
                if($this->request->isPageCall()) {
			            $this->response->redirectToUrl($this->active_expense->getViewUrl());
			          } else {
			            $this->response->respondWithData($this->active_expense, array(
	                  'as' => 'expense', 
			              'detailed' => true, 
	                ));
			          } // if
              } catch(Exception $e) {
                DB::rollback('Failed to update expense @ ' . __CLASS__);
                
                if($this->request->isPageCall()) {
			            $this->smarty->assign('errors', $e);
			          } else {
			            $this->response->exception($e);
			          } // if
              } // try
            } else {
              if($this->request->isAsyncCall()) {
                $this->response->assign(array(
                  '_project_expense_form_row_record' => $this->active_expense, 
                  '_project_expense_form_id' => 'edit_expense_' . $this->active_expense->getId(), 
                ));

                $fragment_view = $this->request->get('thin_form') ?  '_expense_thin_form_row' : '_expense_form_row';
                
                $this->response->respondWithFragment($fragment_view, 'object_tracking_expenses', TRACKING_MODULE, AngieApplication::INTERFACE_DEFAULT);
              } // if
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
    } // edit_expense
    
  }