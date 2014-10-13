<?php

  /**
   * assign_var helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Assign generated value to template
   * 
   * Params:
   * 
   * - name - Variable name
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return null
   * @throws InvalidParamError
   */
  function smarty_block_assign_var($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    $name = trim(array_var($params, 'name'));
    if($name == '') {
      throw new InvalidParamError('name', $name, 'name value is missing', true);
    } // if
    
    $smarty->assign($name, $content);
  } // smarty_block_assign