<?php

  /**
   * Recent activities home screen widget implementation
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage models
   */
  class RecentActivitiesHomescreenWidget extends HomescreenWidget {
    
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('Recent Activities');
    } // getName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return lang('List recent activities on projects that active user is involved with. This widget is permission sensitive, so it will display only the data that the user can see');
    } // getDescription
    
    /**
     * Return widget body
     * 
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     */
    function renderBody(IUser $user, $widget_id, $column_wrapper_class = null) {
      AngieApplication::useHelper('activity_log', ACTIVITY_LOGS_FRAMEWORK);
      
      return smarty_function_activity_log(array(
        'activity_logs' => ActivityLogs::findRecent($user), 
        'user' => $user, 
      ), SmartyForAngie::getInstance());
    } // renderBody
    
  }