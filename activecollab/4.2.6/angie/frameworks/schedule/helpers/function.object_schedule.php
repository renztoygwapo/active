<?php

  /**
   * object_schedule helper definition
   *    *
   * @package angie.frameworks.schedule
   * @subpackage helpers
   */

  /**
   * Render object time widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_schedule($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'ISchedule');
    $user = array_required_var($params, 'user', true, 'IUser');
        
    $id = array_var($params, 'id');
    if(empty($id)) {
      $id = HTML::uniqueId('object_schedule_for_' . $object->getId()); 
    } // if
    
    $params = array(
    	'is_range' => $object->schedule()->isRange(),
    	'reschedule_url' => $object->schedule()->getRescheduleUrl(),
    	'start_on' => $object->schedule()->isRange() ? $object->getStartOn() : null,
    	'due_on' => $object->getDueOn(),
    	'can_reschedule' => $object->schedule()->canReschedule($user),
    	'object_id' => $object->getId(),
    	'object_name' => $object->getName(),
    	'event_name' => $object->getUpdatedEventName(),
    	'listen_events' => array($object->getUpdatedEventName()),
      'show_label' => array_var($params, 'show_label', true, true),
      'interface' => array_var($params, 'interface', AngieApplication::getPreferedInterface(), true)
    );

    AngieApplication::useWidget('object_schedule', SYSTEM_MODULE);
    return '<span class="object_schedule" id="' . $id . '"></span><script type="text/javascript">$(\'#' . $id . '\').objectSchedule(' . JSON::encode($params, $user, null) . ')</script>';
  } // smarty_function_object_schedule