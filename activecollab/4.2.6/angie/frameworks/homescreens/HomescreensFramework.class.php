<?php

  /**
   * Home screens framework definition
   * 
   * @package angie.frameworks.homescreens
   */
  class HomescreensFramework extends AngieFramework {
  
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'homescreens';
    
    /**
     * Define framework routes
     */
    function defineRoutes() {
      Router::map('homescreen_widget_render', 'homescreen/widgets/:widget_id/render', array('controller' => 'homescreen_widgets', 'module' => HOMESCREENS_FRAMEWORK_INJECT_INTO, 'action' => 'render'), array('widget_id' => Router::MATCH_ID));
    } // defineRoutes
    
    /**
     * Define home screens routes for given context
     * 
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineHomescreenRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $tab_requirements = $context_requirements ? array_merge($context_requirements, array('homescreen_tab_id' => '\d+')) : array('homescreen_tab_id' => '\d+');
      $widget_requirements = array_merge($tab_requirements, array('homescreen_widget_id' => '\d+'));
      
      Router::map("{$context}_homescreen", "$context_path/homescreen", array('controller' => $controller_name, 'action' => "{$context}_homescreen", 'module' => $module_name), $context_requirements);

      Router::map("{$context}_homescreen_tabs_add", "$context_path/homescreen/tabs/add", array('controller' => $controller_name, 'action' => "{$context}_homescreen_tabs_add", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_homescreen_tabs_reorder", "$context_path/homescreen/tabs/reorder", array('controller' => $controller_name, 'action' => "{$context}_homescreen_tabs_reorder", 'module' => $module_name), $context_requirements);

      Router::map("{$context}_homescreen_tab", "$context_path/homescreen/tabs/:homescreen_tab_id", array('controller' => $controller_name, 'action' => "{$context}_homescreen_tab", 'module' => $module_name), $tab_requirements);
      Router::map("{$context}_homescreen_tab_edit", "$context_path/homescreen/tabs/:homescreen_tab_id/edit", array('controller' => $controller_name, 'action' => "{$context}_homescreen_tab_edit", 'module' => $module_name), $tab_requirements);
      Router::map("{$context}_homescreen_tab_delete", "$context_path/homescreen/tabs/:homescreen_tab_id/delete", array('controller' => $controller_name, 'action' => "{$context}_homescreen_tab_delete", 'module' => $module_name), $tab_requirements);

      Router::map("{$context}_homescreen_tab_widgets_add", "$context_path/homescreen/tabs/:homescreen_tab_id/widgets/add", array('controller' => $controller_name, 'action' => "{$context}_homescreen_widgets_add", 'module' => $module_name), $tab_requirements);
      Router::map("{$context}_homescreen_tab_widgets_reorder", "$context_path/homescreen/tabs/:homescreen_tab_id/widgets/reorder", array('controller' => $controller_name, 'action' => "{$context}_homescreen_widgets_reorder", 'module' => $module_name), $tab_requirements);

      Router::map("{$context}_homescreen_tab_widget", "$context_path/homescreen/tabs/:homescreen_tab_id/widgets/:homescreen_widget_id", array('controller' => $controller_name, 'action' => "{$context}_homescreen_widget", 'module' => $module_name), $widget_requirements);
      Router::map("{$context}_homescreen_tab_widget_edit", "$context_path/homescreen/tabs/:homescreen_tab_id/widgets/:homescreen_widget_id/edit", array('controller' => $controller_name, 'action' => "{$context}_homescreen_widget_edit", 'module' => $module_name), $widget_requirements);
      Router::map("{$context}_homescreen_tab_widget_delete", "$context_path/homescreen/tabs/:homescreen_tab_id/widgets/:homescreen_widget_id/delete", array('controller' => $controller_name, 'action' => "{$context}_homescreen_widget_delete", 'module' => $module_name), $widget_requirements);
    } // defineHomescreenRoutesFor
    
  }