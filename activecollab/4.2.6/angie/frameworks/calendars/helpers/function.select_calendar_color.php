<?php

  /**
   * select_calendar_color helper implementation
   *
   * @package angie.frameworks.calendars
   * @subpackage helpers
   */

  /**
   * Render select calendar color
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_calendar_color($params, &$smarty) {
    $color_rows = array(
      array('f8b67e', 'f58f84', 'bc6e6e', 'd4657b', 'cc8899', 'ff99ff', 'eb80d0', 'd1a0f1'),
      array('dabfee', 'b583af', '9366b4', '8080c0', '8080e6', '6b94c5', '80bfff', '80d7ff'),
      array('94e0e4', 'b2decf', '80c0c0', '8faea2', '91c591', 'bfdea1', 'd5e395', 'f5e48a'),
      array('ffd266', 'e9b87a', 'e4d2ba', 'dbd0bd', 'baaf9e', 'c8c7c7', 'a6a6a6', '909090'),
    );

    $name = array_required_var($params, 'name');
    $value = array_var($params, 'value');

    if(empty($value)) {
      $value = Calendar::DEFAULT_COLOR;
    } // if

    $id = array_var($params, 'id');
    if(empty($id)) {
      $id = HTML::uniqueId('select_calendar_color');
    } // if

    $result .= '<div id="' . $id . '" class="select_calendar_color">';

    if(isset($params['label']) && $params['label']) {
      $result .= HTML::label($params['label'], null, false, array('class' => 'main_label'));
    } // if

    $result .= '<table cellspacing="0" cellpadding="0">';

    foreach($color_rows as $color_row) {
      $result .= '<tr>';
      foreach($color_row as $color) {
        $result .= '<td color_value="' . $color . '"><a href="#" style="background-color: #' . $color . '"><span>#' . $color . '</span></a></td>';
      } // foreach
      $result .= '</tr>';
    } // foreach

    $result .= '<input type="hidden" name="' . clean($name) . '" value="' . clean($value) . '">';

    return $result . '</table></div><script type="text/javascript">$("#' . $id . '").selectCalendarColor(' . JSON::encode($options) . ');</script>';
  } // smarty_function_select_calendar_color