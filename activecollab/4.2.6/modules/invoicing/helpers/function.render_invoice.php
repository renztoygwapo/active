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
  function smarty_function_render_invoice($params, &$smarty) {
    $invoice = array_required_var($params, 'invoice', null, 'Invoice');
    
    return $smarty->fetch(get_view_path('_render_invoice', null, INVOICING_MODULE));
  } // smarty_function_render_invoice