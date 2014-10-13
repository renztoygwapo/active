<?php

  /**
   * project_exporter_object_comments helper
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */
  
  /**
   * Show a list of comments
   *
   * Parameters:
   * 
   * - object - Comments parent object
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_exporter_object_comments($params, $template) {
    $object = array_required_var($params, 'object', null, 'IComments');
    $visibility = array_var($params, 'visibility', $template->tpl_vars['visibility']->value);
    
    AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');

    $return = '';
    $comments = Comments::findByObject($object);
    if (is_foreachable($comments)) {
      foreach ($comments as $comment) {
      	$return.= '<li class="comment">';
      	$return.= '<p class="author_info">' . smarty_function_project_exporter_user_link(array('id' => $comment->getCreatedById(), 'name' => $comment->getCreatedByName(), 'email' => $comment->getCreatedByEmail()), $template) . '<span class="date">' . lang('on') . ' ' . smarty_modifier_date($comment->getCreatedOn()) . '</span></p>';
      	$return.= '<div class="body">' . HTML::toRichText($comment->getBody()) . '</div>';
      	$return.= smarty_function_project_exporter_object_attachments(array('object' => $comment), $template);
      	$return.= '</li>';
      };
      
      $return = '<div id="object_comments" class="object_info"><h3>' . lang('Comments'). '</h3><ul class="comments">' . $return . '</ul></div>';
    } // if
    return $return;
  } // smarty_function_project_exporter_object_comments