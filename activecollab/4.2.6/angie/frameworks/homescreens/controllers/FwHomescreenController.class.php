<?php

  /**
   * Framework level home screen controller implementation
   * 
   * @package angie.frameworks.homescreens
   * @subpackage controllers
   */
  class FwHomescreenController extends Controller {
    
    /**
     * Active object that uses this home screen
     *
     * @var User|IHomescreen
     */
    protected $active_object;
    
    /**
     * Active home screen tab
     *
     * @var HomescreenTab
     */
    protected $active_homescreen_tab;
    
    /**
     * Active home screen widget
     *
     * @var HomescreenWidget
     */
    protected $active_homescreen_widget;
    
    /**
     * Execute before any action is executed
     */
    function __before() {
      parent::__before();

      if(!($this->active_object instanceof IHomescreen && $this->active_object->homescreen()->canHaveOwn())) {
        $this->response->notFound();
      } // if

      $homescreen_tab_id = $this->request->get('homescreen_tab_id');
      if($homescreen_tab_id && $homescreen_tab_id != 'dashboard') {
        $this->active_homescreen_tab = HomescreenTabs::findById($homescreen_tab_id);
      } // if

      if($this->active_homescreen_tab instanceof HomescreenTab) {
        $homescreen_widget_id = $this->request->getId('homescreen_widget_id');
        if($homescreen_widget_id) {
          $this->active_homescreen_widget = HomescreenWidgets::findById($homescreen_widget_id);
        } // if

        if($this->active_homescreen_widget instanceof HomescreenWidget && $this->active_homescreen_widget->getHomescreenTabId() != $this->active_homescreen_tab->getId()) {
          $this->response->badRequest();
        } // if
      } // if
      
      $this->response->assign(array(
        'active_object' => $this->active_object,
        'active_homescreen_tab' => $this->active_homescreen_tab, 
        'active_homescreen_widget' => $this->active_homescreen_widget, 
      ));
    } // __before
  
    /**
     * Show home screen management page
     */
    function homescreen() {
      $this->wireframe->actions->add('add_homescreen_tab', lang('New Custom Tab'), $this->active_object->homescreen()->getAddTabUrl(), array(
        'onclick' => new FlyoutFormCallback(array(
          'width' => 305,
          'success_event' => 'homescreen_tab_added',
        ))
      ));
    } // homescreen
    
    /**
     * Create a home screen tab
     */
    function homescreen_tabs_add() {
      if($this->request->isApiCall() || $this->request->isAsyncCall()) {
        $homescreen_tab_data = $this->request->post('homescreen_tab');

        $this->response->assign('homescreen_tab_data', $homescreen_tab_data);

        if($this->request->isSubmitted()) {
          try {
            DB::beginWork('Creating home screen tab @ ' . __CLASS__);

            $homescreen_tab_type = array_var($homescreen_tab_data, 'type', null, true);

            if($homescreen_tab_type && class_exists($homescreen_tab_type)) {
              $this->active_homescreen_tab = new $homescreen_tab_type();
            } // if

            if(!($this->active_homescreen_tab instanceof HomescreenTab)) {
              $this->active_homescreen_tab = new SplitHomescreenTab();
            } // if

            $this->active_homescreen_tab->setAttributes($homescreen_tab_data);
            $this->active_homescreen_tab->setUserId($this->active_object->getId());
            $this->active_homescreen_tab->setPosition(HomescreenTabs::getNextPosition($this->active_object));
            $this->active_homescreen_tab->save();

            DB::commit('Home screen tab created @ ' . __CLASS__);

            $this->response->respondWithData($this->active_homescreen_tab, array(
              'as' => 'homescreen_tab',
              'detailed' => true,
            ));
          } catch(Exception $e) {
            DB::rollback('Failed to create home screen tab @ ' . __CLASS__);
            $this->response->exception($e);
          } // try
        } // if

        $this->setView('add_homescreen_tab');
      } else {
        $this->response->badRequest();
      } // if
    } // homescreen_tabs_add
    
    /**
     * Reorder home screen tabs
     */
    function homescreen_tabs_reorder() {
      if(($this->request->isApiCall() || $this->request->isAsyncCall()) && $this->request->isSubmitted()) {
        $homescreen_tabs = $this->request->post('homescreen_tabs');
        
        if(is_foreachable($homescreen_tabs)) {
          foreach($homescreen_tabs as $homescreen_tab_id => $position) {
            $homescreen_tab = $homescreen_tab_id ? HomescreenTabs::findById($homescreen_tab_id) : null;
            
            if($homescreen_tab instanceof HomescreenTab) {
              $homescreen_tab->setPosition($position);
              $homescreen_tab->save();
            } // if
          } // foreach
        } // if
        
        $this->response->ok();
      } else {
        $this->response->badRequest();
      } // if
    } // homescreen_tabs_reorder

    /**
     * Set a selected home screen tab as default
     */
    function homescreen_tabs_set_default() {
      if(($this->request->isApiCall() || $this->request->isAsyncCall()) && $this->request->isSubmitted()) {
        try {
          if($this->active_homescreen_tab instanceof HomescreenTab && $this->active_homescreen_tab->isLoaded()) {
            ConfigOptions::setValueFor('default_homescreen_tab_id', $this->active_object, $this->active_homescreen_tab->getId());
          } else {
            ConfigOptions::removeValuesFor($this->active_object, 'default_homescreen_tab_id');
          } // if

          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // homescreen_tabs_set_default
    
    /**
     * Show homescreen tab details
     */
    function homescreen_tab() {
      if($this->request->isApiCall()) {
        if($this->active_homescreen_tab instanceof HomescreenTab && $this->active_homescreen_tab->isLoaded()) {
          $this->response->respondWithData($this->active_homescreen_tab, array(
            'as' => 'homescreen_tab', 
            'detailed' => true, 
          ));
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // homescreen_tab
    
    /**
     * Update home screen tab
     */
    function homescreen_tab_edit() {
      if($this->request->isApiCall() || $this->request->isAsyncCall()) {
        if($this->active_homescreen_tab instanceof HomescreenTab && $this->active_homescreen_tab->isLoaded()) {
          if(!$this->active_homescreen_tab->canEdit($this->logged_user)) {
            $this->response->forbidden();
          } // if
          
          $homescreen_tab_data = $this->request->post('homescreen_tab', array(
            'name' => $this->active_homescreen_tab->getName(), 
          ));
          
          $this->response->assign('homescreen_tab_data', $homescreen_tab_data);
            
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Updating home screen tab @ ' . __CLASS__);
              
              $this->active_homescreen_tab->setAttributes($homescreen_tab_data);
              $this->active_homescreen_tab->save();
              
              DB::commit('Home screen tab updated @ ' . __CLASS__);
              
              $this->response->respondWithData($this->active_homescreen_tab, array(
                'as' => 'homescreen_tab', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              DB::rollback('Failed to updated home screen tab @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
          } // if
          
          $this->setView('edit_homescreen_tab');
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // homescreen_tab_edit
    
    /**
     * Remove home screen tab
     */
    function homescreen_tab_delete() {
      if(($this->request->isApiCall() || $this->request->isAsyncCall()) && $this->request->isSubmitted()) {
        if($this->active_homescreen_tab instanceof HomescreenTab && $this->active_homescreen_tab->isLoaded()) {
          if($this->active_homescreen_tab->canDelete($this->logged_user)) {
            try {
              $this->active_homescreen_tab->delete();
              $this->response->respondWithData($this->active_homescreen_tab, array(
                'as' => 'homescreen_tab', 
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
    } // homescreen_tab_delete
    
    // ---------------------------------------------------
    //  Widgets
    // ---------------------------------------------------
    
    /**
     * Add home screen widget
     */
    function homescreen_widgets_add() {
      if($this->request->isApiCall() || $this->request->isAsyncCall()) {
        if($this->active_homescreen_tab instanceof HomescreenTab && $this->active_homescreen_tab->isLoaded()) {
          $column_id = (integer) $this->request->get('column_id');
          if(empty($column_id)) {
            $column_id = 1;
          } elseif($column_id > $this->active_homescreen_tab->countColumns()) {
            $column_id = $this->active_homescreen_tab->countColumns();
          } // if
          
          $widget_data = $this->request->post('homescreen_widget');
          
          $this->response->assign(array(
            'column_id' => $column_id, 
            'widget_data' => $widget_data, 
          ));
          
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Adding home screen widget @ ' . __CLASS__);
              
              $widget_type = array_var($widget_data, 'type', null, true);
            
              if($widget_type && class_exists($widget_type, true)) {
                $this->active_homescreen_widget = new $widget_type();
              } // if
              
              if(!($this->active_homescreen_widget instanceof HomescreenWidget)) {
                throw new ValidationErrors(array(
                  'type' => lang('Invalid home screen widget type'), 
                ));
              } // if
              
              $this->active_homescreen_widget->setAttributes($widget_data);
              $this->active_homescreen_widget->setHomescreenTabId($this->active_homescreen_tab->getId());
              $this->active_homescreen_widget->setColumnId($column_id);
              $this->active_homescreen_widget->setPosition(HomescreenWidgets::getNextPosition($this->active_homescreen_tab, $column_id));
              
              $this->active_homescreen_widget->save();
              
              DB::commit('Home screen widget added @ ' . __CLASS__);
              
              $this->response->respondWithData($this->active_homescreen_widget, array(
                'as' => 'homescreen_widget', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              DB::rollback('Failed to add home screen widget @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
          } // if
          
          $this->setView('add_homescreen_widget');
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // homescreen_widgets_add
    
    /**
     * Reorder home screen widgets
     */
    function homescreen_widgets_reorder() {
      if(($this->request->isApiCall() || $this->request->isAsyncCall()) && $this->request->isSubmitted()) {
        if($this->active_homescreen_tab instanceof HomescreenTab && $this->active_homescreen_tab->isLoaded()) {
          $widgets_order = $this->request->post('widgets_order');
          
          // Reorder widgets
          if(is_foreachable($widgets_order)) {
            try {
              DB::beginWork('Reordering home screen widgets @ ' . __CLASS__);
              
              foreach($this->active_homescreen_tab->getWidgets() as $widget) {
                $widget_id = $widget->getId();
                
                if(isset($widgets_order[$widget_id])) {
                  $column_id = (integer) array_var($widgets_order[$widget_id], 'column_id');
                  $position = (integer) array_var($widgets_order[$widget_id], 'position');
                  
                  if($column_id) {
                    $widget->setColumnId($column_id);
                  } // if
                  
                  if($position) {
                    $widget->setPosition($position);
                  } // if
                  
                  $widget->save();
                } // if
              } // foreach
              
              DB::commit('Home screen widgets reordered @ ' . __CLASS__);
              $this->response->ok();
            } catch(Exception $e) {
              DB::rollback('Failed to reorder home screen widgets @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
            
          // Nothing to reorder
          } else {
            $this->response->ok();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // homescreen_widgets_reorder
    
    /**
     * Display home screen widget details
     */
    function homescreen_widget() {
      if($this->request->isApiCall() || $this->request->isAsyncCall()) {
        if($this->active_homescreen_widget instanceof HomescreenWidget && $this->active_homescreen_widget->isLoaded()) {
          $this->response->respondWithData($this->active_homescreen_widget, array(
            'as' => 'homescreen_widget', 
            'detailed' => true, 
          ));
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // homescreen_widget

    /**
     * Renders the homescreen widget
     */
    function homescreen_widget_render() {
      if (!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if

      if(!($this->active_homescreen_widget instanceof HomescreenWidget && $this->active_homescreen_widget->isLoaded())) {
        $this->response->notFound();
      } // if

      $temp_widget_id = $this->request->get('custom_widget_id', HTML::uniqueId('homescreen_widget'));

      $this->response->respondWithData(array(
        'title'   => $this->active_homescreen_widget->renderTitle($this->logged_user, $temp_widget_id),
        'body'    => $this->active_homescreen_widget->renderBody($this->logged_user, $temp_widget_id),
        'footer'  => $this->active_homescreen_widget->renderFooter($this->logged_user, $temp_widget_id)
      ));
    } // renders the homescreen widget
    
    /**
     * Update home screen widget settings
     */
    function homescreen_widget_edit() {
      if($this->request->isApiCall() || $this->request->isAsyncCall()) {
        if($this->active_homescreen_widget instanceof HomescreenWidget && $this->active_homescreen_widget->isLoaded()) {
          $widget_data = $this->request->post('homescreen_widget');
          $this->response->assign('widget_data', $widget_data);
          
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Updating home screen widget @ ' . __CLASS__);
              
              $this->active_homescreen_widget->setAttributes($widget_data);
              $this->active_homescreen_widget->save();
              
              DB::commit('Updating widget added @ ' . __CLASS__);
              
              $this->response->respondWithData($this->active_homescreen_widget, array(
                'as' => 'homescreen_widget', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              DB::rollback('Failed to update home screen widget @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
          } // if
          
          $this->setView('edit_homescreen_widget');
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // homescreen_widget_edit
    
    /**
     * Delete home screen widget
     */
    function homescreen_widget_delete() {
      if(($this->request->isApiCall() || $this->request->isAsyncCall()) && $this->request->isSubmitted()) {
        if($this->active_homescreen_widget instanceof HomescreenWidget && $this->active_homescreen_widget->isLoaded()) {
          if($this->active_homescreen_widget->canDelete($this->logged_user)) {
            try {
              $this->active_homescreen_widget->delete();
              $this->response->respondWithData($this->active_homescreen_widget, array(
                'as' => 'homescreen_widget', 
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
    } // homescreen_widget_delete
    
  }