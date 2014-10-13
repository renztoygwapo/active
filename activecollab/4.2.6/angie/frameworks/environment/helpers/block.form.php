<?php

  /**
   * Render form block
   *
   * Parameters:
   *
   * - All FORM element attributes
   * - block_labels - Use block or inline labels
   * - autofocus - Automatically focus first field, true by default
   * - ask_on_leave - Ask users confirmation when form data is changed and user
   *   tries to navigate of the page. This setting is off by default
   * - disable_submit - If true Submit button will be disabled until all values
   *   in the form are valid. Off by default...
   * - show_errors - Display errors
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_form($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    if(empty($params['method'])) {
      $params['method'] = 'post';
    } // if
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('form');
    } // if
    
    if($interface == AngieApplication::INTERFACE_PHONE || $interface == AngieApplication::INTERFACE_TABLET) {
      $params['data-ajax'] = 'false';
    } // if
    
    $errors = array_var($params, 'errors', null, true);
    if(empty($errors)) {
      $errors = $smarty->getTemplateVars('errors');
    } // if
        
    $result = HTML::openTag('form', $params);
    
    if($errors) {
      $result .= '<div class="form_errors">';
      
      if($errors instanceof ValidationErrors) {
        $result .= '<p>' . lang('Oops! We found some errors that need to be corrected before we can proceed') . '</p>';
        
        if($errors->hasErrors(ValidationErrors::ANY_FIELD)) {
          $result .= '<ul>';
          
          foreach($errors->getFieldErrors(ValidationErrors::ANY_FIELD) as $error_message) {
            $result .= '<li>' . clean($error_message) . '</li>';
          } // foreach
          
          $result .= '</ul>';
        } // if
      } elseif($errors instanceof Error) {
        $result .= '<p>' . lang('Oops!') . ' ' . $errors->getMessage() . '</p>';
      } else {
        $result .= '<p>' . lang('Oops!') . ' ' . $errors . '</p>';
      } // if
      
      $result .= '</div>';
    } // if
    
    $result .= $content;
    
    if(strtolower($params['method']) == 'post') {
      $result .= '<input type="hidden" name="submitted" value="submitted" style="display: none">';
      
      if(isset($params['csfr_protect']) && $params['csfr_protect']) {
        $result .= '<input type="hidden" name="csfr_code" value="' . clean(AngieApplication::getCsfrProtectionCode()) . '" style="display: none">';
      } // if
    } // if
    
    return $result . '</form>';
  } // smarty_block_form