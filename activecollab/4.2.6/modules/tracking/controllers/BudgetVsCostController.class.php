<?php

  // Build on top of reports module
  AngieApplication::useController('reports', REPORTS_FRAMEWORK_INJECT_INTO);

  /**
   * Budget vs cost controller
   *
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class BudgetVsCostController extends ReportsController {

    /**
     * Show budget vs cost report
     */
    function budget_vs_cost() {
      $this->response->assign('projects', Projects::findActiveByUserWithBudget($this->logged_user, true));
    } // budget_vs_cost

  }