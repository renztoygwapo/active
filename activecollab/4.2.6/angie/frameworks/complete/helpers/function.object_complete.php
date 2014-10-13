<?php

  /**
   * object_complete helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render complete / open widget for an object
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_complete($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'IComplete');
    $user = array_required_var($params, 'user', true, 'IUser');
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('object_complete_reopen');
    } // if
    
    if($object->can_be_completed && $object->complete()->canChangeStatus($user)) {
      if($object->getCompletedOn()) {
        $params = array(
          'id'    => $params['id'],
          'href'  => $object->complete()->getOpenUrl(),
          'title' => lang('Reopen task'),
          'class' => 'reopen_task'
        );
        
        $result = HTML::openTag('a', $params) . '<img src="' . AngieApplication::getImageUrl('icons/16x16/checked.png', SYSTEM_MODULE) . '" alt="" /></a>';
      } else {
        $params = array(
          'id'    => $params['id'],
          'href'  => $object->complete()->getCompleteUrl(s),
          'title' => lang('Complete task'),
          'class' => 'complete_task',
        );
        
        $result = HTML::openTag('a', $params) . '<img src="' . AngieApplication::getImageUrl('icons/16x16/not-checked.png', SYSTEM_MODULE) . '" alt="" /></a>';
      } // if
      
      return $result . "\n<script type=\"text/javascript\">App.layout.init_complete_open_link('" . $params['id'] . "')</script>";
    } else {
      return '';
    } // if
  } // smarty_function_object_complete