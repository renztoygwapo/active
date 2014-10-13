<?php

  /**
   * select_default_homescreen_tab home screen tab helper
   *
   * @package angie.frameworks.homescreens
   * @subpackage helpers
   */

  /**
   * Render select default home screen tab widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_default_homescreen_tab($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'User');
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', null, true);

    $options = array();

    $predefined_tabs = array();
    EventsManager::trigger('on_predefined_homescreen_tabs', array(&$user, &$predefined_tabs));

    if($predefined_tabs && is_foreachable($predefined_tabs)) {
      foreach($predefined_tabs as $tab_key => $tab_label) {
        $options[] = HTML::optionForSelect($tab_label, $tab_key, $value == $tab_key);
      } // foreach
    } // if

    $user_tabs = HomescreenTabs::getIdNameMap($user);

    if($user_tabs && is_foreachable($user_tabs)) {
      $custom_tab_possibilities = array();

      foreach($user_tabs as $user_tab_id => $user_tab_label) {
        $custom_tab_possibilities[] = HTML::optionForSelect($user_tab_label, $user_tab_id, $user_tab_id == $value);
      } // foreach

      if(count($options)) {
        $options[] = HTML::optionForSelect('', '');
      } // if

      $options[] = HTML::optionGroup(lang('Custom Tabs'), $custom_tab_possibilities);
    } // if

    if(array_var($params, 'optional', true, true)) {
      return HTML::optionalSelect($name, $options, $params, lang('-- System Default (:value) --', array(
        'value' => $user instanceof Client ? lang("What's New") : lang('My Tasks'),
      )), '');
    } else {
      return HTML::select($name, $options, $params);
    } // if
  } // smarty_function_select_default_homescreen_tab