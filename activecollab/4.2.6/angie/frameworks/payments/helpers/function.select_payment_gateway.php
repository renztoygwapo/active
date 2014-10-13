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
  function smarty_function_select_payment_gateway($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');
    $active_payment_gateway = array_var($params, 'active_payment_gateway',null, true);
    
    $options = array();
    if(!$active_payment_gateway) {
      $gateways = array();
  	  EventsManager::trigger('on_new_gateway',array(&$gateways, &$user));
  	  if($gateways) {
        foreach($gateways as $gateway) {
          $options[get_class($gateway)] = array(
            'name' => $gateway->getGatewayName(),
            'description' => $gateway->getGatewayDescription(), 
            'options' => $gateway->renderOptions($user),
          );
        } // foreach
      } // if
    } else {
      $options[] = array(
        'name' => $active_payment_gateway->getGatewayName(),
        'description' => $active_payment_gateway->getGatewayDescription(), 
        'options' => $active_payment_gateway->renderOptions($user),
      );
      $edit_mode = true;
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
    
    return HTML::openTag('div', $params) . '</div><script type="text/javascript">$("#' . $params['id'] . '").selectPaymentGateway(' . JSON::encode(array(
      'types' => $options, 'edit_mode' => $edit_mode
    )) . ')</script>';
  } // smarty_function_select_payment_gateway