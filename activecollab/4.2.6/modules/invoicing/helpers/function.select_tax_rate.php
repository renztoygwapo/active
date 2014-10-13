<?php

  /**
   * select_tax_rate helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Render select tax rate box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_tax_rate($params, &$smarty) {
    $optional = (boolean) array_var($params, 'optional', false, true);
    $name = array_required_var($params, 'name', true);

    $value = array_var($params, 'value', false, true);
    $first = array_var($params, 'first_tax_rate', true, true);
     
    $options = array();
    if($optional) {
      $options[] = '-- No Tax --';
    } // if

    $tax_rates = TaxRates::find();

    $selected = null;
    if ($value) {
      $selected = $value;
    } // if

    foreach($tax_rates as $tax_rate) {
      if ($value === false && $first && $tax_rate->getIsDefault()) {
        $selected = $tax_rate->getId();
      } // if

      $options[$tax_rate->getId()] = $tax_rate->getName() . ' (' . $tax_rate->getPercentage() . ')';
    } // foreach

    return HTML::selectFromPossibilities($name, $options, $selected, $params);
  } // smarty_function_select_tax_rate