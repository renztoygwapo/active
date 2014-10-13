<?php

  /**
   * select_homescreen_widget_type helper implementation
   * 
   * @package angie.frameworks.homescreens
   * @subpackage helpers
   */

  /**
   * Render select_homescreen_widget_type helper
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_homescreen_widget_type($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');

    $homescreen_widget_types = Homescreens::getWidgetTypes($user);
    
    $grouped_types = array();
    
    if($homescreen_widget_types) {
      $other_widgets = array();
      
      foreach($homescreen_widget_types as $homescreen_widget_type) {
        $group_name = $homescreen_widget_type->getGroupName();
        
        if($group_name) {
          if(!is_array($grouped_types[$group_name])) {
            $grouped_types[$group_name] = array();
          } // if
          
          $grouped_types[$group_name][get_class($homescreen_widget_type)] = array(
            'name' => $homescreen_widget_type->getName(),
            'description' => $homescreen_widget_type->getDescription(), 
            'options' => $homescreen_widget_type->renderOptions($user),
          );
        } else {
          $other_widgets[get_class($homescreen_widget_type)] = array(
            'name' => $homescreen_widget_type->getName(),
            'description' => $homescreen_widget_type->getDescription(), 
            'options' => $homescreen_widget_type->renderOptions($user),
          );
        } // if
      } // foreach
      
      ksort($grouped_types);
      
      $grouped_types[lang('Other')] = $other_widgets;
    } // if
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_homescreen_widget_type');
    } // if
    
    if(empty($params['class'])) {
      $params['class'] = 'select_homescreen_widget_type';
    } else {
      $params['class'] .= ' select_homescreen_widget_type';
    } // if

    AngieApplication::useWidget('select_homescreen_widget_type', HOMESCREENS_FRAMEWORK);
    return HTML::openTag('div', $params) . '</div><script type="text/javascript">$("#' . $params['id'] . '").selectHomescreenWidgetType(' . JSON::encode(array(
      'types' => $grouped_types
    )) . ')</script>';
  } // smarty_function_select_homescreen_widget_type