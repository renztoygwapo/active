<?php

  /**
   * Render flash box (success or error message)
   */

  /**
   * Render smarty flash box
   * 
   * No parameters expected
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_flash_box($params, &$smarty) {
    $flash = array_var($params, 'flash');
    
    if($flash instanceof Flash) {
      if($message = $flash->getVariable('success')) {
        return '<script type="text/javascript">App.Wireframe.Flash.success(' . JSON::encode($message) . ')</script>';
      } elseif($message = $flash->getVariable('error')) {
        return '<script type="text/javascript">App.Wireframe.Flash.error(' . JSON::encode($message) . ')</script>';
      } else {
        return '';
      } // if
    } // if
  } // smarty_function_flash_box