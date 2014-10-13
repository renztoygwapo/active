<?php

  /**
   * object_link helper
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
  function smarty_function_object_link($params, &$smarty) {
    if(isset($params['object']) && $params['object'] instanceof ApplicationObject) {
      $excerpt = isset($params['excerpt']) ? $params['excerpt'] : null;
      $additional = isset($params['additional']) ? $params['additional'] : null;
      $quick_view = isset($params['quick_view']) ? $params['quick_view'] : null;

      return object_link($params['object'], $excerpt, $additional, $quick_view);
    } else {
      throw new InvalidInstanceError('object', $params['object'], 'ApplicationObject');
    } // if
  } // smarty_function_object_link