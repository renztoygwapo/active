<?php

  /**
   * object_assignees helper
   *
   * @package angie.frameworks.assignees
   * @subpackage helpers
   */
  
  /**
   * Render object assignees list
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_assignees($params, &$smarty) {
    $object = $params['object'];
    $inline = isset($params['render_inline']) ? (boolean) $params['render_inline'] : false;
    if($object instanceof IAssignees) {
      $assignees = $object->assignees()->getAllAssignees();
      if(is_foreachable($assignees)) {
      	
      	if (!$inline) {
      		$result = '<ul class="object_assignees">';
	        foreach($assignees as $assignee) {
	          if($assignee->getId() == $object->getAssigneeId()) {
	            $result .= '<li style="text-decoration: underline">';
	          } else {
	            $result .= '<li>';
	          } // if
	          $result .= clean($assignee->getDisplayName(true)) . '</li>';
	        } // foreach
	        return $result .'</ul>';
	        
      	} else {
      		$responsible = null;
      		$formatted_assignees = array();
      		      		
      		foreach($assignees as $assignee) {
      			if($assignee->getId() == $object->getAssigneeId()) {
      				$responsible = '<u>' . clean($assignee->getDisplayName(true)) . '</u>';
      			} else {
      				$formatted_assignees[] = clean($assignee->getDisplayName(true));	
      			} // if
      		} // if
      		
      		return !is_foreachable($formatted_assignees) ? $responsible : $responsible . ', ' . implode(', ', $formatted_assignees); 
      	} // if
      	
      } else {
        return lang('No assignees');
      } // if
    } else {
      throw new InvalidInstanceError('object', $object, 'IAssignees');
    } // if
  } // smarty_function_object_assignees