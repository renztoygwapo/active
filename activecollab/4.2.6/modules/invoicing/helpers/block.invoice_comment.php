<?php

  /**
   * invoice_comment helper implementation
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Render invoice comment field
   * 
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   */
  function smarty_block_invoice_comment($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    if(isset($params['class'])) {
      $params['class'] .= ' invoice_comment';
    } else {
      $params['class'] = 'invoice_comment';
    } // if
    
    $params['maxlength'] = 250;
    
    AngieApplication::useHelper('textarea_field', ENVIRONMENT_FRAMEWORK, 'block');
    
    return smarty_block_textarea_field($params, $content, $smarty, $repeat);
  } // smarty_block_invoice_comment