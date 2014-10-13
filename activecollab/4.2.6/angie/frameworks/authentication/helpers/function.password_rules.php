<?php
/**
 * Class description
 *
 * @package
 * @subpackage
 */
function smarty_function_password_rules($params, &$smarty) {
  $password_policy = Authentication::getPasswordPolicy();

  if($password_policy instanceof ConfigurablePasswordPolicy) {
    $rules = array();

    if(Authentication::getPasswordPolicy()->getMinLength()) {
      $rules[] = lang('Minimal password length is :min_length letters', array(
        'min_length' => Authentication::getPasswordPolicy()->getMinLength(),
      ));
    } // if

    if(Authentication::getPasswordPolicy()->requireNumbers()) {
      $rules[] = lang('At least one number is required');
    } // if

    if(Authentication::getPasswordPolicy()->requireMixedCase()) {
      $rules[] = lang('At least one lower case and one uppercase letter are required');
    } // if

    if(Authentication::getPasswordPolicy()->requireSymbols()) {
      $rules[] = lang('At least one of the following symbols is required: , . ; : ! $ % ^ &');
    } // if

    if(count($rules)) {
      if(isset($params['class']) && $params['class']) {
        $params['class'] .= ' password_rules';
      } else {
        $params['class'] = 'password_rules';
      } // if

      $result = HTML::openTag('div', $params) . '<p>' . lang('Password Rules') . ':</p><ul>';
      foreach($rules as $rule) {
        $result .= '<li>' . clean($rule) . '</li>';
      } // foreach
      return "$result</div>";
    } // if
  } // if
} // smarty_function_password_rules