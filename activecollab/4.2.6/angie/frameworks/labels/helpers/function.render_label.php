<?php

  /**
   * render_label helper implementation
   *
   * @package angie.frameworks.labels
   * @subpackage helpers
   */

  /**
   * Renders label pill
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   * @throws InvalidParamError
   */
  function smarty_function_render_label($params, &$smarty) {
    $label = array_required_var($params, 'label');
    $short_name = (boolean) isset($params['short']) ? $params['short'] : false;

    if($label instanceof Label) {
      return $label->render();
    } elseif(is_array($label)) {
      $style = '';

      if(isset($label['fg_color']) && $label['fg_color']) {
        $style .= "color: $label[fg_color];";
      } // if

      if(isset($label['bg_color']) && $label['bg_color']) {
        $style .= "background-color: $label[bg_color]";
      } // if

      return HTML::openTag('span', array(
        'class' => 'pill',
        'style' => $style,
        'title' => $label['name']
      ), $short_name ? $label['short_name'] : $label['name']);
    } else {
      throw new InvalidParamError('label', $label);
    } // if
  } // smarty_function_render_label