<?php

  /**
   * configure_homescreen helper implementation
   * 
   * @package angie.frameworks.homescreens
   * @subpackage helpers
   */

  /**
   * Configure homescreen helper
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_configure_homescreen($params, &$smarty) {
    AngieApplication::useWidget('manage_homescreen_widgets', HOMESCREENS_FRAMEWORK);

    $parent = array_required_var($params, 'parent', true, 'User');
    $user = array_required_var($params, 'user', true, 'User');
    
    $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('configure_homescreen');

    $default_tab_id = ConfigOptions::getValueFor('default_homescreen_tab_id', $parent);

    // Check if default tab is a valid tab
    if(is_numeric($default_tab_id)) {
      $default_tab_id = (integer) $default_tab_id;

      $default_tab = HomescreenTabs::findById($default_tab_id);

      if(!($default_tab instanceof HomescreenTab && $default_tab->getUserId() == $parent->getId())) {
        $default_tab_id = null;
      } // if
    } // if
    
    $settings = array(
      'add_tab_url' => $parent->homescreen()->getAddTabUrl(),
      'reorder_tabs_url' => $parent->homescreen()->getReorderTabsUrl(),
      'tabs' => array(), 
    );

    $tabs = $parent->homescreen()->getTabs();

    if($tabs) {
      foreach($tabs as $tab) {
        $settings['tabs'][] = $tab->describe($user, true, true);
      } // foreach
    } // if    } // if

    AngieApplication::useWidget('configure_homescreen', HOMESCREENS_FRAMEWORK);
    return '<div id="' . $id . '"></div><script type="text/javascript">$("#' . $id . '").configureHomescreen(' . JSON::encode($settings) . ');</script>';
  } // smarty_function_configure_homescreen