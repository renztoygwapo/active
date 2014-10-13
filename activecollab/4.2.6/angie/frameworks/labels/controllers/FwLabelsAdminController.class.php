<?php

  // Built on top of administration controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Labels administration controller
   *
   * @package angie.framework.labels
   */
  class FwLabelsAdminController extends Controller {
  	
  	/**
     * Selected label
     *
     * @var Label
     */
    protected $active_label;
    
    /**
     * Class of the labels that's handled by parent controller
     *
     * @var string
     */
    protected $label_type;
    
    /**
     * Indicates whether user can create labels of this type or not
     *
     * @var boolean
     */
    protected $can_add_label = false;
    
    /**
     * Labels URL (for loading more labels)
     *
     * @var unknown_type
     */
    protected $labels_url;
    
    /**
     * Define new label URL
     *
     * @var string
     */
    protected $labels_add_url;
    
    /**
     * Display main labels administration page
     */
    function labels() {
      if($this->can_add_label) {
        $this->wireframe->actions->add('add_label', lang('New Label'), $this->labels_add_url, array(
    	    'onclick' => new FlyoutFormCallback(array(
    	    	'success_event' => $this->active_label->getCreatedEventName(),
            'width' => 400
          )),
        	'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),    	    
    	  ));
      } // if
      
    	$labels_per_page = 50;
    	
    	if($this->request->get('paged_list')) {
    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
    		
    		$this->response->respondWithData(Labels::getSliceByType($labels_per_page, $this->label_type, $exclude, $timestamp));
    	} else {
    	  $this->response->assign(array(
    	    'load_more_labels_url' => $this->labels_url, 
    		  'labels' => Labels::getSliceByType($labels_per_page, $this->label_type), 
    			'expense_categories_per_page' => $labels_per_page, 
    		  'total_labels' => Labels::countByType($this->label_type), 
    		));
    	} // if
    	
    	$this->response->assign(array(
    	    'label_type' => $this->label_type, 
    	));
    	
    } // labels
    
    /**
     * Define a new label
     */
    function add_label() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() || $this->request->isSubmitted())) {
        if($this->can_add_label) {
          $label_data = $this->request->post('label', array(
            'fg_color' => '000000', 
            'bg_color' => 'FFFFFF',
          ));
          
          $this->response->assign(array(
            'add_label_url' => $this->labels_add_url, 
            'label_data' => $label_data
          ));
          
          if($this->request->isSubmitted()) {
            try {
              $this->active_label->setAttributes($label_data);
              $this->active_label->save();
              
              $this->response->respondWithData($this->active_label, array('as' => 'label'));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add_label
    
    /**
     * Update label
     */
    function edit_label() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_label->isLoaded()) {
          if($this->active_label->canEdit($this->logged_user)) {
            $label_data = $this->request->post('label', array(
              'name' => $this->active_label->getName(), 
              'fg_color' => $this->active_label->getForegroundColor(), 
              'bg_color' => $this->active_label->getBackgroundColor(), 
            ));
          
            $this->response->assign('label_data', $label_data);
            
            if($this->request->isSubmitted()) {
              try {
                $this->active_label->setAttributes($label_data);
                $this->active_label->save();
                
                $this->response->respondWithData($this->active_label, array(
                  'as' => 'label'
                ));
              } catch(Exception $e) {
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
    } // edit_label
    
    /**
     * Set selected label as default
     */
    function set_label_as_default() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_label->isLoaded()) {
          if($this->active_label->canEdit($this->logged_user)) {
            try {
              if($this->active_label->getIsDefault()) {
                Labels::unsetDefault($this->active_label); 
              } else {
                Labels::setDefault($this->active_label);
              } // if              
              $this->response->respondWithData($this->active_label, array('as' => 'label'));
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
    } // set_label_as_default
    
    /**
     * Delete label
     */
    function delete_label() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_label->isLoaded()) {
          if($this->active_label->canDelete($this->logged_user)) {
            try {
              $this->active_label->delete();
              $this->response->respondWithData($this->active_label, array('as' => 'label'));
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
    } // delete_label
    
  }