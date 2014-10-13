<?php

  /**
   * link_button
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Draws a button with dropdown menu
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_link_button_dropdown($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if

    $content = trim($content);
    if (!$content) {
      return smarty_function_link_button($params, $smarty);
    } // if

    $id = array_var($params, 'id', NULL);
    if(!$id) {
      $id = HTML::uniqueId('link_button_dropdown');
    } // if

    $button_class = array_var($params, 'class', null);
    $icon_class = array_var($params, 'icon_class', null);
    $label = array_var($params, 'label', lang('Button'));

    AngieApplication::useWidget('link_button_dropdown', INVOICING_MODULE);
    $return = '<span class="link_button_dropdown ' . $button_class . '" id="' . $id . '">';
    $return.= '  <span class="link_button_dropdown_button"><span class="inner">';
    $return.= $icon_class ? '<span class="icon ' . $icon_class . '">' : '';
    $return.= $label;
    $return.= $icon_class ? '</span>' : '';
    $return.= '  </span></span>';
    $return.= '  <span class="link_button_dropdown_dropdown">' . $content . '</span>';
    $return.= '</span>';
    $return.= '<script type="text/javascript">$("#' . $id . '").linkButtonDropdown();</script>';

    return $return;
  } // smarty_block_link_button_dropdown