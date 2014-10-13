<?php

  /**
   * Render project tabs helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select project tabs widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project_tabs($params, &$smarty) {
    static $ids = array();
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $counter = 1;
      do {
        $id = 'select_project_tabs_' . $counter++;
      } while(in_array($id, $ids));
    } // if
    
    $ids[] = $id;
    
    $tabs = array();
    EventsManager::trigger('on_available_project_tabs', array(&$tabs));
    
    $value = isset($params['value']) && is_array($params['value']) ? $params['value'] : array();
    
    // Lets make sure that we have only tab names that actually are available
    if(is_foreachable($value)) {
      $available_tab_names = array_keys($tabs);
      foreach($value as $k => $v) {
        if($v == '-' || isset($tabs[$v])) {
          continue;
        } // if
        
        unset($value[$k]);
      } // foreach
    } // if
    
    $smarty->assign(array(
      '_select_project_tabs_id' => $id,
      '_select_project_tabs_name' => array_var($params, 'name'), 
      '_select_project_tabs_value' => $value, 
      '_select_project_tabs' => $tabs,
    ));
    
    return $smarty->fetch(get_view_path('_select_project_tabs', null, SYSTEM_MODULE));
  } // smarty_function_select_project_tabs