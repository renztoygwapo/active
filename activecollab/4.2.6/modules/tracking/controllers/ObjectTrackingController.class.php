<?php

  /**
   * Object tracking controller
   *
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class ObjectTrackingController extends Controller {
    
    /**
     * Selected object
     *
     * @var ITracking
     */
    protected $active_tracking_object;
    
    /**
     * Time records controller delegate
     *
     * @var ObjectTrackingTimeRecordsController
     */
    protected $time_records_delegate;
    
    /**
     * Expenses delegate controller
     *
     * @var ObjectTrackingExpensesController
     */
    protected $expenses_delegate;
    
    /**
     * Construct object tracking controller delegate
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
    	parent::__construct($parent, $context);
      
      $this->time_records_delegate = $this->__delegate('object_tracking_time_records', TRACKING_MODULE, $context);
      $this->expenses_delegate = $this->__delegate('object_tracking_expenses', TRACKING_MODULE, $context);
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      if($this->active_tracking_object instanceof ITracking && $this->active_tracking_object->isLoaded()) {
        $this->time_records_delegate->__setProperties(array(
          'active_tracking_object' => &$this->active_tracking_object,
        ));

        $this->expenses_delegate->__setProperties(array(
          'active_tracking_object' => &$this->active_tracking_object,
        ));

        if($this->request->isWebBrowser() && $this->active_tracking_object instanceof Project) {
          $this->wireframe->breadcrumbs->add('object_tracking', lang('Time and Expenses'), Router::assemble('project_tracking', array('project_slug' => $this->active_tracking_object->getSlug())));
        } // if

        $this->response->assign('active_tracking_object', $this->active_tracking_object);
      } else {
        $this->response->notFound();
      } // if
    } // __before
    
    /**
     * Show object time and expenses
     */
    function object_tracking_list() {

    	// Regular web browser request
    	if($this->request->isWebBrowser()) {
    		$this->response->assign(array(
	        'items' => TrackingObjects::findByParentAsArray($this->logged_user, $this->active_tracking_object),
	        'can_add' => $this->active_tracking_object->tracking()->canAdd($this->logged_user),
	        'can_track_for_others' => TrackingObjects::canTrackForOthers($this->logged_user, $this->active_tracking_object->getProject())
	      ));
	      
	    // Request made by mobile device
    	} elseif($this->request->isMobileDevice()) {
    		if($this->active_tracking_object->tracking()->canAdd($this->logged_user)) {
          $this->wireframe->actions->beginWith('add_time', lang('Add Time'), $this->active_tracking_object->tracking()->getAddTimeUrl(), array(
            'icon' => AngieApplication::getImageUrl('icons/navbar/add-time.png', TRACKING_MODULE, AngieApplication::INTERFACE_PHONE)
          ));
          
          $this->wireframe->actions->addAfter('add_expense', lang('Add Time'), $this->active_tracking_object->tracking()->getAddExpenseUrl(), 'add_time', array(
            'icon' => AngieApplication::getImageUrl('icons/navbar/add-expense.png', TRACKING_MODULE, AngieApplication::INTERFACE_PHONE)
          ));
        } // if
    		
    		$this->response->assign('formatted_items', TrackingObjects::findForPhoneListByParent($this->logged_user, $this->active_tracking_object));

      // Respond to API request
      } elseif($this->request->isApiCall()) {
        $limit = $this->request->get('dont_limit_result') ? null : 300;

        $this->response->respondWithData(TrackingObjects::findRecent($this->logged_user, $this->active_tracking_object, STATE_ARCHIVED, $this->logged_user->getMinVisibility(), $limit), array(
          'as' => 'tracking_objects',
        ));
    	} // if
    } // object_tracking_list
    
    /**
     * Show and update object tracking estimate
     */
    function object_tracking_estimates() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        $estimates = $this->active_tracking_object->tracking()->getEstimates();
        
        if($this->request->isApiCall()) {
          $this->response->respondWithData($estimates, array('as' => 'estimates'));
        } else {
          $this->response->assign(array(
            'estimates' => $estimates, 
            'job_types' => JobTypes::getIdNameMap(), 
          ));
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // object_tracking_estimates
    
    /**
     * Set object estimate
     */
    function object_tracking_estimate_set() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall()) && $this->request->isSubmitted()) {
        $current_estimate = $this->active_tracking_object->tracking()->getEstimate();
          
        $estimate_data = $this->request->post('estimate');
        
        if(!is_array($estimate_data)) {
          $estimate_data = array(
            'value' => $current_estimate instanceof Estimate ? $current_estimate->getValue() : 1, 
            'job_type_id' => $current_estimate instanceof Estimate ? $current_estimate->getJobTypeId() : JobTypes::getDefaultJobTypeId(), 
          );
        } // if
        
        $this->response->assign('estimate_data', $estimate_data);
        
        if($this->request->isSubmitted()) {
          try {
            $value = isset($estimate_data['value']) && $estimate_data['value'] ? (float) $estimate_data['value'] : null;
            $job_type = isset($estimate_data['job_type_id']) && $estimate_data['job_type_id'] ? JobTypes::findById($estimate_data['job_type_id']) : null;
            $comment = isset($estimate_data['comment']) ? $estimate_data['comment'] : null;
            
            if($job_type instanceof JobType) {
              $estimate = $this->active_tracking_object->tracking()->setEstimate($value, $job_type, $comment, $this->logged_user);
            } else {
              throw new ValidationErrors(array(
                'job_type_id' => lang('Job type is required'), 
              ));
            } // if
            
            $this->response->respondWithData($estimate, array(
            	'as' => 'estimate',
            	'detailed' => true
            ));
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // object_tracking_estimate_set
    
  }