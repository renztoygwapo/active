<?php

  /**
   * Payment framework initialization file
   *
   * @package angie.frameworks.payments
   */
  
  const PAYMENTS_FRAMEWORK = 'payments';
  const PAYMENTS_FRAMEWORK_PATH = __DIR__;

  defined('PAYMENTS_FRAMEWORK_INJECT_INTO') or define('PAYMENTS_FRAMEWORK_INJECT_INTO', 'system');

  const CUSTOM_PAYMENT = 'Custom Payment';
  const PAYPAL_DIRECT_PAYMENT = 'Paypal Direct Gateway';
  const PAYPAL_EXPRESS_CHECKOUT = 'Paypal Express Checkout Gateway';
  const AUTHORIZE_AIM = 'Authorize AIM Gateway';
  const STRIPE_PAYMENT = 'Stripe Gateway';
  const BRAINTREE_PAYMENT = 'Stripe Gateway';

  AngieApplication::setForAutoload(array(
    'IPayments' => PAYMENTS_FRAMEWORK_PATH . '/models/IPayments.class.php', 
    'IPaymentsImplementation' => PAYMENTS_FRAMEWORK_PATH . '/models/IPaymentsImplementation.class.php', 
    'FwPayment' => PAYMENTS_FRAMEWORK_PATH . '/models/payments/FwPayment.class.php', 
    'FwPayments' => PAYMENTS_FRAMEWORK_PATH . '/models/payments/FwPayments.class.php',
    'IPaymentGateway' => PAYMENTS_FRAMEWORK_PATH . '/models/IPaymentGateway.class.php', 
    'IPaymentGatewaysImplementation' => PAYMENTS_FRAMEWORK_PATH . '/models/IPaymentGatewaysImplementation.class.php', 
    'FwPaymentGateway' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateways/FwPaymentGateway.class.php', 
    'FwPaymentGateways' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateways/FwPaymentGateways.class.php',
    'PaypalDirectGateway' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateways/PaypalDirectGateway.class.php',
    'PaypalExpressCheckoutGateway' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateways/PaypalExpressCheckoutGateway.class.php',
    'PaypalGateway' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateways/PaypalGateway.class.php',
  	'AuthorizeGateway' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateways/AuthorizeGateway.class.php',
    'AuthorizeAimGateway' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateways/AuthorizeAimGateway.class.php',
    'CustomPaymentGateway' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateways/CustomPaymentGateway.class.php',
    'StripeGateway' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateways/StripeGateway.class.php',
    'BrainTreeGateway' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateways/BrainTreeGateway.class.php',

    'PaypalPayment' => PAYMENTS_FRAMEWORK_PATH . '/models/payments/PaypalPayment.class.php',
  	'PaypalDirectPayment' => PAYMENTS_FRAMEWORK_PATH . '/models/payments/PaypalDirectPayment.class.php',
    'PaypalExpressCheckoutPayment' => PAYMENTS_FRAMEWORK_PATH . '/models/payments/PaypalExpressCheckoutPayment.class.php',
  	'AuthorizeNetPayment' => PAYMENTS_FRAMEWORK_PATH . '/models/payments/AuthorizeNetPayment.class.php',
  	'CustomPayment' => PAYMENTS_FRAMEWORK_PATH . '/models/payments/CustomPayment.class.php',
    'StripePayment' => PAYMENTS_FRAMEWORK_PATH . '/models/payments/StripePayment.class.php',
    'BrainTreePayment' => PAYMENTS_FRAMEWORK_PATH . '/models/payments/BrainTreePayment.class.php',

    'PaymentResponse' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateway_response/PaymentResponse.class.php',
    'PaypalDirectPaymentResponse' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateway_response/PaypalDirectPaymentResponse.class.php',
    'PaypalExpressCheckoutResponse' => PAYMENTS_FRAMEWORK_PATH .'/models/payment_gateway_response/PaypalExpressCheckoutResponse.class.php',
    'AuthorizeAimResponse' => PAYMENTS_FRAMEWORK_PATH . '/models/payment_gateway_response/AuthorizeAimResponse.class.php',
  
  	'FwPaymentReport' => PAYMENTS_FRAMEWORK_PATH . '/models/reports/FwPaymentReport.class.php',
  	'FwPaymentReports' => PAYMENTS_FRAMEWORK_PATH . '/models/reports/FwPaymentReports.class.php',
  
  	'FwPaymentSummaryReport' => PAYMENTS_FRAMEWORK_PATH . '/models/reports/FwPaymentSummaryReport.class.php',

    // Notifications
  	'BasePaymentNotification' => PAYMENTS_FRAMEWORK_PATH . '/notifications/BasePaymentNotification.class.php',
  	'NewPaymentNotification' => PAYMENTS_FRAMEWORK_PATH . '/notifications/NewPaymentNotification.class.php',
  	'NewPaymentToPayerNotification' => PAYMENTS_FRAMEWORK_PATH . '/notifications/NewPaymentToPayerNotification.class.php',
  ));