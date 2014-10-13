<?php

  /**
   * ScheduleController controller
   *
   * @package angie.frameworks.schedule
   * @subpackage controllers
   */
  class FwScheduleController extends Controller {
    
    /**
     * Active object instance
     *
     * @var ISchedule
     */
    protected $active_object;
    
    /**
     * Execute before all controller actions
     */
    function __before() {
    	parent::__before();
    	
    	if($this->active_object instanceof ISchedule) {
    	  if($this->active_object->isLoaded()) {
    	    if($this->active_object->schedule()->canReschedule($this->logged_user)) {
    	      $this->response->assign('active_object', $this->active_object);
    	    } else {
      	    $this->response->forbidden();
      	  } // if
    	  } else {
    	    $this->response->notFound();
    	  } // if
    	} else {
    	  $this->response->notFound();
    	} // if
    } // __before 
      	
  	/**
  	 * Reschedule object
  	 */
  	function reschedule() {
  	  if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
  	    $reschedule_data = $this->request->post('reschedule', array(
    			'start_on' => $this->active_object->fieldExists('start_on') ? $this->active_object->getStartOn() :  null,
    			'due_on' => $this->active_object->getDueOn(),
    			'tbd' => $this->active_object->fieldExists('start_on') ? !($this->active_object->getDueOn() || $this->active_object->getStartOn()) : !$this->active_object->getDueOn() 
    		));

		    // preset due on date
		    if ($this->request->get('due_on')) {
			    $reschedule_data['due_on'] = DateValue::makeFromString($this->request->get('due_on'));
			    $reschedule_data['tbd'] = false;
		    } // if

		    $reschedule_url = $this->active_object->schedule()->getRescheduleUrl();
		    $as_calendar_events = $this->request->get('as_calendar_events', false);
		    if ($as_calendar_events) {
			    $reschedule_url = extend_url($reschedule_url, array(
				    'as_calendar_events' => true
			    ));
		    } // if

    		$this->smarty->assign(array(
    			'reschedule_data' => $reschedule_data,
    			'reschedule_url' => $reschedule_url
    		));
    		
    		if ($this->request->isSubmitted()) {
    			try {
    				if (array_var($reschedule_data, 'tbd')) {
    					$start_on = null;
    					$due_on = null;
    				} else {
    					$start_on = isset($reschedule_data['start_on']) && $reschedule_data['start_on'] ? $reschedule_data['start_on'] : null;
    					$due_on = isset($reschedule_data['due_on']) && $reschedule_data['due_on'] ? $reschedule_data['due_on'] : null;
    				} // if
    				
    				if ($this->active_object->fieldExists('start_on')) {
    					$this->active_object->setStartOn($start_on);
    				} // if
    				$this->active_object->setDueOn($due_on);
    				
    				$this->active_object->save();

				    if ($as_calendar_events) {
					    $this->response->respondWithData($this->active_object->calendar_event_context()->describe($this->logged_user));
				    } else {
					    $this->response->respondWithData($this->active_object, array(
						    'detailed' => true,
					    ));
				    }
    			} catch (Exception $e) {
    				$this->response->exception($e);
    			} // try  			
    		} // if
  	  } else {
  	    $this->response->badRequest();
  	  } // if
  	} // reschedule
  	
  }