<?php

/**
 * progressbar helper implementation
 *
 * @package angie.frameworks.environment
 * @subpackage helpers
 */

/**
 * Render progressbar
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_progressbar($params, &$smarty) {
  $max_value = array_var($params, 'max_value', null, true);
  $value = array_required_var($params, 'value', true);
  $icon = array_var($params, 'icon', null, true);
  $href= array_var($params, 'href', null, true);
  $label = array_required_var($params, 'label', true);
  $class = array_var($params, 'class', '', true);

  // calculate class & style
  $class.= $max_value ? ' with_progressbar' : ' without_progressbar';
  $class.= $icon ? ' with_icon' : '';
  $style = $icon ? 'background-image: url(' . $icon . ');' : '';

  // open widget
  $rendered = $href ? '<a href="' . $href . '" class="advanced_progressbar ' . $class . '" style="' . $style . '">' : '<span class="advanced_progressbar ' . $class . '" style="' . $style . '">';

  // print label
  $rendered.= '<span class="advanced_progressbar_label">' . $label . '</span>';

  // print progressbar
  if ($max_value) {
    $percentage = ceil(($value / $max_value) * 100);
    $percentage = $percentage > 100 ? 100 : $percentage;
    $rendered.= '<span class="advanced_progressbar_progressbar"><span class="advanced_progressbar_progressbar_inner" style="width: ' . $percentage . '%"></span></span>';
  } // if

  $rendered.= $href ? '</a>' : '</span>';

  return $rendered;
} // smarty_function_progressbar