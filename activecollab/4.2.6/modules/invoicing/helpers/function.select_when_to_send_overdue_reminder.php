<?php

  /**
   * select_when_to_send_overdue_reminder helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Select when to send overdue reminder value
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_when_to_send_overdue_reminder($params, &$smarty) {
    $settings = array(
      'name' => array_required_var($params, 'name', true),
      'value' => array_var($params, 'value', 0, true),
      'optional' => (boolean) array_var($params, 'optional', true, true),
      'class' => array_var($params, 'class', '', true),
    );

    $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('select_when_to_send_overdue_reminder');

    AngieApplication::useWidget('select_when_to_send_overdue_reminder', INVOICING_MODULE);
    return '<span id="' . $id . '"></span><script type="text/javascript">$("#' . $id . '").selectWhenToSendOverdueReminder(' . JSON::encode($settings) . ');</script>';
  } // smarty_function_select_when_to_send_overdue_reminder