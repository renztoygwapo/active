<?php

  /**
   * select_task helper implementation
   *
   * @package activeCollab.modules.tracking
   * @subpackage helpers
   */

  /**
   * Select task
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_task($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');

    $settings = array(
      'name' => array_required_var($params, 'name', true),
      'value' => array_var($params, 'value', 0, true),
      'optional' => (boolean) array_var($params, 'optional', true, true)
    );

    $settings['tasks'] = Tasks::getIdNameMapByUser($user);

    $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('select_task');

    AngieApplication::useWidget('select_task', TRACKING_MODULE);
    return '<span id="' . $id . '"></span><script type="text/javascript">$("#' . $id . '").selectTask(' . JSON::encode($settings) . ');</script>';
  } // smarty_function_select_task