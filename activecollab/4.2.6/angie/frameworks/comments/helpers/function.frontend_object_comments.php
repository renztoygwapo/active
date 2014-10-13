<?php

  /**
   * frontend_object_comments helper implementation
   * 
   * @package angie.frameworks.comments
   * @subpackage comments
   */

  /**
   * Render frontend object comments
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_frontend_object_comments($params, &$smarty) {
    $object = array_required_var($params, 'object', false, 'IComments');
    $user = array_required_var($params, 'user');
    
    if(empty($user) && empty($params['comment_data'])) {
      $params['comment_data'] = array(
        'created_by_name' => Authentication::getVisitorName(), 
        'created_by_email' => Authentication::getVisitorEmail(), 
      );
    } // if

    $errors = array_var($params, 'errors', null, true);
    
    if($object->comments()->isLocked()) {
      $post_comment_url = false;
    } else {
      $post_comment_url = array_var($params, 'post_comment_url');
    } // if
    
    $template = $smarty->createTemplate(get_view_path('_frontend_object_comments', null, COMMENTS_FRAMEWORK));
    $template->assign(array(
      'errors' => $errors,
      'object' => $object, 
      'user' => $user,
      'id' => isset($params['id']) && $params['id']  ? $params['id'] : HTML::uniqueId('object_comments'), 
      'post_comment_url' => $post_comment_url, 
      'comment_data' => $post_comment_url && isset($params['comment_data']) && $params['comment_data']  ? $params['comment_data'] : array(), 
    ));
    
    return $template->fetch();
  } // smarty_function_frontend_object_comments