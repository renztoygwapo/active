<?php

  /**
   * on_admin_panel event handler
   * 
   * @package angie.frameworks.payments
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function payments_handle_on_admin_panel(AdminPanel &$admin_panel) {
    $admin_panel->addToTools('payment_gateways', lang('Payment Settings'), Router::assemble('payment_gateways_admin_section'), AngieApplication::getImageUrl('admin_panel/payment-gateway.png', PAYMENTS_FRAMEWORK));
  } // payments_handle_on_admin_panel