<?php

  /**
   * smarty_function_select_payment_gateway helper implementation
   * 
   * @package angie.frameworks.payments
   * @subpackage helpers
   */

  /**
   * Render select_payment_gateway helper
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_make_payment($params, &$smarty) {
    $user = array_var($params, 'user', true, 'IUser');
    
    $object = array_var($params, 'object', true, true);

    $is_public = array_var($params, 'is_public', false, true);

    $options = array();

    $payment_data = $smarty->getTemplateVars('payment_data');

    $payment_gateway_id = $payment_data['payment_gateway_id'];

    if($object instanceof IPayments && $object->payments()->isEnabled()) {
      $gateways = PaymentGateways::findAllCurrencySupported($object->getCurrency()->getCode());
  	  if($gateways) {
        foreach($gateways as $gateway) {
          $hidden_fileds = "<input type='hidden' value='" . $gateway->getId() . "' name='payment_gateway_id' />";
          $selected = false;
          if($payment_gateway_id) {
            if($payment_gateway_id == $gateway->getId()) {
              $selected = true;
            } //if
          } else {
            $selected = $gateway->isDefault();
          } //if
          $options[] = array(
            'name' => $gateway->getName(),
            'description' => $gateway->getGatewayDescription(),
            'options' => $gateway->renderPaymentForm($user) . $hidden_fileds,
            'selected' => $selected
          );
        } // foreach
      } // if
    }//if
    
    if($user instanceof User && $user->isFinancialManager()) {
      $custom_gateway = new CustomPaymentGateway();
      $hidden_ = "<input type='hidden' value='0' name='payment_gateway_id' />"; 
      $options[] = array(
        'name' => lang('Custom Payment'),
        'description' => $custom_gateway->getGatewayDescription(), 
        'options' => $custom_gateway->renderPaymentForm($user) . $hidden_,
      );
    }//if  

    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_payment_gateway');
    } // if
    
    if(empty($params['class'])) {
      $params['class'] = 'select_payment_gateway';
    } else {
      $params['class'] .= ' select_payment_gateway';
    } // if

    AngieApplication::useWidget('select_payment_gateway', PAYMENTS_FRAMEWORK);

    //for public interface without logged user
    $object_info = array(
      'short_name' => $object->getName(true),
      'paid_amount' => $object->getPaidAmount(true),
      'total' => $object->getTotal(true),
      'currency' => array(
        'code' => $object->getCurrency()->getCode(),
        'is_default' => $object->getCurrency()->getIsDefault(),
        'decimal_spaces' => $object->getCurrency()->getDecimalSpaces(),
        'decimal_rounding' => $object->getCurrency()->getDecimalRounding()
      ),
    );

    $logged_user = Authentication::getLoggedUser();
    if($logged_user instanceof User) {
      $logged_user = array(
        'email' => $logged_user->getEmail(),
        'display_name' => $logged_user->getDisplayName()
      );
    } //if



    return HTML::openTag('div', $params) . '</div><script type="text/javascript">$("#' . $params['id'] . '").selectPaymentGateway(' . JSON::encode(array(
      'types' => $options, 'is_public' => $is_public, 'object_info' => $object_info, 'logged_user' => $logged_user, 'payment_data' => $payment_data
    )) . ')</script>';
  } // smarty_function_select_payment_gateway