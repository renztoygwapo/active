<?php

  /**
   * Object tracking time controller
   *
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class ObjectTrackingTimeRecordsController extends Controller {
    
    /**
     * Selected object
     *
     * @var ITracking
     */
    protected $active_tracking_object;
    
    /**
     * Loaded time record
     *
     * @var TimeRecord
     */
    protected $active_time_record;
    
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
    function __construct(&$parent, $context = null) {
      parent::__construct($parent, $context);
      
      $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, "{$context}_tracking_time_record");
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_tracking_object instanceof ITracking && $this->active_tracking_object->isLoaded()) {
        $time_record_id = $this->request->getId('time_record_id');
        if($time_record_id) {
          $this->active_time_record = TimeRecords::findById($time_record_id);
        } // if

        if($this->active_time_record instanceof TimeRecord) {
          $this->state_delegate->__setProperties(array(
            'active_object' => &$this->active_time_record,
          ));
        } else {
          $this->active_time_record = new TimeRecord();
          $this->active_time_record->setParent($this->active_tracking_object);
        } // if

        $active_project = $this->active_tracking_object instanceof Project ? $this->active_tracking_object : $this->active_time_record->getProject();

        // Assign variables
        $this->response->assign(array(
          'active_time_record' => $this->active_time_record,
          'can_track_for_others' => TrackingObjects::canTrackForOthers($this->logged_user, $active_project)
        ));
      } else {
        $this->response->notFound();
      } // if
    } // __before
    
    /**
     * Show single record information (API & Mobile devices only)
     */
    function view_time_record() {
      if($this->active_time_record->isLoaded()) {
        if($this->active_time_record->canView($this->logged_user)) {

          // Phone call
          if($this->request->isPhone()) {
            $this->wireframe->setPageObject($this->active_time_record, $this->logged_user);
            $this->wireframe->actions->remove(array('archive'));

          // Regular interface
          } elseif($this->request->isWebBrowser()) {
            $this->response->assign('job_type', $this->active_time_record->getJobType());

          // API call
          } else {
            $this->response->respondWithData($this->active_time_record, array(
              'as' => 'time_record',
              'detailed' => true,
            ));
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view_time_record
    
    /**
     * Create a new time record
     */
    function add_time_record() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_tracking_object->tracking()->canAdd($this->logged_user)) {
          $time_record_data = $this->request->post('time_record');
          if(!is_array($time_record_data)) {
            $time_record_data = array(
              'user_id' => $this->logged_user->getId(), 
              'record_date' => DateTimeValue::now()->getForUser($this->logged_user), 
              'billable_status' => $this->active_tracking_object->tracking()->getDefaultBillableStatus(),
            );
          } // if
          
          $this->response->assign('time_record_data', $time_record_data);
          
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Creating time record @ ' . __CLASS__);

              $time_record_data['value'] = isset($time_record_data['value']) ? time_to_float($time_record_data['value']) : 0;
              if(empty($time_record_data['value']) || $time_record_data['value'] < 0) {
                throw new ValidationErrors(array(
                  'value' => lang('Value is required'),
                ));
              } // if
              
              $this->active_time_record->setAttributes($time_record_data);
              $this->active_time_record->setCreatedBy($this->logged_user);
              
              $this->active_time_record->setState(STATE_VISIBLE);
              
              if($this->active_time_record->getParent() == null) {
                $this->active_time_record->setParent($this->active_tracking_object);
              } // if
              
              $this->active_time_record->save();
              
              DB::commit('Time record created @ ' . __CLASS__);

              AngieApplication::cache()->removeByObject($this->active_tracking_object, 'describe');
              
              if($this->request->isPageCall()) {
		            $this->response->redirectToUrl($this->active_time_record->getViewUrl());
		          } else {
		            $this->response->respondWithData($this->active_time_record, array(
	                'as' => 'time_record', 
		              'detailed' => true, 
	              ));
		          } // if
            } catch(Exception $e) {
              DB::rollback('Failed to create time record @ ' . __CLASS__);
              
              if($this->request->isPageCall()) {
		            $this->smarty->assign('errors', $e);
		          } else {
		            $this->response->exception($e);
		          } // if
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add_time_record
    
    /**
     * Update time record
     */
    function edit_time_record() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_time_record->isLoaded()) {
          if($this->active_time_record->canEdit($this->logged_user)) {
            $time_record_data = $this->request->post('time_record');
            if(!is_array($time_record_data)) {
              $time_record_data = array(
                'user_id' => $this->active_time_record->getUserId(),
                'record_user' => $this->active_time_record->getUser(),
                'value' => $this->active_time_record->getValue(),
                'job_type_id' => $this->active_time_record->getJobTypeId(), 
                'summary' => $this->active_time_record->getSummary(),
                'record_date' => $this->active_time_record->getRecordDate(),
                'billable_status' => $this->active_time_record->getBillableStatus()
              );
            } // if
            $this->response->assign('time_record_data', $time_record_data);
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating time record @ ' . __CLASS__);

                $time_record_data['value'] = isset($time_record_data['value']) ? time_to_float($time_record_data['value']) : 0;
                if(empty($time_record_data['value']) || $time_record_data['value'] < 0) {
                  throw new ValidationErrors(array(
                    'value' => lang('Value is required'),
                  ));
                } // if
                
                $this->active_time_record->setAttributes($time_record_data);
                $this->active_time_record->save();
                
                DB::commit('Time record updated @ ' . __CLASS__);

                AngieApplication::cache()->removeByObject($this->active_tracking_object, 'describe');
                
                if($this->request->isPageCall()) {
			            $this->response->redirectToUrl($this->active_time_record->getViewUrl());
			          } else {
			            $this->response->respondWithData($this->active_time_record, array(
	                  'as' => 'time_record', 
			              'detailed' => true, 
	                ));
			          } // if
              } catch(Exception $e) {
                DB::rollback('Failed to update time record @ ' . __CLASS__);
                
                if($this->request->isPageCall()) {
			            $this->smarty->assign('errors', $e);
			          } else {
			            $this->response->exception($e);
			          } // if
              } // try
            } else {
              if($this->request->isAsyncCall()) {
                $this->response->assign(array(
                  '_project_time_form_row_record' => $this->active_time_record, 
                  '_project_time_form_id' => 'edit_time_record_' . $this->active_time_record->getId(), 
                ));

                $fragment_view = $this->request->get('thin_form') ?  '_time_record_thin_form_row' : '_time_record_form_row';

                $this->response->respondWithFragment($fragment_view, 'object_tracking_time_records', TRACKING_MODULE);
                die();
              } elseif(!$this->request->isPhone()) {
                $this->response->badRequest();
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
    } // edit_time_record
    
  }