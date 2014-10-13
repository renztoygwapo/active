<?php

  /**
   * project_exporter_tracking_list helper
   *
   * @package activeCollab.modules.tracking
   * @subpackage helpers
   */
  
  /**
   * Shows a list of timerecords and expenses 
   *
   * Parameters:
   * 
   * - tracking_objects - array of TrackingObject
   * - timerecord_icon - string path to timerecord icon
   * - expense_icon - string path to expense icon
   * - csv_file_path - string file path for Comma Separated Value file
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_tracking_list($params, $template) {
  	$tracking_objects = array_var($params, 'tracking_objects', null);
  	$csv_file_path = array_var($params,'csv_file_path',null);
  	$timerecord_icon = array_var($params, 'timerecord_icon', '');
  	$expense_icon = array_var($params, 'expense_icon', '');
  	
  	AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
  	
  	if (!is_foreachable($tracking_objects)) {
  	  return '<p>' . lang('There are no time records or expenses on this project') . '</p>';
  	} //if
  	$csv_file_handler = fopen($csv_file_path, 'w');
  	$return = '';
  	$csv_link = '';
  	foreach ($tracking_objects as $tracking_object) {
  	  $return .= '<tr>';
  	  if ($tracking_object instanceof TimeRecord) {
  	    $return .= '<td class="column_hour">' . $tracking_object->getValue() . ' h</td>';
  	    $return .= '<td class="icon"><img src="' . $timerecord_icon . '" alt="Timerecord" /></td>';
  	    $return .= '<td class="column_user">' . smarty_function_project_exporter_user_link(array('id' => $tracking_object->getUserId(), 'name' => $tracking_object->getUserName(), 'email' => $tracking_object->getUserEmail()), $template) . '</td>';
  	    $return .= '<td class="column_summary">' . clean($tracking_object->getSummary()) . '</td>';                                 
  	    $return .= '<td class="column_date">' . smarty_modifier_date($tracking_object->getRecordDate(), 0) . '</td>';
  	  } elseif ($tracking_object instanceof Expense) {
  	    $return .= '<td class="column_hour">' . $tracking_object->getValue() . '</td>';
  	    $return .= '<td class="icon"><img src="' . $expense_icon . '" alt="Expense" /></td>';
  	    $return .= '<td class="column_user">' . smarty_function_project_exporter_user_link(array('id' => $tracking_object->getUserId(), 'name' => $tracking_object->getUserName(), 'email' => $tracking_object->getUserEmail()), $template) . '</td>';
  	    $return .= '<td class="column_summary">' . clean($tracking_object->getSummary()) . '</td>';                                 
  	    $return .= '<td class="column_date">' . smarty_modifier_date($tracking_object->getRecordDate(), 0) . '</td>';
  	  } //if
  	  $return .= '</tr>';
  	  if ($csv_file_handler !== false) {
  	      fwrite($csv_file_handler, project_exporter_tracking_add_csv_row($tracking_object));
  	  } //if
  	} //foreach
  	if ($csv_file_handler !== false) {
  	  fclose($csv_file_handler);
  	  $csv_link = '<br /><p><a href="' . basename($csv_file_path) . '">' . lang('Export CSV') . '</a></p>';
  	} //if
  	return '<table class="common" id="tracking_list">' . $return . '</table>'.$csv_link;
  } // smarty_function_project_exporter_tracking_list
  
  function project_exporter_tracking_add_csv_row($tracking_object) {
    if ($tracking_object instanceof TimeRecord) {
      return $tracking_object->getValue() . ' h,Timerecord,' . $tracking_object->getUserName() . ',' . $tracking_object->getSummary() . ',' . smarty_modifier_date($tracking_object->getCreatedOn())."\n";
    } elseif ($tracking_object instanceof Expense) {
      return $tracking_object->getValue() . ',Expense,' . $tracking_object->getUserName() . ',' . $tracking_object->getSummary() . ',' . smarty_modifier_date($tracking_object->getCreatedOn())."\n";
    } else {
      return '';
    } //if
  } //project_exporter_tracking_add_csv_row