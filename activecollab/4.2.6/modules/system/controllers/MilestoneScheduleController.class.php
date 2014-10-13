<?php

  // Build on top of the schedule controller
  AngieApplication::useController('schedule', SYSTEM_MODULE);

	/**
	 * Reschedule milestone
	 * 
	 * @package activeCollab.modules.system
	 * @subpackage controller
	 */
	class MilestoneScheduleController extends ScheduleController {
		
		/**
		 * Reschedule milestone
		 */
		function reschedule() {
		  if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        $describe_affected = $this->request->get('describe_affected', false);
			  $as_calendar_events = $this->request->get('as_calendar_events', false);
  			
        $reschedule_url = $this->active_object->schedule()->getRescheduleUrl();
        if ($describe_affected) {
        	$reschedule_url = extend_url($reschedule_url, array(
            'describe_affected' => true
          ));
        } // if

			  if ($as_calendar_events) {
				  $reschedule_url = extend_url($reschedule_url, array(
					  'as_calendar_events' => true
				  ));
			  } // if
  
        $milestone_data = $this->request->post('milestone', array(
  	      'start_on' => $this->request->get('start_on') ? DateValue::makeFromString($this->request->get('start_on')) : $this->active_object->getStartOn(),
  	      'due_on' => $this->request->get('due_on') ? DateValue::makeFromString($this->request->get('due_on')) : $this->active_object->getDueOn(),
  	      'reschedule_milestone_objects' => true,
        ));
        
        $successive_milestones = Milestones::findSuccessiveByMilestones($this->active_object, STATE_VISIBLE, $this->logged_user->getMinVisibility());
        
        $this->response->assign(array(
          'milestone_data' => $milestone_data,
          'successive_milestones' => $successive_milestones,
        	'reschedule_url'	=> $reschedule_url
        ));
        
        if($this->request->isSubmitted()) {
          $to_be_determined = isset($milestone_data['to_be_determined']) && $milestone_data['to_be_determined'];
          
          if($to_be_determined) {
            $new_start_on = $new_due_on = null;
            $reschedule_tasks = false;
          } else {
            $new_start_on = isset($milestone_data['start_on']) && $milestone_data['start_on'] ? new DateValue($milestone_data['start_on']) : null;
            $new_due_on = isset($milestone_data['due_on']) && $milestone_data['due_on'] ? new DateValue($milestone_data['due_on']) : null;
            
            if($new_start_on instanceof DateValue || $new_due_on instanceof DateValue) {
            	if (is_null($new_start_on)) {
            		$new_start_on = clone($new_due_on);
            	} elseif (is_null($new_due_on)) {
            		$new_due_on = clone($new_start_on);
            	} //if
              $reschedule_tasks = isset($milestone_data['reschedule_milestone_objects']) && $milestone_data['reschedule_milestone_objects'];
            } else {
              $to_be_determined = true;
              $reschedule_tasks = false;
            } // if
          } // if
          
          try {
            $to_move_successive = null;
            
            if ($to_be_determined) {
            	$this->active_object->setDueOn(null);
            	$this->active_object->setStartOn(null);
            	$this->active_object->save();
            } else {
              $with_successive = array_var($milestone_data, 'with_sucessive');

              switch(array_var($with_successive, 'action')) {
                case 'move_all':
                  $to_move_successive = $successive_milestones;
                  break;
                case 'move_selected':
                  $selected_milestones = array_var($with_successive, 'milestones');
                  if($selected_milestones && is_foreachable($selected_milestones)) {
                    $to_move_successive = Milestones::findByIds($selected_milestones, STATE_VISIBLE, $this->logged_user->getMinVisibility());
                  } // if
                  break;
              } // switch

            	ProjectScheduler::rescheduleMilestone($this->active_object, $new_start_on, $new_due_on, $reschedule_tasks, $to_move_successive);
            } //if

	          if ($as_calendar_events) {
		          $described_milestone = $this->active_object->calendar_event_context()->describe($this->logged_user, true, true);
	          } else {
		          $described_milestone = $this->active_object->describe($this->logged_user, true, true);
	          } // if
            
            if ($describe_affected && is_foreachable($to_move_successive)) {
              $described_milestone['affected_milestones'] = array();

              foreach($to_move_successive as $affected_milestone) {
                $described_milestone['affected_milestones'][] = DataObjectPool::get('Milestone', $affected_milestone->getId(), true)->describe($this->logged_user, true, true);

	              if ($as_calendar_events) {
		              // find all affected tasks
		              $tasks = Tasks::findByMilestone($affected_milestone, STATE_VISIBLE, $this->logged_user->getMinVisibility());
		              if (is_foreachable($tasks)) {
			              foreach ($tasks as $task) {
				              if ($task->getDueOn()) {
					              $described_milestone['affected_tasks'][] = $task->calendar_event_context()->describe($this->logged_user, true, true);
				              } // if
			              } // foreach

			              $subtasks = Subtasks::findBySql('SELECT * FROM ' . TABLE_PREFIX . 'subtasks WHERE parent_type = ? AND parent_id IN (?) AND state >= ? AND due_on IS NOT NULL', 'Task', objects_array_extract($tasks, 'getId'), STATE_VISIBLE);
			              if (is_foreachable($subtasks)) {
				              foreach ($subtasks as $subtask) {
					              $described_milestone['affected_subtasks'][] = $subtask->calendar_event_context()->describe($this->logged_user, true, true);
				              } // foreach
			              } // if
		              } // if
	              } // if
              } // foreach
            } // if

	          // @todo describe affected tasks and subtasks
	          if ($describe_affected && $reschedule_tasks && $as_calendar_events) {
		          $described_milestone['affected_tasks'] = array();
		          $described_milestone['affected_subtasks'] = array();

		          // find all affected tasks
		          $tasks = Tasks::findByMilestone($this->active_object, STATE_VISIBLE, $this->logged_user->getMinVisibility());
		          if (is_foreachable($tasks)) {
 			          foreach ($tasks as $task) {
			            if ($task->getDueOn()) {
				            $described_milestone['affected_tasks'][] = $task->calendar_event_context()->describe($this->logged_user, true, true);
			            } // if
			          } // foreach

			          $subtasks = Subtasks::findBySql('SELECT * FROM ' . TABLE_PREFIX . 'subtasks WHERE parent_type = ? AND parent_id IN (?) AND state >= ? AND due_on IS NOT NULL', 'Task', objects_array_extract($tasks, 'getId'), STATE_VISIBLE);
			          if (is_foreachable($subtasks)) {
				          foreach ($subtasks as $subtask) {
					          $described_milestone['affected_subtasks'][] = $subtask->calendar_event_context()->describe($this->logged_user, true, true);
				          } // foreach
			          } // if
		          } // if
	          } // if

            if ($this->request->isPageCall()) {
              $this->response->redirectToUrl($this->active_object->getViewUrl());
            } else {
              $this->response->respondWithData($described_milestone);
            } // if
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
		  } else {
		    $this->response->forbidden();
		  } // if
		} // reschedule		
		
	}