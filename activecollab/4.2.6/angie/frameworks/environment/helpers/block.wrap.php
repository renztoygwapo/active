<?php

  /**
   * Wrap form field into a DIV
   * 
   * Properties:
   * 
   * - field - field name
   * - errors - errors container (ValidationErrors instance)
   * - show_errors - show errors list inside of field wrapper
   * - aid - Show aid text beneath the field
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_wrap($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    $field = array_required_var($params, 'field', true);
    
    // Classes and wrapper settings
    if(AngieApplication::getPreferedInterface() == AngieApplication::INTERFACE_PHONE || AngieApplication::getPreferedInterface() == AngieApplication::INTERFACE_TABLET) {
      $params['data-role'] = 'fieldcontain';
    } // if
    
    if(empty($params['class'])) {
      $params['class'] = 'control_holder';
    } else {
      $params['class'] .= ' control_holder';
    } // if

    // Allow control to show content outside of container block?
    if(array_var($params, 'visible_overflow', false, true)) {
      $params['class'] .= ' overflow_visible';
    } // if
    
    // Errors
    $field_errors = '';
    
    if(array_var($params, 'show_errors', true, true)) {
      if(isset($params['errors'])) {
        $errors = array_var($params, 'errors', null, true);
      } else {
        $errors = $smarty->getTemplateVars('errors');
      } // if
      
      if($errors instanceof ValidationErrors && $errors->hasError($field)) {
        $params['class'] .= ' error';
        
        $field_errors = '<ul class="field_errors">';
        
        foreach($errors->getFieldErrors($field) as $error_message) {
          $field_errors .= '<li>' . clean($error_message) . '</li>';
        } // if
        
        $field_errors .= '</ul>';
      } // if
    } // if
    
    // Aid
    if(isset($params['aid']) && $params['aid']) {
      $aid = '<p class="aid">' . clean(lang($params['aid'])) . '</p>';
      unset($params['aid']);
    } else {
      $aid = '';
    } // if
    
    // Assemble
    return HTML::openTag('div', $params) . "\n$field_errors\n" . $content . $aid . "\n</div>";
  } // smarty_block_wrap