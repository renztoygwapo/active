<?php

  /**
   * Framework level API client subscriptions controller
   * 
   * @package angie.frameworks.authentication
   * @subpackage controllers
   */
  abstract class FwApiClientSubscriptionsController extends Controller {
    
    /**
     * Selected API client subscription
     *
     * @var ApiClientSubscription
     */
    protected $active_api_client_subscription;
    
    /**
     * Execute before any of the actions is executed
     */
    function __before() {
      parent::__before();
      
      if($this->active_object instanceof User && $this->active_object->isLoaded()) {
        $this->wireframe->breadcrumbs->add('api_subscriptions', lang('API Subscriptions'), $this->active_object->getApiSubscriptionsUrl());
      } else {
        $this->response->notFound();
      } // if

      $api_client_subscription_id = $this->request->getId('api_client_subscription_id');
      if($api_client_subscription_id) {
        $this->active_api_client_subscription = ApiClientSubscriptions::findById($api_client_subscription_id);
      } // if
      
      if($this->active_api_client_subscription instanceof ApiClientSubscription) {
        $this->wireframe->breadcrumbs->add('api_subscription', $this->active_api_client_subscription->getName(), $this->active_api_client_subscription->getVIewUrl());
      } else {
        $this->active_api_client_subscription = new ApiClientSubscription();
        $this->active_api_client_subscription->setUser($this->active_object);
      } // if
      
      $this->smarty->assign(array(
        'active_object' => $this->active_object, 
        'active_api_client_subscription' => $this->active_api_client_subscription, 
      ));
    } // __before
  
    /**
     * List API client subscriptions for the given user
     */
    function api_client_subscriptions() {
      $subscriptions_per_page = 50;

      if($this->active_object instanceof User) {
        //if someone else tring to look at account owner api subscription
        if(!$this->active_object->canSeeApiSubscription($this->logged_user)) {
          $this->response->forbidden();
        }//if
      } //if

      if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(ApiClientSubscriptions::getSliceByUser($this->active_object, $subscriptions_per_page, $exclude, $timestamp), array(
    		  'as' => 'api_client_subscriptions', 
    		));
    	} else {
    	  if($this->active_object->canAddApiSubscription($this->logged_user)) {
          $this->wireframe->actions->add('add_api_subscription', lang('New Subscription'), $this->active_object->getAddApiSubscriptionUrl(), array(
            'onclick' => new FlyoutFormCallback('api_client_subscription_created', array(
              'width' => 300
            )),
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),            
          ));
        } // if
    	  
    		$this->smarty->assign(array(
          'subscriptions' => ApiClientSubscriptions::getSliceByUser($this->active_object, $subscriptions_per_page),
          'subscriptions_per_page' => $subscriptions_per_page, 
          'total_subscriptions' => ApiClientSubscriptions::countByUser($this->active_object), 
        ));
        
        $this->setView('index');
    	} // if
    } // api_client_subscriptions
    
    /**
     * Display API subscription details
     */
    function view_api_client_subscription() {
      if($this->request->isAsyncCall()) {
        if($this->active_api_client_subscription->isLoaded()) {
          $this->setView('view');
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // view_api_client_subscription
    
    /**
     * Create a new API subscription
     */
    function add_api_client_subscription() {
      if($this->request->isAsyncCall()) {
        if($this->active_object->canAddApiSubscription($this->logged_user)) {
          $api_client_subscription_data = $this->request->post('api_client_subscription');
          $this->smarty->assign('api_client_subscription_data', $api_client_subscription_data);
          
          if($this->request->isSubmitted(true, $this->response)) {
            try {
              DB::beginWork('Creating new API client subscription @ ' . __CLASS__);

              $this->active_api_client_subscription->setAttributes($api_client_subscription_data);
              $this->active_api_client_subscription->setToken(ApiClientSubscriptions::generateToken());
              $this->active_api_client_subscription->setIsEnabled(true);
              
              $this->active_api_client_subscription->save();
              
              DB::commit('New API client subscription created @ ' . __CLASS__);
              
              $this->response->respondWithData($this->active_api_client_subscription, array(
                'as' => 'api_client_subscription', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              DB::rollback('Failed to create new API client subscription @ ' . __CLASS__);
              
              AngieApplication::revertCsfrProtectionCode();
              
              $this->response->exception($e);
            } // try
            
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
      
      $this->setView('add');
    } // add_api_client_subscription
    
    /**
     * Update an existing API subscription
     */
    function edit_api_client_subscription() {
      if($this->request->isAsyncCall()) {
        if($this->active_api_client_subscription->isLoaded()) {
          if($this->active_api_client_subscription->canEdit($this->logged_user)) {
            $api_client_subscription_data = $this->request->post('api_client_subscription', array(
              'client_name' => $this->active_api_client_subscription->getClientName(),
              'client_vendor' => $this->active_api_client_subscription->getClientVendor(), 
              'is_read_only' => $this->active_api_client_subscription->getIsReadOnly(), 
            ));
            $this->smarty->assign('api_client_subscription_data', $api_client_subscription_data);
            
            if($this->request->isSubmitted(true, $this->response)) {
              try {
                DB::beginWork('Updating API client subscription @ ' . __CLASS__);
                
                $this->active_api_client_subscription->setAttributes($api_client_subscription_data);
                $this->active_api_client_subscription->save();
                
                DB::commit('API client subscription updated @ ' . __CLASS__);
                
                $this->response->respondWithData($this->active_api_client_subscription, array(
                  'as' => 'api_client_subscription', 
                  'detailed' => true, 
                ));
              } catch(Exception $e) {
                DB::rollback('Failed to update API client subscription @ ' . __CLASS__);
                
                AngieApplication::revertCsfrProtectionCode();
                
                $this->response->exception($e);
              } // try
              
            } // if
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
      
      $this->setView('edit');
    } // edit_api_client_subscription
    
    /**
     * Enable an existing API subscription
     */
    function enable_api_client_subscription() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if($this->active_api_client_subscription->isLoaded()) {
          if($this->active_api_client_subscription->canChangeStatus($this->logged_user)) {
            try {
              $this->active_api_client_subscription->enable($this->logged_user);
              $this->response->respondWithData($this->active_api_client_subscription, array(
                'as' => 'api_client_subscription', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // enable_api_client_subscription
    
    /**
     * Disable an existing API subscription
     */
    function disable_api_client_subscription() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if($this->active_api_client_subscription->isLoaded()) {
          if($this->active_api_client_subscription->canChangeStatus($this->logged_user)) {
            try {
              $this->active_api_client_subscription->disable($this->logged_user);
              $this->response->respondWithData($this->active_api_client_subscription, array(
                'as' => 'api_client_subscription', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // disable_api_client_subscriptions
    
    /**
     * Remove an existing API subscription
     */
    function delete_api_client_subscription() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if($this->active_api_client_subscription->isLoaded()) {
          if($this->active_api_client_subscription->canDelete($this->logged_user)) {
            try {
              $this->active_api_client_subscription->delete();
              $this->response->respondWithData($this->active_api_client_subscription, array(
                'as' => 'api_client_subscription', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // delete_api_client_subscription
    
  }