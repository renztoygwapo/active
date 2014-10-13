<?php

  /**
   * select_invoice_second_tax_mode helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Render select invoice second tax mode
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_invoice_second_tax_mode($params, &$smarty){
    $value = array_var($params, 'value', false, true);
    $name = array_required_var($params, 'name', true);

    if (!array_key_exists('label', $params)) {
      $params['label'] = lang('Second Tax is Compound Tax');
    } // if

    $params['value'] = 1;

    return HTML::checkbox($name, $value > 0, $params);
  } // smarty_function_select_invoice_second_tax_mode