<?php

  /**
   * on_admin_panel event handler
   * 
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function tracking_handle_on_admin_panel(AdminPanel &$admin_panel) {
    if(AngieApplication::isModuleLoaded('invoicing')) {
      $admin_panel->addToInvoicing('job_types_admin', lang('Job Types & Hourly Rates'), Router::assemble('job_types_admin'), AngieApplication::getImageUrl('admin_panel/hourly-rates.png', TRACKING_MODULE), array(
        'onclick' => new FlyoutCallback(array(
          'width' => 700
        )),
      ));
      $admin_panel->addToInvoicing('expense_categories', lang('Expense Categories'), Router::assemble('expense_categories_admin'), AngieApplication::getImageUrl('admin_panel/expense-categories.png', TRACKING_MODULE));
    } else {
      $admin_panel->addToProjects('job_types_admin', lang('Job Types & Hourly Rates'), Router::assemble('job_types_admin'), AngieApplication::getImageUrl('admin_panel/hourly-rates.png', TRACKING_MODULE), array(
        'onclick' => new FlyoutCallback(array(
          'width' => 700
        )),
      ));
      $admin_panel->addToProjects('expense_categories', lang('Expense Categories'), Router::assemble('expense_categories_admin'), AngieApplication::getImageUrl('admin_panel/expense-categories.png', TRACKING_MODULE));
    } // if
  } // tracking_handle_on_admin_panel