<?php

  /**
   * select_visibility helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select visibility control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_visibility($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $object = array_var($params, 'object', null, true, 'IVisibility');

    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_visibility');
    } // if
    
    $optional = array_var($params, 'optional', false, true);
    $value = array_var($params, 'value', ($optional ? null : VISIBILITY_NORMAL));

    // check if object is shared and disable select if true

    if ($object instanceof ISharing) {
      $is_shared = $object->sharing()->isShared() ? lang('Visibility cannot be changed because this :object is shared', array('object' => $object->getVerboseType(true))) : false;
    } else {
      $is_shared = false;
    } //if

    // need warning if turning to private that some users without permission to see private object will be removed from the object assignee or subscriber list
    if($object instanceof IAssignees && $object->visibility()->hasAssigneesWithoutPrivatePermission()) {
      $has_subscribers_or_assignees = array(
        'has' => true,
        'message' => lang("Users who don't have permissions to see private :objects will be unassigned", array('object' => $object->getVerboseType(true)))
      );
    } else if ($object instanceof ISubscriptions && $object->visibility()->hasSubscribersWithoutPrivatePermission()) {
      $has_subscribers_or_assignees = array(
        'has' => true,
        'message' => lang("Users who don't have permissions to see private :objects will be unsubscribed", array('object' => $object->getVerboseType(true)))
      );
    } else {
      $has_subscribers_or_assignees = false;
    } // if

    $json = array(
      // disable if shared
      'is_shared' => $is_shared,
      'has_subscribers_or_assignees' => $has_subscribers_or_assignees,
    );
    
    if(isset($params['class'])) {
      $params['class'] .= ' select_visibility';
    } else {
      $params['class'] = 'select_visibility';
    } // if
    
    $possibilities = array(
      VISIBILITY_NORMAL => lang('Normal'), 
      VISIBILITY_PRIVATE => lang('Private'), 
    );
    
    if($optional) {
      if(ConfigOptions::getValue('default_project_object_visibility') == VISIBILITY_NORMAL) {
        $label = lang('-- System Default (Normal) --');
      } else {
        $label = lang('-- System Default (Private) --');
      } // if
      
      $return = HTML::optionalSelectFromPossibilities($name, $possibilities, $value, $params, $label, '');
    } else {
      $return = HTML::selectFromPossibilities($name, $possibilities, $value, $params);
    } // if

    AngieApplication::useWidget('select_visibility', ENVIRONMENT_FRAMEWORK);
    return $return . '<script type="text/javascript">$("#' . $params['id'] . '").selectVisibility(' . JSON::encode($json) . ')</script>';
  } // smarty_function_select_visibility