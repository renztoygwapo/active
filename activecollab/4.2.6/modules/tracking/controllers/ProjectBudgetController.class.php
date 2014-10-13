<?php

  // Build on top of project controller
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * Project budget controller implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class ProjectBudgetController extends ProjectController {
  
    /**
     * Execute before any controller action
     */
    function __before() {
      parent::__before();
      
      if(!$this->active_project->canManageBudget($this->logged_user)) {
        $this->response->forbidden();
      } // if
    } // __before
    
    /**
     * Show project budget information
     */
    function index() {
      $budget = $this->active_project->getBudget();
      
      $cost_by_type = TrackingObjects::sumCostByTypeAndProject($this->logged_user, $this->active_project);
      
      if($cost_by_type) {
        $cost_so_far = 0;
        
        foreach($cost_by_type as $k => $v) {
          $cost_so_far += $v['value'];
        } // foreach
      } else {
        $cost_so_far = 0;
      } // if
      
      if($budget) {
        $cost_so_far_perc = $cost_so_far > 0 ? ceil(($cost_so_far * 100) / $budget) : 0;
        $cost_over_budget_perc = $cost_so_far > $budget ? $cost_so_far_perc - 100 : null;
      } else {
        $cost_so_far_perc = null;
        $cost_over_budget_perc = null;
      } // if
      
      $this->smarty->assign(array(
        'project_currency' => $this->active_project->getCurrency(),
        'budget' => $budget, 
        'cost_by_type' => $cost_by_type, 
        'cost_so_far' => $cost_so_far, 
        'cost_so_far_perc' => $cost_so_far_perc, 
        'cost_over_budget_perc' => $cost_over_budget_perc, 
      ));
    } // index
    
  }