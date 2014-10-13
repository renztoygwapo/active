<?php

  /**
   * on_tracking_report_options handler implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Add invoice related report options
   *
   * @param TrackingReport $report
   * @param NamedList $options
   * @param IUser $user
   */
  function invoicing_handle_on_tracking_report_options(TrackingReport &$report, NamedList &$options, IUser &$user) {
//    if(Invoices::canAdd($user)) {
//      $options->add('add_invoice', array(
//        'text' => lang('Create Invoice'), 
//        'url' => '#', 
//      ));
//    } // if
  } // invoicing_handle_on_tracking_report_options