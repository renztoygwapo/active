<?php

  /**
   * Comments framework definition
   *
   * @package angie.frameworks.comments
   */
  class CommentsFramework extends AngieFramework {
    
    /**
     * Short name of the framework
     *
     * @var string
     */
    protected $name = 'comments';
    
    /**
     * Define comment routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineCommentsRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $comment_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('comment_id' => '\d+')) : array('comment_id' => '\d+');
      
      Router::map("{$context}_comments", "$context_path/comments", array('controller' => $controller_name, 'action' => "{$context}_comments", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_comments_add", "$context_path/comments/add", array('controller' => $controller_name, 'action' => "{$context}_add_comment", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_comments_lock", "$context_path/comments/lock", array('controller' => $controller_name, 'action' => "{$context}_comments_lock", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_comments_unlock", "$context_path/comments/unlock", array('controller' => $controller_name, 'action' => "{$context}_comments_unlock", 'module' => $module_name), $context_requirements);
      
      Router::map("{$context}_comment", "$context_path/comments/:comment_id", array('controller' => $controller_name, 'action' => "{$context}_view_comment", 'module' => $module_name), array('comment_id' => '\d+'), $comment_requirements);
      Router::map("{$context}_comment_edit", "$context_path/comments/:comment_id/edit", array('controller' => $controller_name, 'action' => "{$context}_edit_comment", 'module' => $module_name), array('comment_id' => '\d+'), $comment_requirements);
      Router::map("{$context}_comment_delete", "$context_path/comments/:comment_id/delete", array('controller' => $controller_name, 'action' => "{$context}_delete_comment", 'module' => $module_name), array('comment_id' => '\d+'), $comment_requirements);
      
      AngieApplication::getModule('attachments')->defineAttachmentsRoutesFor("{$context}_comment", "$context_path/comments/:comment_id", $controller_name, $module_name, $comment_requirements);
      AngieApplication::getModule('environment')->defineStateRoutesFor("{$context}_comment", "$context_path/comments/:comment_id", $controller_name, $module_name, $comment_requirements);
    } // defineCommentsRoutesFor
    
    /**
     * Define handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_activity_log_callbacks', 'on_activity_log_callbacks');
      EventsManager::listen('on_incoming_mail_interceptors', 'on_incoming_mail_interceptors');
      EventsManager::listen('on_incoming_mail_actions', 'on_incoming_mail_actions');
    } // defineHandlers
  
  }