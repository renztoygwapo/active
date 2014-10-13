<?php

  /**
   * project_exporter_object_timerecords helper
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */
  
  /**
   * Show a list of timerecords and expenses
   *
   * Parameters:
   * 
   * - object - timerecords parent object
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_exporter_object_timerecords($params, $template) {
    $object = array_var($params, 'object', null);
    if(!($object instanceof ProjectObject)) {
      throw new InvalidInstanceError('object', $object, 'ProjectObject');
    } // if

    $user = array_required_var($params, 'user', true, 'User');
    
    AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
    
    $return = '';
		$tracking = TrackingObjects::findByParentAsArray($user, $object);
		if (is_foreachable($tracking)) {
			foreach ($tracking as $tracking_object) {
				if ($tracking_object['class'] == 'Expense') {
					$value = $tracking_object['value'];
				} else {
					$value = $tracking_object['value'] . ' ' . lang('hours');
				} // if

				$return.= '<tr><td>' . $value . '</td><td>' . $tracking_object['summary'] . '</td><td class="column_author">' . smarty_function_project_exporter_user_link(array('id' => $tracking_object['created_by_id'], 'name' => $tracking_object['created_by_name'], 'email' => $tracking_object['created_by_email']), $template) . '</td><td class="column_date">' . smarty_modifier_date($tracking_object['created_on']) . '</td></tr>';
			};
		} // if
		       
    if ($return) {
      $return = '<div id="object_tracking" class="object_info"><h3>' . lang('Time Records and Expenses') . '</h3><table class="common">' . $return . '</table></div>';
    } // if
    
    return $return;
  } // smarty_function_project_exporter_object_timerecords