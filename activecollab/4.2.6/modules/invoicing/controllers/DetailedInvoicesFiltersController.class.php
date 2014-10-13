<?php

  // Build on top of main invoices filter controller
  AngieApplication::useController('invoices_filters', INVOICING_MODULE);

  /**
   * Detailed invoices filter controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class DetailedInvoicesFiltersController extends InvoicesFiltersController {

    /**
     * Return filter class managed by this controller
     *
     * @return string
     */
    function getFilterType() {
      return 'DetailedInvoicesFilter';
    } // getFilterType

    /**
     * Return filter ID variable name
     *
     * @return mixed
     */
    function getFilterIdVariableName() {
      return 'detailed_invoices_filter_id';
    } // getFilterIdVariableName

  }