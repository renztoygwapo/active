<?php

  /**
   * render_payments helper implementation
   *
   * @package angie.frameworks.payments
   * @subpackage helpers
   */

  /**
   * Display payments box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_render_payments($params, &$smarty) {
	  return $smarty->fetch(get_view_path('_object_payments', fw_payments, PAYMENTS_FRAMEWORK));
  } // smarty_function_render_payments