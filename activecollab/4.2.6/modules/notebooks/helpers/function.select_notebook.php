<?php

  /**
   * Select notebook helper implementation
   *
   * @package activeCollab.modules.notebooks
   * @subpackage helpers
   */

  /**
   * Select notebook
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_notebook($params, &$smarty) {
  	$project = array_required_var($params, 'project', true, 'Project');
  	$user = array_required_var($params, 'user', true, 'User');
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', true);
    $optional = array_var($params, 'optional', true, true);
    $skip = array_var($params, 'skip', array(), true);
    
    if (is_string($skip) || is_scalar($skip)) {
    	$skip = array($skip);
    } // if
    
    $notebooks = Notebooks::findByProject($project, STATE_VISIBLE, $user->getMinVisibility());
    $possibilities = array();
    
    if($optional) {
      $options[] = lang('-- None --');
      $options[] = '';
    } // if
    
    if(is_foreachable($notebooks)) {
    	foreach($notebooks as $notebook) {
    		if (!in_array($notebook->getId(), $skip)) {
    			$possibilities[$notebook->getId()] = $notebook->getName();
    		} // if
    	} // foreach
    } // if
        
    return HTML::selectFromPossibilities($name, $possibilities, $value, $params);
  } // smarty_function_select_notebook

?>