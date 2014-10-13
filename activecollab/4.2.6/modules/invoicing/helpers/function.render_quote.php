<?php

  /**
   * render_quote helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage modules
   */

  /**
   * Display quote
   * 
   * Params:
   * 
   * - invoice
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_render_quote($params, &$smarty) {
    $quote = array_required_var($params, 'quote', null, 'Quote');
    
		return $smarty->fetch(get_view_path('_render_quote', null, INVOICING_MODULE));
  } // smarty_function_render_quote