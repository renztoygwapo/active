<?php

  /**
   * Widget container home screen tab
   * 
   * @package angie.frameworks.homescreens
   * @subpackage models
   */
  abstract class WidgetsHomescreenTab extends HomescreenTab {
    
    /**
     * Return description
     * 
     * @return string
     */
    function getDescription() {
      switch($this->countWidgets()) {
        case 0:
          return lang('Home screen tab with no widgets');
        case 1:
          return lang('Home screen tab with one widget');
        default:
          return lang('Home screen tab with :num widgets', array('num' => $this->countWidgets()));
      } // switch
    } // getDescription
    
    /**
     * Column definitions (none)
     *
     * @var array
     */
    protected $columns;
    
    /**
     * Return tab columns
     * 
     * @return array
     */
    function getColumns() {
      return $this->columns;
    } // getColumns
    
    /**
     * Return number of columns that this tab has
     * 
     * @return integer
     */
    function countColumns() {
      return is_array($this->columns) ? count($this->columns) : 0;
    } // countColumns
    
    /**
     * Cached tab widgets
     *
     * @var DBResult
     */
    private $widgets = false;
    
    /**
     * Return all widgets that belong to this tab
     *
     * @return HomescreenWidget[]
     */
    function getWidgets() {
      if($this->widgets === false) {
        $this->widgets = HomescreenWidgets::findByHomescreenTab($this);
      } // if
      
      return $this->widgets;
    } // getWidgets
    
    /**
     * Cached number of widgets
     *
     * @var integer
     */
    private $widgets_count = false;
    
    /**
     * Return number of widgets in this tab
     * 
     * @return integer
     */
    function countWidgets() {
      if($this->widgets_count === false) {
        if($this->widgets === false) {
          $this->widgets_count = HomescreenWidgets::countByHomescreenTab($this);
        } else {
          $this->widgets_count = $this->widgets === null ? 0 : count($this->widgets);
        } // if
      } // if
      
      return $this->widgets_count;
    } // countWidgets
    
    /**
     * Returns true if there are widgets in given column
     * 
     * @param integer $column_id
     * @return boolean
     */
    function columnHasWidgets($column_id) {
      if($this->getWidgets()) {
        foreach($this->getWidgets() as $widget) {
          if($widget->getColumnId() == $column_id) {
            return true;
          } // if
        } // foreach
      } // if
      
      return false;
    } // columnHasWidgets
    
    /**
     * Return widgets that belong to a given column
     * 
     * @param integer $column_id
     * @return array
     */
    function getColumnWidgets($column_id) {
      if($this->getWidgets()) {
        $result = array();
        
        foreach($this->getWidgets() as $widget) {
          if($widget->getColumnId() == $column_id) {
            $result[] = $widget;
          } // if
        } // foreach
        
        return count($result) ? $result : null;
      } else {
        return null;
      } // if 
    } // getColumnWidgets
    
    // ---------------------------------------------------
    //  Render
    // ---------------------------------------------------
    
    /**
     * Render tab content
     * 
     * @param IUser $user
     * @return string
     */
    function render(IUser $user) {
      $classes = array('homescreen');
      
      if($this->accept_widgets) {
        $classes[] = 'accept_widgets';
      } else {
        $classes[] = 'no_widgets';
      } // if
      
      if($this->columns) {
        switch(count($this->columns)) {
          case 1:
            $classes[] = 'one_column';
            break;
          case 2:
            $classes[] = 'two_columns';
            break;
          case 3:
            $classes[] = 'three_columns';
            break;
          default:
            $classes[] = 'no_columns';
        } // switch
      } else {
        $classes[] = 'no_columns';
      } // if
      
      $content = '<div class="' . implode(' ', $classes) . '">';
      
      if($this->accept_widgets && $this->columns) {
        foreach($this->columns as $column_id => $column_wrapper_class) {
          $has_widgets = $this->columnHasWidgets($column_id);
          
          $content .= '<div class="homescreen_tab_column_wrapper ' . $column_wrapper_class . '"><div class="homescreen_tab_column homescreen_tab_column_' . $column_id . ' ' . ($has_widgets ? 'has_widgets' : 'no_widgets') . '">';
          
          if($has_widgets) {
            foreach($this->getColumnWidgets($column_id) as $widget) {
              $widget_id = HTML::uniqueId('homescreen_widget');
              $content .= '<div class="homescreen_widget" id="' . $widget_id . '">';
              $content .= '<h3 class="head"><span class="head_inner">' . $widget->renderTitle($user, $widget_id, $column_wrapper_class) . '</span></h3>';
              $content .= '<div class="body"><div class="body_inner">' . $widget->renderBody($user, $widget_id, $column_wrapper_class) . '</div></div>';
              $content .= '</div>' . $widget->renderFooter($user, $widget_id, $column_wrapper_class);
            } // foreach
          } else {
            $content .= '<p>' . lang('Empty') . '</p>';
          } // if
          
          $content .= '</div></div>';
        } // foreach
      } // if
      
      return "$content</div>";
    } // render
    
    /**
     * Render management widget
     * 
     * @param IUser $user
     * @return string
     */
    function renderManager(IUser $user) {
      $widget_id = HTML::uniqueId('manage_homescreen_widgets');
      
      $options = array(
        'reoder_widgets_url' => $this->getReoderWidgetsUrl(), 
        'columns' => array(),
      );
      
      foreach($this->columns as $column_id => $column_wrapper_class) {
        $options['columns'][$column_id] = array(
          'wrapper_class' => $column_wrapper_class, 
          'add_widget_url' => $this->getAddWidgetUrl($column_id), 
          'widgets' => $this->getColumnWidgets($column_id), 
        );
      } // foreach
      
      $classes = array('manage_homescreen_widgets');
      if($this->columns) {
        switch(count($this->columns)) {
          case 1:
            $classes[] = 'one_column';
            break;
          case 2:
            $classes[] = 'two_columns';
            break;
          case 3:
            $classes[] = 'three_columns';
            break;
          default:
            $classes[] = 'no_columns';
        } // switch
      } else {
        $classes[] = 'no_columns';
      } // if

      AngieApplication::useWidget('manage_homescreen_widgets', HOMESCREENS_FRAMEWORK);
      
      return HTML::openTag('div', array(
        'class' => implode(' ', $classes), 
        'id' => $widget_id, 
      )) . '</div><script type="text/javascript">$("#' . $widget_id . '").manageHomescreenWidgets(' . JSON::encode($options) . ')</script>';
    } // renderManager
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Cached add widget URL pattern
     *
     * @var string
     */
    private $add_widget_url = false;
    
    /**
     * Return add widget URL
     * 
     * @param integer $column_id
     * @return string
     */
    function getAddWidgetUrl($column_id) {
      if($this->add_widget_url === false) {
        $params = $this->getRoutingContextParams();
        if($params) {
          $params['column_id'] = '--COLUMN-ID--';
        } else {
          $params = array('column_id' => '--COLUMN-ID--');
        } // if
        
        $this->add_widget_url = Router::assemble($this->getRoutingContext() . '_widgets_add', $params);
      } // if
      
      return str_replace('--COLUMN-ID--', $column_id, $this->add_widget_url);
    } // getAddWidgetUrl
    
    /**
     * Return reorder widgets URL
     * 
     * @return string
     */
    function getReoderWidgetsUrl() {
      return Router::assemble($this->getRoutingContext() . '_widgets_reorder', $this->getRoutingContextParams());
    } // getReoderWidgetsUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Copy this tab and all of its settings and widgets to a home screen
     * 
     * @param User $user
     * @return HomescreenTab
     * @throws Exception
     */
    function copyTo(User $user) {
      try {
        DB::beginWork('Copying home screen tab to a home screen @ ' . __CLASS__);
        
        $homescreen_tab = parent::copyTo($user);
        
        if($homescreen_tab instanceof HomescreenTab && $this->getWidgets()) {
          foreach($this->getWidgets() as $widget) {
            $widget->copyTo($homescreen_tab);
          } // foreach
        } // if
        
        DB::commit('Home screen tab copied to a home screen @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to copy home screen tab to a home screen @ ' . __CLASS__);
        throw $e;
      } // try
      
      return $homescreen_tab;
    } // copyTo
    
    /**
     * Remove this home screen tab
     */
    function delete() {
      try {
        DB::beginWork('Removing home screen tab @ ' . __CLASS__);

        HomescreenWidgets::deleteByHomescreenTab($this);
        parent::delete();
        
        DB::commit('Home screen tab removed @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove home screen tab @ ' . __CLASS__);
        throw $e;
      } // try
    } // delete
  
  }