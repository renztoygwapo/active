<?php

  /**
   * project_budget helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Display project budget
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_budget($params, &$smarty) {
    $project = array_required_var($params, 'project', true, 'Project');
    $user = array_required_var($params, 'user', true, 'User');
    
    $budget = $project->getBudget();
    
    if($budget > 0) {
      AngieApplication::useHelper('number', GLOBALIZATION_FRAMEWORK, 'modifier');
      AngieApplication::useHelper('money', GLOBALIZATION_FRAMEWORK, 'modifier');
    
      if(AngieApplication::isModuleLoaded('tracking')) {
        $cost_so_far = TrackingObjects::sumCostByProject($user, $project);
        
        if($cost_so_far > 0) {
          $done_so_far = ceil(($cost_so_far * 100) / $budget);
        } else {
          $done_so_far = 0;
        } // if
        
        if($done_so_far > 100) {
          $class = 'cost_over_budget';
          $message = lang(':percent% over', array(
          	'percent' => $done_so_far - 100
          ));
        } elseif($done_so_far >= 90) {
          $class = 'cost_close_to_budget';
          $message = lang(':percent%', array(
          	'percent' => $done_so_far
          ));
        } else {
          $class = 'cost_ok';
          $message = null;
        } // if
        
        $result = '<span class="project_budget ' . $class . '"><span class="amount">' . smarty_modifier_money($budget, $project->getCurrency()) . '</span>';
        if($message) {
          $result .= " ($message)";
        } // if
        
        return $result . '</span>';
      } else {
        return '<span class="project_budget">' . smarty_modifier_money($project->getBudget(), $project->getCurrency()) . '</span>';
      } // if
    } else {
      return '--';
    } // if
  } // smarty_function_project_budget