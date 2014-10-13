<?php

  /**
   * Render select filter type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_filter_type_mailbox($params, &$smarty) {
    $selected = array_var($params, 'value', null, true);

    $only_active = array_var($params,'only_active',true,true);

    $possibilities = array();

    $filter_types = $only_active ? IncomingMailboxes::findAllActive() : IncomingMailboxes::find();
    if($filter_types) {
      foreach($filter_types as $filter_type) {
        $possibilities[$filter_type->getId()] = $filter_type->getDisplayName();
      } // foreach
    } // if
    
    return HTML::checkboxGroupFromPossibilities($params['name'], $possibilities, $selected, $params);
  } // smarty_function_select_filter_type_mailbox