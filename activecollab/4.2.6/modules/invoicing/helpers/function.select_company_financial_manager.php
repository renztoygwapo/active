<?php

  /**
   * select_company_financial_manager helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage helper
   */

  /**
   * Render select company financial manager picker
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_company_financial_manager($params, &$smarty) {
    $company = array_required_var($params, 'company', true, 'Company');

    if(isset($params['class'])) {
      $params['class'] .= ' select_user';
    } else {
      $params['class'] = 'select_user';
    } // if

    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_user');
    } // if

    $name = array_var($params, 'name', null, true);
    $value = array_var($params, 'value', null, true);

    $optional = array_var($params, 'optional', false, true);

    $clients = Users::findClientsByPermissions('can_manage_client_finances', $company->getId());
    if(is_foreachable($clients)) {
      foreach($clients as $client) {
        $options[$client->getId()] = $client->getDisplayName();
      } //foreach
    } //if

    if($options) {
      if($optional) {
        $result = HTML::optionalSelectFromPossibilities($name, $options, $value,$params);
      } else {
        $result = HTML::selectFromPossibilities($name, $options, $value,$params);
      } //if
    } else {
      $result = lang('No Clients with "Receive and Pay Invoices" permissions in :company_name company', array('company_name' => $company->getName()));
    }


    return $result;
  } // smarty_function_select_company_financial_manager