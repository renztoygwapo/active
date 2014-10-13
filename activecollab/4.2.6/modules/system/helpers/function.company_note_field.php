<?php

  /**
   * Company note field implementation
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render company note field
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_company_note_field($params, &$smarty) {
    $value = (string) array_var($params, 'value', '', true);

    if(!isset($params['rows'])) {
      $params['rows'] = 10;
    } // if

    if(!isset($params['cols'])) {
      $params['cols'] = 48;
    } // if

    if(isset($params['class']) && $params['class']) {
      $params['class'] .= ' company_note_field';
    } else {
      $params['class'] = 'company_note_field';
    }

    return HTML::textarea(@$params['name'], $value, $params);
  } // smarty_function_company_note_field