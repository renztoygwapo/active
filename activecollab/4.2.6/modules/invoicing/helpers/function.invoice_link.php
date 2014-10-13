<?php

  /**
   * invoice_link helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage modules
   */

  /**
   * Display invoice link
   * 
   * Params:
   * 
   * - invoice
   *
   * @param array $params
   * @return string
   */
  function smarty_function_invoice_link($params) {
    $invoice = array_var($params, 'invoice', null, true);
    if(!($invoice instanceof  Invoice)) {
      return new InvalidParamError('invoice', $invoice, '$invoice is expected to be an instance of Invoice class', true);
    } // if
    
    if(array_var($params, 'company')) {
      $params['href'] = $invoice->getCompanyViewUrl();
    } else {
      $params['href'] = $invoice->getViewUrl();
    } // if
    
    return open_html_tag('a', $params) . clean($invoice->getName()) . '</a>';
  } // smarty_function_invoice_link

?>