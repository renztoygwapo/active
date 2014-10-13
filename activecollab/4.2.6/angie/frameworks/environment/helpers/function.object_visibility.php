<?php

  /**
   * Visibility helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Show object visibility if it's private
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_visibility($params, &$smarty) {
  	$object = array_var($params, 'object', true, 'IVisibility');
    $user = array_var($params, 'user', true, 'IUser');
    
    if($object->getVisibility() > VISIBILITY_PRIVATE || !$user->canSeePrivate()) {
      return '';
    } // if
    
    if(isset($params['class'])) {
      $params['class'] = ' object_visiblity';
    } else {
      $params['class'] = 'object_visiblity';
    } // if
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('object_visibility');
    } // if
    
    $params['title'] = $object->visibility()->getStatement(true);
    
    return HTML::openTag('span', $params) . '<img src="' . AngieApplication::getImageUrl('icons/16x16/private.png', ENVIRONMENT_FRAMEWORK) . '"></span>';
  } // smarty_function_object_visibility