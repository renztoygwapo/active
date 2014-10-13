<?php

  /**
   * Summarized invoices filter
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class SummarizedInvoicesFilter extends InvoicesFilter {

    /**
     * Run the filter
     *
     * @param IUser $user
     * @param mixed $additional
     * @return array
     */
    function run(IUser $user, $additional = null) {

    } // run

    /**
     * Run export and return file name where data is temporaly stored
     *
     * @param IUser $user
     * @param mixed $additional
     * @return string
     */
    function runForExport(IUser $user, $additional = null) {

    } // runForExport

    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'summarized_invoices_filter';
    } // getRoutingContext

    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array(
        'summarized_invoices_filter_id' => $this->getId(), 
      );
    } // getRoutingContextParams

  }