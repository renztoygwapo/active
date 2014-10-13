<?php

  /**
   * Comments framework definition
   *
   * @package angie.frameworks.comments
   */
  
  const COMMENTS_FRAMEWORK = 'comments';
  const COMMENTS_FRAMEWORK_PATH = __DIR__;

  defined('COMMENTS_FRAMEWORK_INJECT_INTO') or define('COMMENTS_FRAMEWORK_INJECT_INTO', 'system');
  
  AngieApplication::setForAutoload(array(
    'FwComment' => COMMENTS_FRAMEWORK_PATH . '/models/comments/FwComment.class.php', 
    'FwComments' => COMMENTS_FRAMEWORK_PATH . '/models/comments/FwComments.class.php', 
    'IComments' => COMMENTS_FRAMEWORK_PATH . '/models/IComments.class.php', 
    'ICommentsImplementation' => COMMENTS_FRAMEWORK_PATH . '/models/ICommentsImplementation.class.php',

    'FwNewCommentNotification' => COMMENTS_FRAMEWORK_PATH . '/notifications/FwNewCommentNotification.class.php',
    
    'ICommentActivityLogsImplementation' => COMMENTS_FRAMEWORK_PATH . '/models/ICommentActivityLogsImplementation.class.php',
  
    'CommentCreatedActivityLogCallback' => COMMENTS_FRAMEWORK_PATH . '/models/javascript_callbacks/CommentCreatedActivityLogCallback.class.php',

    //interceptors
    'ReplyToCommentInterceptor' => COMMENTS_FRAMEWORK_PATH . '/models/incoming_mail_interceptors/ReplyToCommentInterceptor.class.php',
    'IncomingMailCommentAction' => COMMENTS_FRAMEWORK_PATH . '/models/IncomingMailCommentAction.class.php',

  ));

  DataObjectPool::registerTypeLoader('Comment', function($ids) {
    return Comments::findByIds($ids);
  });