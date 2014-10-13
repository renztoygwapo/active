<?php

  /**
   * object_mass_actions helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render default object link
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_mass_actions($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'ApplicationObject');    
    $user = array_required_var($params, 'user', true, 'IUser');
    
    $mass_actions = array();
    EventsManager::trigger('on_mass_edit', array(&$object, &$user, null, null, &$mass_actions, &$smarty));
    if(count($mass_actions)) {
      $result = '<div class="group"><table class="objects_list_multi_actions">';
      foreach($mass_actions as $mass_action_name => $mass_action) {
        $result .= '<tr>
          <td class="checkbox"><label><input type="checkbox" value="' . $mass_action_name . '" />' . clean($mass_action['title']) . '</label></td>
          <td class="new_value">' . $mass_action['controls'] . '</td>
        </tr>';
      } // foreach
      
      return $result .= '</table></div>';
    } else {
      return '';
    } //if
  } // smarty_function_object_link