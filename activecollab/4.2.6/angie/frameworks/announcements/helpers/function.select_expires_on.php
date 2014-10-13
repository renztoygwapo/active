<?php

  /**
   * select_expires_on helper implementation
   *
   * @package angie.frameworks.announcements
   * @subpackage helpers
   */

  /**
   * Render select expires on field
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_expires_on($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', null, true);

    $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('expires_on');

    $result = '<table id="' . $id . '" class="select_expires_on" cellspacing="0"><tbody><tr>';

    $result .= '<td class="expires_on_select">' . HTML::selectFromPossibilities($name . '[type]', array(
      FwAnnouncement::ANNOUNCE_EXPIRATION_TYPE_NEVER => lang('Never'),
      FwAnnouncement::ANNOUNCE_EXPIRATION_TYPE_UNTIL_DISMISSED => lang('Until Dismissed'),
      FwAnnouncement::ANNOUNCE_EXPIRATION_TYPE_ON_DAY => lang('On a Day')
    ), isset($value['type']) && $value['type'] ? $value['type'] : null, $params) . '</td>';

    $result .= '<td class="expires_on_data">';

    require_once ENVIRONMENT_FRAMEWORK_PATH . '/helpers/function.select_date.php';
    $result .= '<div id="on_day" style="display: none;">' . smarty_function_select_date(array(
      'type' => 'text',
      'name' => $name . '[date]',
      'value' => isset($value['date']) && $value['date'] ? $value['date'] : null
    ), $smarty) . '</div>';

    $result .= '</td>';

    AngieApplication::useWidget('select_expires_on', ANNOUNCEMENTS_FRAMEWORK);

    return $result . '</tr></tbody></table><script type="text/javascript">App.widgets.SelectExpiresOn.init("' . $id . '");</script>';
  } // smarty_function_select_expires_on