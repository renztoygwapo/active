<?php

  /**
   * Sharring settings controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class SharingSettingsController extends Controller {
    
    /**
     * Parent object
     *
     * @var ISharing
     */
    protected $active_object;
  
    /**
     * Execute before any controller action
     */
    function __before() {
      parent::__before();
      
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_object instanceof ISharing && $this->active_object->isLoaded()) {
          if($this->active_object->sharing()->canChangeSettings($this->logged_user)) {
            $this->smarty->assign('active_object', $this->active_object);
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // __before
    
    /**
     * Show and process sharing
     */
    function sharing_settings() {
      $sharing_data = $this->request->post('sharing');
      if(!is_array($sharing_data)) {
        
        // Already shared? Propose change
        if($this->active_object->sharing()->getSharingProfile() instanceof SharedObjectProfile) {
          
          $sharing_data = array(
            'enabled' => true, 
            'code' => $this->active_object->sharing()->getSharingProfile()->getSharingCode(), 
            'url' => $this->active_object->sharing()->getUrl(), 
            'expires_on' => $this->active_object->sharing()->getSharingProfile()->getExpiresOn(),  
          );
          
          if($this->active_object->sharing()->supportsComments()) {
            $comments_enabled = $this->active_object->sharing()->getSharingProfile()->getAdditionalProperty('comments_enabled');
            $sharing_data['comments_enabled'] = $comments_enabled;
            $sharing_data['attachments_enabled'] = $comments_enabled && $this->active_object->sharing()->getSharingProfile()->getAdditionalProperty('attachments_enabled');

            if($this->active_object->sharing()->supportsReopenIfCompleted()) {
              $sharing_data['comment_reopens'] = $comments_enabled && $this->active_object->sharing()->getSharingProfile()->getAdditionalProperty('comment_reopens');
            } // if
          } // if
          
        // Not shared at the moment? Prepare defaults
        } else {
          $code = $this->active_object->sharing()->generateSharingCode();
          
          $sharing_data = array(
            'enabled' => false, 
            'code' => $code, 
            'url' => $this->active_object->sharing()->getUrlProposal($code), 
            'expires_on' => null, 
          );
          
          if($this->active_object instanceof IComments) {
            $sharing_data['comments_enabled'] = true;
            $sharing_data['attachments_enabled'] = false;
            
            if($this->active_object instanceof IComplete) {
              $sharing_data['comment_reopens'] = false;
            } // if
          } // if
        } // if
      } // if
      
      $this->smarty->assign('sharing_data', $sharing_data);
      
      if($this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating sharing settings @ ' . __CLASS__);
          
          // Share or update sharing settings
          if(isset($sharing_data['enabled']) && $sharing_data['enabled']) {
            $this->active_object->sharing()->share($this->logged_user, $sharing_data);

            // invitees
            $invitees = array_var($sharing_data, 'invitees', null);
            if ($invitees) {
              $this->active_object->sharing()->invite(csv_to_array($invitees), $this->logged_user);
            } // if
            
          // Unshare
          } else {
            $unsubscribe_unregistered_users = (boolean) array_var($sharing_data, 'unsubscribe_unregistered', false);

            $this->active_object->sharing()->unshare($this->logged_user, $unsubscribe_unregistered_users);
          } // if
          
          DB::commit('Share settings updated @ ' . __CLASS__);

          $this->response->respondWithData($this->active_object, array(
          	'as' => $this->active_object->getBaseTypeName(),
          	'detailed' => true
          ));
        } catch(Exception $e) {
          DB::rollback('Failed to update sharing settings @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } // if
    } // sharing_settings
    
  }