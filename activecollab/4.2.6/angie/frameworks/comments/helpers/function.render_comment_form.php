<?php

  /**
   * render_comment_form helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage modules
   */

  /**
   * Display post comment form
   * 
   * Required:
   * 
   * - object
   * - id
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_render_comment_form($params, &$smarty) {
    $object = array_required_var($params, 'object');
    $id = array_required_var($params, 'id');
    
    if($object->comments()->isLocked()) {
      $post_comment_url = false;
    } else {
      $post_comment_url = $object->comments()->getPostUrl();
    } // if
    
    $template = $smarty->createTemplate(get_view_path('_render_comment_form', null, COMMENTS_FRAMEWORK, AngieApplication::INTERFACE_PHONE));
    $template->assign(array(
      'object' => $object,
      'id' => $id,
      'post_comment_url' => $post_comment_url
    ));
    
    return $template->fetch();
  } // smarty_function_render_comment_form