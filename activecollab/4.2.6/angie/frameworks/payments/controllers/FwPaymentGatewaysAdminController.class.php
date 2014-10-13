<?php
 // Build on top of admin controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level payment gateway administration controller
   *
   * @package angie.frameworks.payments
   * @subpackage controllers
   */
  abstract class FwPaymentGatewaysAdminController extends AdminController {
  	
    /**
     * Active payment gateway
     * 
     *  @var PaymentGateway object
     */
    protected $active_payment_gateway = false;
    
    /**
     * Default payment gateway
     * 
     * @var PaymentGateway object
     */
    protected $default_payment_gateway = false;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
   
      $payment_gateway_id = $this->request->get('payment_gateway_id');
  	  if($payment_gateway_id) {
  	     $this->active_payment_gateway = PaymentGateways::findById($payment_gateway_id);
  	  } else {
  	     $this->active_payment_gateway = new PaymentGateway();
  	  }//if
  	  
  	  $this->default_payment_gateway = PaymentGateways::findDefault();
  	  
  	  $this->wireframe->actions->add('payment_gateways_admin_add', lang('New Payment Gateway'), Router::assemble('admin_payment_gateway_add'), array(
  	    'onclick' => new FlyoutFormCallback(array('success_event'=>'payment_gateway_created','width' => 650)), 
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),  	    
  	  ));
  	 
  	  $this->response->assign(array(
  	  	'payment_gateway' => $this->active_payment_gateway,
  	    'default_gateway' => $this->default_payment_gateway,
  	    'allow_payments_url' => PaymentGateway::getAllowPaymentsUrl(),
  	    'allow_payments_for_invoice_url' => PaymentGateway::getAllowPaymentsForInvoiceUrl(),
  	    'allow_payments' => ConfigOptions::getValue('allow_payments'),
        'allow_payments_for_invoice' => ConfigOptions::getValue('allow_payments_for_invoice'),
  	    'enforce_payment_settings_url' => PaymentGateway::getEnforceSettingsUrl()
  	  )); 	    	  
  	} // __construct

  	/**
  	 * Index of payment gateways administration page
  	 */
  	function index() {
  	  $this->wireframe->breadcrumbs->add('payment_gateways', lang('Payment Settings'), Router::assemble('payment_gateways_admin_section')); 
  	 
  	  $payments_gateways_per_page = 20;
    	
    	if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(PaymentGateways::getSlice($payments_gateways_per_page, $exclude, $timestamp));
    	} else {
    		$this->response->assign(array(
    		  'payment_gateways' => PaymentGateways::getSlice($payments_gateways_per_page), 
    		  'payment_gateways_per_page' => $payments_gateways_per_page, 
    		  'total_payment_gateways' => PaymentGateways::count(), 
    		));
    	} // if
  	} // index

  	/**
  	 * View payment gateway details
  	 */
  	function view() {
  	  $this->wireframe->breadcrumbs->add('payment_gateways', lang('Payment Settings'),$this->active_payment_gateway->getMainPageUrl()); 
  	  $this->wireframe->breadcrumbs->add('payment_gateway', $this->active_payment_gateway->getName(),$this->active_payment_gateway->getViewUrl()); 
  	} //view
  	
  	/**
  	 * Add paypal direct payment gateway
  	 * 
  	 */
  	function add() {
  	  if($this->request->isAsyncCall() || $this->request->isApiCall()) {
    	  $payment_gateway_data = $this->request->post('payment_gateway');
    	  
    	  if($this->request->isSubmitted()) {
    	    try {
    	      DB::beginWork('Add new payment gateway @ ' . __CLASS__);

    	      $payment_type = $payment_gateway_data['type'];
    	      $this->active_payment_gateway = new $payment_type();

            //if this method exists, please check is all necessery extension loaded
            if(method_exists($this->active_payment_gateway, 'checkEnvironment')) {
              $this->active_payment_gateway->checkEnvironment();
            }//if

    	      $this->active_payment_gateway->setAdditionalProperties(array_var($payment_gateway_data,'additional_properties'));
    	      $this->active_payment_gateway->setAttributes($payment_gateway_data);
    	      
    	      $this->active_payment_gateway->save();
    	      DB::commit('New payment gateway added @ ' . __CLASS__);
              $this->response->respondWithData($this->active_payment_gateway, array('as' => 'payment_gateway'));
      	    } catch(Error $e) {
    	        DB::rollback('Failed to add new payment gateway @ ' . __CLASS__);
              $this->response->exception($e);
    	    }//try
    	  }//if 
  	  } else {
  	    $this->response->badRequest();
  	  }//if
  	} //add
  	  	
  	/**
  	 * Edit payment gateway
  	 */
  	function edit() {
  	  if($this->request->isAsyncCall() || $this->request->isApiCall()) {
    	  if(!$this->active_payment_gateway instanceof PaymentGateway) {
    	    $this->response->notFound();
    	  } //if
         	  
        $payment_gateway_data = $this->request->post('payment_gateway');
        if(!is_array($payment_gateway_data)) {
        	$payment_gateway_data = array(      	
        	  'name' => $this->active_payment_gateway->getName(),
        	  'api_username' => $this->active_payment_gateway->getApiUsername(),
        	  'api_password' => $this->active_payment_gateway->getApiPassword(),
        	  'api_signature' => $this->active_payment_gateway->getApiSignature(),
        	  'api_login_id' => $this->active_payment_gateway->getApiLoginId(),
        	  'transaction_id' => $this->active_payment_gateway->getTransactionId(),
        	  'go_live' => $this->active_payment_gateway->getGoLive(),
        	  'is_default' => $this->active_payment_gateway->getIsDefault(),
            'api_key' => method_exists($this->active_payment_gateway,'getApiKey') ? $this->active_payment_gateway->getApiKey() : null,
            'merchant_key' => method_exists($this->active_payment_gateway,'getMerchantKey') ? $this->active_payment_gateway->getMerchantKey() : null,
            'public_key' => method_exists($this->active_payment_gateway,'getPublicKey') ? $this->active_payment_gateway->getPublicKey() : null,
            'private_key' => method_exists($this->active_payment_gateway,'getPrivateKey') ? $this->active_payment_gateway->getPrivateKey() : null,
            'merchant_account_ids' => method_exists($this->active_payment_gateway,'getMerchantAccountIds') ? $this->active_payment_gateway->getMerchantAccountIds() : null
        	);
        } // if

        $this->smarty->assign(array(
        	'payment_gateway_data' => $payment_gateway_data,
        ));
    	   
      	if($this->request->isSubmitted()) {
      	  try {
      	    
      	    DB::beginWork('Edit payment gateway @ ' . __CLASS__);

            //if this method exists, please check is all necessery extension loaded
            if(method_exists($this->active_payment_gateway, 'checkEnvironment')) {
              $this->active_payment_gateway->checkEnvironment();
            }//if

            $this->active_payment_gateway->setAdditionalProperties(array_var($payment_gateway_data,'additional_properties'));
            $this->active_payment_gateway->setAttributes($payment_gateway_data);
            $this->active_payment_gateway->save();
            
      	    DB::commit('Payment gateway edited @ ' . __CLASS__);
      	    
            $this->response->respondWithData($this->active_payment_gateway, array(
              'as' => 'payment_gateway', 
              'detailed' => true, 
            ));
      	  } catch (Error $e) {
      	    DB::rollback('Failed to edit payment gateway @ ' . __CLASS__);
            $this->response->exception($e);
      	  }//try
        } //if
  	  } else {
  	    $this->response->badRequest();
  	  }//if
  	} //edit
  	
  	/**
  	 * Delete payment gateway
  	 */
  	function delete() {
  	  if ($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        if ($this->active_payment_gateway->isNew()) {
          $this->response->notFound();
        } // if
        
        try {
          $this->active_payment_gateway->delete(); 
          $this->response->respondWithData($this->active_payment_gateway, array('as' => 'payment_gateway'));
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try 
      } //if
  	} //delete
  	
  	/**
  	 * Set payment gateway as default payment gateway
  	 * 
  	 */
  	function set_as_default() {
  	  if ($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        if($this->active_payment_gateway->isNew()) {
          $this->response->notFound();
        } //if
        
        try {
          DB::beginWork('Change default payment gateway @ ' . __CLASS__);

          $this->unsetDefault();
          $this->active_payment_gateway->setIsDefault(true);          
          $this->active_payment_gateway->save();
          
          DB::commit('Payment gateway @ ' . __CLASS__);
          $this->response->respondWithData($this->active_payment_gateway, array(
            'as' => 'payment_gateway', 
            'detailed' => true, 
          ));            
        } catch(Exception $e) {
          DB::rollback('Failed to change payment gateway @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
  	} //setAsDefault
  	   
  	/**
  	 * Set isDefault value to 0
  	 * 
  	 * @return boolean
  	 */
  	protected function unsetDefault() {
  	  if($this->default_payment_gateway instanceof PaymentGateway) {
  	    $this->default_payment_gateway->setIsDefault(false);
  	    $this->default_payment_gateway->save();
  	  } 
  	} //unsetDefault
  	
  	
  	/**
  	 * Enable payment gateway
  	 * 
  	 */
  	function enable() {
  	  if ($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        if($this->active_payment_gateway->isNew()) {
          $this->response->notFound();
        } //if
        
        try {
          DB::beginWork('Enable payment gateway @ ' . __CLASS__);

          //if this method exists, please check is all necessery extension loaded
          if(method_exists($this->active_payment_gateway, 'checkEnvironment')) {
            $this->active_payment_gateway->checkEnvironment();
          }//if

          $this->active_payment_gateway->setIsEnabled(true);          
          $this->active_payment_gateway->save();
          
          DB::commit('Payment gateway enabled @ ' . __CLASS__);
          $this->response->respondWithData($this->active_payment_gateway, array(
            'as' => 'payment_gateway', 
            'detailed' => true, 
          ));            
        } catch(Exception $e) {
          DB::rollback('Failed to change payment gateway @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
  	} //enable
  	
  	
  	/**
  	 * Disable payment gateway
  	 * 
  	 */
  	function disable() {
  	  if ($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        if($this->active_payment_gateway->isNew()) {
          $this->response->notFound();
        } //if
        
        try {
          DB::beginWork('Disable payment gateway @ ' . __CLASS__);

          $this->active_payment_gateway->setIsEnabled(false);          
          $this->active_payment_gateway->save();
          
          DB::commit('Payment gateway enabled @ ' . __CLASS__);
          $this->response->respondWithData($this->active_payment_gateway, array(
            'as' => 'payment_gateway', 
            'detailed' => true, 
          ));            
        } catch(Exception $e) {
          DB::rollback('Failed to change payment gateway @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
  	} //disable

  	/**
  	 * Change payments settings
  	 * 
  	 */
  	public function settings() {
  	  if ($this->request->isAsyncCall()) {
  	  
        if ($this->request->isSubmitted()) {
          try {
            $payments_config = $this->request->post('payments_config');
            ConfigOptions::setValue('allow_payments',$payments_config['allow_payments']);
            ConfigOptions::setValue('allow_payments_for_invoice',$payments_config['allow_payments_for_invoice']);
            
            if($payments_config['enforce']) {
              $invoices = Invoices::find();
              if(is_foreachable($invoices)) {
                foreach($invoices as $invoice) {
                  if($invoice instanceof Invoice) {
                    $invoice->setAllowPayments($payments_config['allow_payments_for_invoice']);
                    $invoice->save();
                  }//if
                }//foreach
              }//if
            }//if
            
            AngieApplication::useHelper('display_payments_type','payments');
            $payments_config['payment_settings_global'] = smarty_function_display_payments_type(array('value' => $payments_config['allow_payments']));
            $payments_config['invoice_payment'] = smarty_function_display_payments_type(array('value' => $payments_config['allow_payments_for_invoice']));
            
            
            $this->response->respondWithData($payments_config, array('as' => 'settings'));
          } catch (Error $e) {
            $this->response->exception($e);
          }//if
        }//if
  	  } else {
        $this->response->badRequest();
      }//if
  	}//change_settings

  	/*
  	 * Manage payment methods
  	 *
  	 */
  	public function methods() {
  	  if ($this->request->isAsyncCall()) {
  	    
  	    $payment_methods = array();
        EventsManager::trigger('on_payment_methods', array(&$payment_methods));
        
        $this->response->assign(array(
          'payment_methods' => $payment_methods
        ));
        
        if ($this->request->isSubmitted()) {
          try {
            
            if(is_foreachable($payment_methods)) {
              foreach($payment_methods as $payment_method) {
                $value = $this->request->post($payment_method['name']);
                
                //add default "Online Payment" if doesn't exist
                if($payment_method['name'] == 'payment_methods_online') {
                  if(!in_array('Online Payment (PayPal)',$value)) {
                    $value[] = 'Online Payment (PayPal)';
                  }//if
                  if(!in_array('Online Payment (Authorize)',$value)) {
                    $value[] = 'Online Payment (Authorize)';
                  }//if
                }//if
                
                if(is_foreachable($value)) {
                  ConfigOptions::setValue($payment_method['name'], $value);
                } else {
                  ConfigOptions::setValue($payment_method['name'], array());
                } // if
              } // foreach
            } // if
            
            $this->response->ok();
          } catch (Error $e) {
            $this->response->exception($e);
          }//if
        }//if
  	  } else {
        $this->response->badRequest();
      }//if
  	}//methods
   
  }