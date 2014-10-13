<?php

  /**
   * select_invoicing_default_due helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Render select default due for issued invoices
   *
   * @param $params
   * @param $smarty
   * @return string
   */
  function smarty_function_select_invoice_due_on($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $value = array_var($params, 'value', 15);
    $id = array_var($params, 'id', null, true);

    $mode = array_var($params, 'mode', 'radio', true);
    
    if(empty($id)) {
      $id = HTML::uniqueId('select_invoice_due_on');
    } // if

    $possibilities = array(
      0 => lang('Due Upon Receipt'),
      10 => lang('10 Days After Issue (NET 10)'),
      15 => lang('15 Days After Issue (NET 15)'),
      30 => lang('30 Days After Issue (NET 30)'),
      60 => lang('60 Days After Issue (NET 60)'),
    );

    if($mode == 'select' && !isset($value)) {
      $value = ConfigOptions::getValue('invoicing_default_due');
    }//if
    
    $allow_selected = $mode != 'select' && array_var($params, 'allow_selected', true, true);

    if($allow_selected) {
      $possibilities['selected'] = lang('Specify Due Date');
    } // if

    if($mode == 'select') {
      $control = HTML::selectFromPossibilities($name, $possibilities, $value, $params); //if drop down control required
    } else {
      $allow_selected =
      $control = HTML::radioGroupFromPossibilities($name, $possibilities, (string) $value, $params); //if radio buttons control
    } // if
    
    $result = '<div id="' . $id . '" class="select_invoicing_default_due">' . $control;

    if($allow_selected) {
      AngieApplication::useHelper('select_date', ENVIRONMENT_FRAMEWORK);

      $result .= '<div class="select_invoice_due_on_selected slide_down_settings" style="display: none;">' . smarty_function_select_date(array(
        'name' => str_ends_with($name, ']') ? substr($name, 0, strlen($name) - 1) . '_selected_date]' : $name . '_selected_date',
        'value' => $value > 0 ? DateValue::makeFromString("+{$value} days") : DateValue::now(),
        'label' => lang('Select Custom Due Date'),
      ), $smarty) . '</div>';
    } // if

    AngieApplication::useWidget('select_invoice_due_on', INVOICING_MODULE);
    return $result . '</div><script type="text/javascript">$("#' . $id . '").selectInvoiceDueOn();</script>';
  } // smarty_function_select_invoice_due_on