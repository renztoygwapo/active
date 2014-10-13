<?php

  /**
   * when_invoice_is_based_on helper implementation
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Render what to do when invoice is based on another object picker
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_when_invoice_is_based_on($params, &$smarty) {
    AngieApplication::useWidget('when_invoice_is_based_on', INVOICING_MODULE);

    $id = array_var($params, 'id');
    if(empty($id)) {
      $params['id'] = HTML::uniqueId('when_invoice_is_based_on');
    } // if

    $possibilities = array(
      Invoice::INVOICE_SETTINGS_KEEP_AS_SEPARATE => lang('Keep time records as separate invoice items'),
      Invoice::INVOICE_SETTINGS_SUM_ALL_BY_TASK => lang('Sum all time records grouped by task'),
      Invoice::INVOICE_SETTINGS_SUM_ALL_BY_PROJECT => lang('Sum all time records grouped by project'),
      Invoice::INVOICE_SETTINGS_SUM_ALL_BY_JOB_TYPE => lang('Sum all time records grouped by job type'),
      Invoice::INVOICE_SETTINGS_SUM_ALL => lang('Sum all time records as a single invoice item'),
    );
    
    $name = array_required_var($params, 'name', true);
    $mode = array_var($params, 'mode', null, true);
    
    $default_value = ConfigOptions::getValue('on_invoice_based_on');
    if(empty($default_value)) {
      $default_value = Invoice::INVOICE_SETTINGS_SUM_ALL;
    }//if
    
    $value = array_var($params, 'value', $default_value, true);

    if($mode == 'select') {
      return HTML::selectFromPossibilities($name, $possibilities, $value, $params);
    } else {
      return HTML::radioGroupFromPossibilities($name, $possibilities, $value, $params);
    } // if
  } // smarty_function_when_invoice_is_based_on