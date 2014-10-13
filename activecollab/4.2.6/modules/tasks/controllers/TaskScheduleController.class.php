<?php 

  /**
   * Task schedule controller
   * 
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class TaskScheduleController extends ScheduleController {
    
    /**
     * Reschedule task
     */
    function reschedule() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        $old_due_on = $this->active_object->getDueOn();
        
        $reschedule_data = $this->request->post('reschedule', array(
          'due_on' => $old_due_on,
          'tbd' => empty($old_due_on),
          'reschedule_subtasks' => true,  
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

        $this->response->assign(array(
          'reschedule_data' => $reschedule_data,
          'reschedule_url' => $reschedule_url
        ));
        
        if ($this->request->isSubmitted()) {
          try {
            DB::beginWork('Rescheduling task @ ' . __CLASS__);
            
            $tbd = isset($reschedule_data['tbd']) && $reschedule_data['tbd'];
            $reschedule_subtasks = isset($reschedule_data['reschedule_subtasks']) && $reschedule_data['reschedule_subtasks'];
            
            if(empty($tbd) && isset($reschedule_data['due_on']) && $reschedule_data['due_on']) {
              $due_on = new DateValue($reschedule_data['due_on']);
            } else {
              $due_on = null;
            } // if
            
            if(empty($due_on)) {
              $this->active_object->setDueOn(null);
              $this->active_object->save();
            } else {
              ProjectScheduler::rescheduleProjectObject($this->active_object, $due_on, $reschedule_subtasks);
            } // if
            
            DB::commit('Task rescheduled @ ' . __CLASS__);

	          if ($as_calendar_events) {
		          $described_task = $this->active_object->calendar_event_context()->describe($this->logged_user, true, true);
	          } else {
		          $described_task = $this->active_object->describe($this->logged_user, true, true);
	          } // if

	          if ($as_calendar_events) {
		          $described_task['affected_subtasks'] = array();

		          $subtasks = Subtasks::findByParent($this->active_object);
		          if (is_foreachable($subtasks)) {
			          foreach ($subtasks as $subtask) {
				          if ($subtask->getDueOn()) {
					          $described_task['affected_subtasks'][] = $subtask->calendar_event_context()->describe($this->logged_user, true, true);
				          } // if
			          } // foreach
		          } // if
	          } // if
            
            $this->response->respondWithData($described_task);
          } catch (Exception $e) {
            DB::rollback('Task rescheduling failed @ ' . __CLASS__);
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // reschedule
    
  }