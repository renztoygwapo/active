<?php

  // Build on top of main invoices filter controller
  AngieApplication::useController('invoices_filters', INVOICING_MODULE);

  /**
   * Summarized invoices filter controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class SummarizedInvoicesFiltersController extends InvoicesFiltersController {

    /**
     * Return filter class managed by this controller
     *
     * @return string
     */
    function getFilterType() {
      return 'SummarizedInvoicesFilter';
    } // getFilterType

    /**
     * Return filter ID variable name
     *
     * @return mixed
     */
    function getFilterIdVariableName() {
      return 'summarized_invoices_filter_id';
    } // getFilterIdVariableName

  }