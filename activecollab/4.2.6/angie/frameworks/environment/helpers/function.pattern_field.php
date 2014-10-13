<?php

  /**
   * pattern_field helper implementation
   *
   * @package Angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render pattern field
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_pattern_field($params, &$smarty) {
    $id = array_var($params, 'id', null, true);

    if(empty($id)) {
      $id = HTML::uniqueId('pattern_field');
    } // if

    $default_format = array_var($params, 'default_format', '', true);

    if(empty($params['placeholder'])) {
      $params['placeholder'] = $default_format ? lang('Default format - :format', array('format' => $default_format)) : lang('Default format');
    } // if

    $variables = array_required_var($params, 'variables', true);

    if($variables) {
      $variables = explode(',', $variables);
    } // if

    AngieApplication::useWidget('pattern_field', ENVIRONMENT_FRAMEWORK);
    AngieApplication::useHelper('text_field', ENVIRONMENT_FRAMEWORK);

    $result = '<div id="' . $id . '" class="pattern_field">' . smarty_function_text_field($params, $smarty);

    $result .= '<ul><li>' . lang('Variables') . ':</li>';
    foreach($variables as $variable) {
      $result .= '<li><a href="#" class="variable_name" title="' . lang('Click to Add') . '" tabindex="-1">:' . clean($variable) . '</a></li>';
    } // foreach
    $result .= '</ul>';

    return $result . '</div><script type="text/javascript">$("#' . $id . '").patternField();</script>';
  } // smarty_function_pattern_field