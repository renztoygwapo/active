<?php

  /**
   * smarty_function_list_financial_managers helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Render financial manager list
   *
   * @param $params
   * @param $smarty
   * @return string
   */
  function smarty_function_list_financial_managers($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $value = array_var($params, 'value', null, true);
    $id = array_var($params, 'id', null, true);

    if(empty($id)) {
      $id = HTML::uniqueId('financial_manager_list');
    } // if

    $managers = InvoiceObjects::findFinancialManagers();
    if(is_foreachable($managers)) {
      $possibilities = array();

      foreach($managers as $manager) {
        $possibilities[$manager->getId()] = $manager->getDisplayName();
      } // foreach

      $control = HTML::checkboxGroupFromPossibilities($name, $possibilities, $value, $params); 
    } else {
      $control = "You don't have user with 'Manage Finances' permission in your system so no user will be notified on new payment.";
    }//if
   
    $result = '<div id="' . $id . '" class="select_financial_managers">' . $control;

    return $result . '</div>';
  } // smarty_function_list_financial_managers
  