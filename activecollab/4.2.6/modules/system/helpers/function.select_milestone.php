<?php

  /**
   * select_milestone helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select milestone control
   * 
   * Params:
   * 
   * - project - Project instance that need to be used
   * - active_only - Return only active milestones, true by default
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_milestone($params, &$smarty) {
    $project = array_required_var($params, 'project', true, 'Project');
    $user = array_required_var($params, 'user', true, 'IUser');
    
    $active_only = (boolean) array_var($params, 'active_only', false, true);
    $value = array_var($params, 'value', null, true);
    
    $milestones = $active_only ? 
      Milestones::findActiveByProject($project, STATE_VISIBLE, $user->getMinVisibility()) : 
      Milestones::findByProject($project, $user);
    
    $options = array();
      
    if(is_foreachable($milestones)) {
      $completed_options = array();
      
      foreach($milestones as $milestone) {
        $selected = $milestone->getId() == $value;
        
        if($milestone->complete()->isCompleted()) {
          $completed_options[] = HTML::optionForSelect($milestone->getName(), $milestone->getId(), $selected);
        } else {
          $options[] = HTML::optionForSelect($milestone->getName(), $milestone->getId(), $selected);
        } // if
      } // foreach
      
      if(count($completed_options)) {
        $options[] = HTML::optionForSelect('', '');
        $options[] = HTML::optionGroup(lang('Completed'), $completed_options);
      } // if
    } // if
    
    if(array_var($params, 'optional', true, true)) {
      return HTML::optionalSelect($params['name'], $options, $params);
    } else {
      return HTML::select($params['name'], $options, $params);
    } // if
  } // smarty_function_select_milestone