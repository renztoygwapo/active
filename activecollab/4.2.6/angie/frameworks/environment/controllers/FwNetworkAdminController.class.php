<?php

// Build on top of administration controller
AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

/**
 * Network admin controller
 *
 * @package angie.frameworks.environment
 * @subpackage controllers
 */
abstract class FwNetworkAdminController extends AdminController {

  /**
   * Index action
   */
  function index() {
    if (!$this->request->isAsyncCall()) {
      $this->response->badRequest();
    } // if

    $network_data = $this->request->post('network');
    if(!is_array($network_data)) {
      $network_data = ConfigOptions::getValue(array(
        'network_proxy_enabled',
        'network_proxy_protocol',
        'network_proxy_address',
        'network_proxy_port'
      ));
    } // if

    $this->smarty->assign(array(
      'network_data' => $network_data
    ));

    if ($this->request->isSubmitted()) {
      try {
        $network_proxy_enabled = (boolean) array_var($network_data, 'network_proxy_enabled');
        $network_proxy_protocol = array_var($network_data, 'network_proxy_protocol');
        $network_proxy_address = trim(array_var($network_data, 'network_proxy_address'));
        $network_proxy_port = (int) array_var($network_data, 'network_proxy_port');

        if ($network_proxy_enabled) {
          $validation_errors = new ValidationErrors();
          if ($network_proxy_port < 1 || $network_proxy_port > 65535) {
            $validation_errors->addError(lang('Proxy port is required, and it has to be number in range 1–65535'), 'network_proxy_port');
          } // if

          if (!$network_proxy_address) {
            $validation_errors->addError(lang('Proxy address is required'), 'network_proxy_address');
          } // if

          if (!(is_valid_url($network_proxy_address) || is_valid_ip_address($network_proxy_address))) {
            $validation_errors->addError(lang('Proxy address has to be valid url or ip address'), 'network_proxy_address');
          } // if

          if ($validation_errors->hasErrors()) {
            throw $validation_errors;
          } // if
        } // if

        ConfigOptions::setValue(array(
          'network_proxy_enabled' => $network_proxy_enabled
        ));

        if ($network_proxy_enabled) {
          ConfigOptions::setValue(array(
            'network_proxy_protocol' => $network_proxy_protocol,
            'network_proxy_address' => $network_proxy_address,
            'network_proxy_port' => $network_proxy_port,
          ));
        } // if

        $this->response->ok();
      } catch (Exception $e) {
        $this->response->exception($e);
      } // try

    } // if
  } // index

} // FwNetworkAdminController