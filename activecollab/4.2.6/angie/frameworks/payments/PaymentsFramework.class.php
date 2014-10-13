<?php

  /**
   * Payment framework definiton
   *
   * @package angie.frameworks.payments
   */
  class PaymentsFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'payments';
   
    /**
     * Define payments framework routes
     */
    function defineRoutes() {
     
      // Payments
      Router::map('admin_paypal_express_checkout_return_url', 'admin/express/checkout/return/url', array('controller' => 'make_returning_payment', 'action' => 'paypal_express_checkout_return', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO));	
	    Router::map('admin_paypal_express_checkout_cancel_url', 'admin/express/checkout/cancel/url', array('controller' => 'make_returning_payment', 'action' => 'cancel_from_gateway', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO));  
      
	    // Payments gateway admin index
      Router::map('payment_gateways_admin_section', 'payment/gateways/admin', array('controller' => 'payment_gateways_admin', 'action' => 'index', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO));	
  	  Router::map('payment_gateways_allow_payments', 'admin/allow/payments/change', array('controller' => 'payment_gateways_admin', 'action' => 'allow_payments', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO));	
  	  Router::map('payment_gateways_allow_payments_for_invoice', 'admin/allow/invoices/payments/change', array('controller' => 'payment_gateways_admin', 'action' => 'allow_payments_for_invoice', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO));	
      Router::map('payment_gateways_enforce_settings', 'admin/invoices/payments/enforce', array('controller' => 'payment_gateways_admin', 'action' => 'enforce_settings', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO));	
      Router::map('payment_gateways_settings', 'admin/invoices/payments/settings', array('controller' => 'payment_gateways_admin', 'action' => 'settings', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO));	
      
  	  Router::map('admin_payment_gateway_edit', 'payment/gateways/:payment_gateway_id/edit', array('controller' => 'payment_gateways_admin', 'action' => 'edit', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO), array('payment_gateway_id' => '\d+'));	
  	  Router::map('admin_payment_gateway_view', 'payment/gateways/:payment_gateway_id/view', array('controller' => 'payment_gateways_admin', 'action' => 'view', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO), array('payment_gateway_id' => '\d+'));	
  	  Router::map('admin_payment_set_as_default', 'payment/gateways/:payment_gateway_id/set_as_default', array('controller' => 'payment_gateways_admin', 'action' => 'set_as_default', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO), array('payment_gateway_id' => '\d+'));	
  	  Router::map('admin_payment_gateway_delete', 'payment/gateways/:payment_gateway_id/delete', array('controller' => 'payment_gateways_admin', 'action' => 'delete', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO), array('payment_gateway_id' => '\d+'));	
      Router::map('admin_payment_gateway_add', 'payment/gateways/add', array('controller' => 'payment_gateways_admin', 'action' => 'add', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO));	
  	  Router::map('admin_payment_disable', 'payment/gateways/:payment_gateway_id/disable', array('controller' => 'payment_gateways_admin', 'action' => 'disable', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO), array('payment_gateway_id' => '\d+'));	
  	  Router::map('admin_payment_enable', 'payment/gateways/:payment_gateway_id/enable', array('controller' => 'payment_gateways_admin', 'action' => 'enable', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO), array('payment_gateway_id' => '\d+'));	
  	  
  	  Router::map('payment_methods_settings', 'admin/invoices/payments/methods/settings', array('controller' => 'payment_gateways_admin', 'action' => 'methods', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO));	
      
  	  //Router::map('payments_report', 'reports/payments', array('controller' => 'payments_reports','module' => PAYMENTS_FRAMEWORK_INJECT_INTO));	
  	 // Router::map('payments_report_run', 'reports/payments/run', array('controller' => 'payments_reports', 'action' => 'run', 'module' => PAYMENTS_FRAMEWORK_INJECT_INTO));	
  	  
  	  
  	  AngieApplication::getModule('reports')->defineDataFilterRoutes('payments_report', 'payments/report', 'payments_reports', PAYMENTS_FRAMEWORK_INJECT_INTO);
  	  AngieApplication::getModule('reports')->defineDataFilterRoutes('payments_summary_report', 'payments/summary', 'payments_summary_reports', PAYMENTS_FRAMEWORK_INJECT_INTO);
      
  	} // defineRoutes
    
    /**
     * Define payment routes for given context
     * 
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function definePaymentRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $payment_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('payment_id' => '\d+')) : array('payment_id' => '\d+');
   
      Router::map("{$context}_payments", "$context_path/payments", array('controller' => $controller_name, 'action' => "{$context}_payments", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_payments_add", "$context_path/payments/add", array('controller' => $controller_name, 'action' => "{$context}_payments_add", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_custom_payments_add", "$context_path/custom/payments/add", array('controller' => $controller_name, 'action' => "{$context}_custom_payments_add", 'module' => $module_name), $context_requirements);
       
      Router::map("{$context}_payment", "$context_path/payments/:payment_id", array('controller' => $controller_name, 'action' => "{$context}_payment_view", 'module' => $module_name), $payment_requirements);
      Router::map("{$context}_payment_edit", "$context_path/payments/:payment_id/edit", array('controller' => $controller_name, 'action' => "{$context}_payment_edit", 'module' => $module_name), $payment_requirements);
      Router::map("{$context}_payment_delete", "$context_path/payments/:payment_id/delete", array('controller' => $controller_name, 'action' => "{$context}_payment_delete", 'module' => $module_name), $payment_requirements);
      
    } // definePaymentRoutesFor
    
    /**
     * Define payment routes for given context
     * 
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineCustomPaymentRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $context_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('payment_id' => '\d+')) : array('payment_id' => '\d+');
   
      Router::map("{$context}_custom_payments_add", "$context_path/payments/add", array('controller' => $controller_name, 'action' => "{$context}_payments_add", 'module' => $module_name), $context_requirements);
    } // definePaymentRoutesFor
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
      EventsManager::listen('on_new_gateway','on_new_gateway');
      EventsManager::listen('on_reports_panel','on_reports_panel');
      EventsManager::listen('on_payment_methods','on_payment_methods');
    } // defineHandlers
    
  }