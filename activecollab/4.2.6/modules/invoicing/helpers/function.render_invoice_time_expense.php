<?php

  /**
   * render_invoice helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage modules
   */

  /**
   * Display invoice
   * 
   * Params:
   * 
   * - invoice
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_render_invoice_time_expense($params, &$smarty) {
    $invoice = array_required_var($params, 'invoice', null, 'Invoice');
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    return $smarty->fetch(get_view_path('_invoice_time_expense', 'invoices', INVOICING_MODULE, $interface));
  } // smarty_function_render_invoice