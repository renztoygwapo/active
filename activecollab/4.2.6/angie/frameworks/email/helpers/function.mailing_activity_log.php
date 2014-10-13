<?php

  /**
   * mailing_activity_log helper implementation
   * 
   * @package angie.frameworks.email
   * @subpackage helpers
   */

  /**
   * Render mailing activity log
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mailing_activity_log($params, &$smarty) {
  	$user = array_required_var($params, 'user', true, 'IUser');
  	
  	if(empty($params['id'])) {
  	  $params['id'] = HTML::uniqueId('mailing_activity_log');
  	} // if
  	
  	$per_load = (integer) array_var($params, 'per_load');
  	if($per_load < 1) {
  		$per_load = 30;
  	} // if
  	
  	$additional_params = array('per_load' => $per_load);
  	$additional_conditions = array();
  	
  	$direction = array_var($params, 'direction');
  	if($direction == MailingActivityLog::DIRECTION_IN || $direction == MailingActivityLog::DIRECTION_OUT) {
  		$additional_params['direction'] = $direction;
  		$additional_conditions[] = DB::prepare('direction = ?', $direction);
  	} // if
  	
  	$additional_conditions = count($additional_conditions) ? '(' . implode(' AND ', $additional_conditions) . ')' : null;

    AngieApplication::useWidget('mailing_activity_log', EMAIL_FRAMEWORK);
  	
  	return '<div id="' . $params['id'] . '" class="mailing_activity_log"></div><script type="text/javascript">$("#' . $params['id'] . '").mailingActivityLog(' . JSON::encode(array(
  	  'load_more_url' => Router::assemble('email_admin_logs', $additional_params), 
  	  'entries' => MailingActivityLogs::getSlice($per_load, null, null, $additional_conditions), 
  	  'entries_per_load' => $per_load, 
  	  'total_entries' => MailingActivityLogs::count($additional_conditions)
  	)) . ');</script>';
  } // smarty_function_mailing_activity_log