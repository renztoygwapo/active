<?php

  /**
   * object_completion helper implementation
   * 
   * @package angie.frameworks.complete
   * @subpackage helpers
   */

  /**
   * Render object completion text
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_completion($params, Smarty $smarty) {
  	$object = array_required_var($params, 'object', true, 'IComplete');
  	$user = array_required_var($params, 'user', true, 'IUser');
  	$interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
  	
  	if(empty($params['id'])) {
  	  $params['id'] = HTML::uniqueId('object_completion');
  	} // if
  	
  	if(isset($params['class'])) {
  	  $params['class'] .= ' object_compleation';
  	} else {
  	  $params['class'] = 'object_compleation';
  	} // if
  	
  	if($object->complete()->isCompleted()) {
  		$text = lang('Completed by :user on :date', array(
  		  'user' => $object->complete()->getCompletedBy()->getDisplayName(true), 
  			'date' => $object->getCompletedOn()->formatForUser($user), 
      ));
  	} else {
  		$text = lang('Open');
  	} // if
  	
  	$options = array(
  	  'complete_event' =>	$object->getUpdatedEventName(), 
  	  'open_event' =>  $object->getUpdatedEventName(),
  	);
  	
  	$result = HTML::openTag('div', $params) . $text . '</div>';
  	
  	if($interface == AngieApplication::INTERFACE_DEFAULT) {
      AngieApplication::useWidget('object_completion', COMPLETE_FRAMEWORK);
  	  $result .= '<script type="text/javascript">$("#' . $params['id']. '").objectCompletion(' . JSON::encode($options, $user) . ');</script>';
  	} // if
  	
  	return $result;
  } // smarty_function_object_completion