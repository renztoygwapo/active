<?php
  // Inherit application level controller
  AngieApplication::useController('email_admin', EMAIL_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level incoming email filter administration
   *
   * @package angie.framework.email
   * @subpackage controllers
   */

  abstract class FwIncomingMailFilterAdminController extends EmailAdminController  {
    /**
     * Active filter
     * 
     * @var IncomingMailFilter
     */
    protected $active_filter;
 
    /**
     * @var $action NamedList
     */
    protected $actions;
    
    /**
     * Unavaliable actions
     * 
     * @var array
     */
    protected $unavailable_actions;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      if(AngieApplication::isOnDemand()) {
        $this->response->notFound();
      } //if

      $this->actions = new NamedList();
      $this->unavailable_actions = array();
      
      EventsManager::trigger('on_incoming_mail_actions', array(&$this->actions, $this->logged_user, &$this->unavailable_actions));
    
      $filter_id = $this->request->getId('filter_id');
      if($filter_id) {
        $this->active_filter = IncomingMailFilters::findById($filter_id);
      } // if
      
      if (!($this->active_filter instanceof IncomingMailFilter)) {
        $this->active_filter = new IncomingMailFilter();
      } // if
      
      $this->smarty->assign(array(
        'active_filter'      => $this->active_filter,
        'incoming_mail_actions' => $this->actions,
        'unavailable_actions' => $this->unavailable_actions
      ));
    } // __construct
    
    /**
     * Index filters
     */
    function index() {
      $this->wireframe->actions->add('new_incoming_email_rule', lang('New Filter'), Router::assemble('incoming_email_admin_filter_add'), array(
        'onclick' => new FlyoutFormCallback('incoming_filter_created'),
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),        
      ));
     
      $filters_per_page = 15;
    
    	if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(IncomingMailFilters::getSlice($filters_per_page, $exclude, $timestamp));
    	} else {
    	 
    		$this->smarty->assign(array(
    		  'filters' => IncomingMailFilters::getSlice($filters_per_page), 
    		  'filters_per_page' => $filters_per_page, 
    		  'total_filters' => IncomingMailFilters::count(), 
    		));
    		 
    	} // if
    	
    }//index
    
    /**
     * Add new filter
     */
    function add() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {

        $action_forms = array();
        if(is_foreachable($this->actions)) {
          foreach ($this->actions as $action) {
            if($action->getTemplateName()) {
              $action_forms[$action->getActionClassName()] = $this->smarty->fetch(get_view_path($action->getTemplateName(), $action->getTemplateFolder(), $action->getModuleName()));
            }//if
          }//foreach
        }//if
        
        $this->smarty->assign(array(
          'action_forms' => $action_forms,
          'to_url' => AngieApplication::getName() == 'activeCollab' ? Router::assemble('project_action_project_changed') : '',
        ));
        
        if($this->request->isSubmitted()) {
          $posted_data = $this->request->post('filter');
         
          try {
            DB::beginWork('Add new filter @ ' . __CLASS__);

            
            $all_mailboxes = array_var($posted_data,'all_mailboxes',null,true);
            if($all_mailboxes) {
              //if ==1 use all mailboxes
              unset($posted_data['mailbox_id']);
            }//if
            
            $this->active_filter->serializeData($posted_data);
            $this->active_filter->setAttributes($posted_data);
            $this->active_filter->setIsEnabled(true);
            $this->active_filter->save();
            
            DB::commit('New filter added @ ' . __CLASS__);
     
            $this->response->respondWithData($this->active_filter, array(
              'as' => 'incoming_filter', 
              'detailed' => true, 
            ));  
          } catch(Exception $e) {
            DB::rollback('Failed to add new filter @ ' . __CLASS__);
            $this->response->exception($e);
          } // try
        }//if
      } else {
        $this->response->badRequest();
      }//if
      
   } //add
   
    /**
     * Page for editing filter
     */
    function edit() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        if($this->active_filter->isNew()) {
          $this->response->notFound();
        } // if
            
        $this->wireframe->breadcrumbs->add('incoming_mail_filter', clean($this->active_filter->getName()), $this->active_filter->getViewUrl());
        
        $posted_data = $this->request->post('filter');
        if (!is_array($posted_data)) {
          $posted_data = array(
            'name' => $this->active_filter->getName(),
            'subject_type' => $this->active_filter->getSubjectType(),
            'subject_text' => $this->active_filter->getSubjectText(),
            'body_type' => $this->active_filter->getBodyType(),
            'body_text' => $this->active_filter->getBodyText(),
            'priority' => $this->active_filter->getPriority(),
            'attachments' => $this->active_filter->getAttachments(),
            'sender_type' => $this->active_filter->getSenderType(),
            'sender_text' => $this->active_filter->getSenderText(),
          	'to_email_type' => $this->active_filter->getToEmailType(),
            'to_email_text' => $this->active_filter->getToEmailText(),
            'mailbox_id'  => $this->active_filter->getMailboxId(),
            'action_name' => $this->active_filter->getActionName(),
            'action_parameters' => $this->active_filter->getActionParameters(),
            'is_default' => $this->active_filter->getIsDefault(),
           
            );
        } // if

        $action_forms = array();
        if(is_foreachable($this->actions)) {
          foreach ($this->actions as $action) {
            if($action->getTemplateName()) {
              $this->smarty->assign(array(
                "filter_data" => $posted_data,
              ));
              $action_forms[$action->getActionClassName()] = $this->smarty->fetch(get_view_path($action->getTemplateName(), $action->getTemplateFolder(), $action->getModuleName()));
            }//if
          }//foreach
        }//if
        
        $this->smarty->assign(array(
          "filter_data" => $posted_data,
          'action_forms' => $action_forms,
          'to_url' => AngieApplication::getName() == 'activeCollab' ? Router::assemble('project_action_project_changed') : '',
        ));
        
        if ($this->request->isSubmitted()) {
          try {
            DB::beginWork('Updating filter @ ' . __CLASS__);
           
            $all_mailboxes = array_var($posted_data,'all_mailboxes',null,true);
            if($all_mailboxes) {
              //if ==1 use all mailboxes
              unset($posted_data['mailbox_id']);
            }//if
            
            $this->active_filter->serializeData($posted_data);
            $this->active_filter->disableNotPostedData($posted_data);
            $this->active_filter->setAttributes($posted_data);
            $this->active_filter->save();
            
            DB::commit('Filter updated @ ' . __CLASS__);
            $this->response->respondWithData($this->active_filter, array(
              'as' => 'incoming_filter', 
              'detailed' => true, 
            ));  
            
          } catch(Exception $e) {
            DB::rollback('Failed to update filter @ ' . __CLASS__);
            $this->response->exception($e);
          } // try     
        } // if
        
      } else {
        $this->response->badRequest();
      }//if
    } // edit
       
    /**
     * Filter enable
     */
    function enable() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        
        if(!$this->active_filter instanceof IncomingMailFilter) {
          $this->httpError(HTTP_ERR_NOT_FOUND, lang("Can't find filter."), true, true);
        } //if
                
        try {
          DB::beginWork('Enabling filter @ ' . __CLASS__);

          $this->active_filter->setIsEnabled(IncomingMailFilter::FILTER_ENABLED);
         
          $this->active_filter->save();
          
          DB::commit('Filter enabled @ ' . __CLASS__);
          $this->response->respondWithData($this->active_filter, array(
            'as' => 'incoming_filter', 
            'detailed' => true, 
          ));              
        } catch(Exception $e) {
          DB::rollback('Failed to enable filter @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    }//enable
    
    /**
     * Filter disable
     */
    function disable() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        
        if(!$this->active_filter instanceof IncomingMailFilter) {
          $this->httpError(HTTP_ERR_NOT_FOUND, lang("Can't find filter."), true, true);
        } //if
                
        try {
          DB::beginWork('Disabling filter @ ' . __CLASS__);

          $this->active_filter->setIsEnabled(IncomingMailFilter::FILTER_DISABLED);
         
          $this->active_filter->save();
          
          DB::commit('Filter disabled @ ' . __CLASS__);
          $this->response->respondWithData($this->active_filter, array(
            'as' => 'incoming_filter', 
            'detailed' => true, 
          ));              
        } catch(Exception $e) {
          DB::rollback('Failed to disable filter @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    }//enable
    
    
    /**
     * View filter 
     * 
     */
    function view() {
      
    } //view
    
    /**
     * Reorder filter position async
     */
    function reorder_position() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $new_order = $this->request->post('new_order');
        if(!$new_order) {
          $this->response->badRequest();
        } //if
        $new_order = explode(",",$new_order);
        
        try {
          DB::beginWork('Updating filter position @ ' . __CLASS__);
          $position = 1;
          foreach ($new_order as $filter_id) {
            $filter = IncomingMailFilters::findById($filter_id);
            if(!$filter instanceof IncomingMailFilter) {
              $this->response->badRequest();
            }//if
            //if not default filter with position -1
            $filter->setPosition($position);
            $filter->save();
            $position++;
          }//foreach
          DB::commit('Filter position updated @ ' . __CLASS__);
          $this->response->ok();            
        } catch(Exception $e) {
          DB::rollback('Failed to update filter @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
      
    } //reorder_position
    
    /**
     * Delete active filter
     */
    function delete() {
      if (($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        try {
          $this->active_filter->delete();
          
          $this->response->respondWithData($this->active_filter, array(
            'as' => 'incoming_filter', 
            'detailed' => true, 
          ));  
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // delete
    
  }