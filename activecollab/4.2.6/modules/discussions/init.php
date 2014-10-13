<?php

  /**
   * Init discussions module
   *
   * @package activeCollab.modules.discussions
   */
  
  define('DISCUSSIONS_MODULE', 'discussions');
  define('DISCUSSIONS_MODULE_PATH', APPLICATION_PATH . '/modules/discussions');
  
  AngieApplication::setForAutoload(array(
    'Discussion' => DISCUSSIONS_MODULE_PATH . '/models/discussions/Discussion.class.php',
    'Discussions' => DISCUSSIONS_MODULE_PATH . '/models/discussions/Discussions.class.php',
    
    'DiscussionCategory' => DISCUSSIONS_MODULE_PATH . '/models/DiscussionCategory.class.php',
    'IDiscussionCategoryImplementation' => DISCUSSIONS_MODULE_PATH . '/models/IDiscussionCategoryImplementation.class.php',
  	'IDiscussionSharingImplementation' => DISCUSSIONS_MODULE_PATH . '/models/IDiscussionSharingImplementation.class.php',

    'MyDiscussionsHomescreenWidget' => DISCUSSIONS_MODULE_PATH . '/models/homescreen_widgets/MyDiscussionsHomescreenWidget.class.php',
    
    'IDiscussionCommentsImplementation' => DISCUSSIONS_MODULE_PATH . '/models/IDiscussionCommentsImplementation.class.php',
    'DiscussionComment' => DISCUSSIONS_MODULE_PATH . '/models/DiscussionComment.class.php',
  
    'IDiscussionInspectorImplementation' => DISCUSSIONS_MODULE_PATH . '/models/IDiscussionInspectorImplementation.class.php',
  
    'DiscussionsProjectExporter' => DISCUSSIONS_MODULE_PATH . '/models/DiscussionsProjectExporter.class.php',
    'IncomingMailDiscussionAction' => DISCUSSIONS_MODULE_PATH . '/models/IncomingMailDiscussionAction.class.php',
  
    'IDiscussionSearchItemImplementation' => DISCUSSIONS_MODULE_PATH . '/models/IDiscussionSearchItemImplementation.class.php',

    'NewDiscussionNotification' => DISCUSSIONS_MODULE_PATH . '/notifications/NewDiscussionNotification.class.php',
  ));
  
  DataObjectPool::registerTypeLoader('Discussion', function($ids) {
    return Discussions::findByIds($ids, STATE_TRASHED, VISIBILITY_PRIVATE);
  });
  
  DataObjectPool::registerTypeLoader('DiscussionComment', function($ids) {
    return Comments::findByIds($ids);
  });