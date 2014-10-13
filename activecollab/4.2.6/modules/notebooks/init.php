<?php

  /**
   * Init notebooks module
   *
   * @package activeCollab.modules.notebooks
   */
  
  define('NOTEBOOKS_MODULE', 'notebooks');
  define('NOTEBOOKS_MODULE_PATH', APPLICATION_PATH . '/modules/notebooks');
  
  AngieApplication::setForAutoload(array(
    'Notebook' => NOTEBOOKS_MODULE_PATH . '/models/notebooks/Notebook.class.php',
    'Notebooks' => NOTEBOOKS_MODULE_PATH . '/models/notebooks/Notebooks.class.php',
    
    'INotebookPageCommentsImplementation' => NOTEBOOKS_MODULE_PATH . '/models/INotebookPageCommentsImplementation.class.php',
    'INotebookPageSubscriptionsImplementation' => NOTEBOOKS_MODULE_PATH . '/models/INotebookPageSubscriptionsImplementation.class.php',
    
    'NotebookInspectorProperty' => NOTEBOOKS_MODULE_PATH . '/models/NotebookInspectorProperty.class.php',
    'NotebookPageComment' => NOTEBOOKS_MODULE_PATH . '/models/NotebookPageComment.class.php',

    'INotebookAvatarImplementation'	=> NOTEBOOKS_MODULE_PATH . '/models/INotebookAvatarImplementation.class.php',
  
    'NotebooksProjectExporter' => NOTEBOOKS_MODULE_PATH . '/models/NotebooksProjectExporter.class.php',
  
    // Activity log helpers
    'NotebookPageVersionCreatedActivityLogCallback' => NOTEBOOKS_MODULE_PATH . '/models/javascript_callbacks/NotebookPageVersionCreatedActivityLogCallback.class.php',
    'INotebookPageActivityLogsImplementation' => NOTEBOOKS_MODULE_PATH . '/models/INotebookPageActivityLogsImplementation.class.php',
  
	  // Inspector
    'INotebookInspectorImplementation' => NOTEBOOKS_MODULE_PATH . '/models/INotebookInspectorImplementation.class.php',
    'INotebookPageInspectorImplementation' => NOTEBOOKS_MODULE_PATH . '/models/INotebookPageInspectorImplementation.class.php',

    // search
    'INotebookSearchItemImplementation' => NOTEBOOKS_MODULE_PATH . '/models/INotebookSearchItemImplementation.class.php',
    'INotebookPageSearchItemImplementation' => NOTEBOOKS_MODULE_PATH . '/models/INotebookPageSearchItemImplementation.class.php',

    // sharing
    'INotebookSharingImplementation' => NOTEBOOKS_MODULE_PATH . '/models/INotebookSharingImplementation.class.php',

    // state
    'INotebookStateImplementation' => NOTEBOOKS_MODULE_PATH . '/models/INotebookStateImplementation.class.php',
    'INotebookPageStateImplementation' => NOTEBOOKS_MODULE_PATH . '/models/INotebookPageStateImplementation.class.php',

    // Notifications
    'NewNotebookNotification' => NOTEBOOKS_MODULE_PATH . '/notifications/NewNotebookNotification.class.php',
    'NewNotebookPageNotification' => NOTEBOOKS_MODULE_PATH . '/notifications/NewNotebookPageNotification.class.php',
    'NewNotebookPageVersionNotification' => NOTEBOOKS_MODULE_PATH . '/notifications/NewNotebookPageVersionNotification.class.php',
  ));
  
  AngieApplication::useModel(array('notebook_pages', 'notebook_page_versions'), NOTEBOOKS_MODULE);
  
  DataObjectPool::registerTypeLoader('Notebook', function($ids) {
    return Notebooks::findByIds($ids, STATE_TRASHED, VISIBILITY_PRIVATE);
  });
  
  DataObjectPool::registerTypeLoader('NotebookPage', function($ids) {
    return NotebookPages::findByIds($ids, STATE_TRASHED);
  });
  
  DataObjectPool::registerTypeLoader('NotebookPageComment', function($ids) {
    return Comments::findByIds($ids);
  });

  DataObjectPool::registerTypeLoader('NotebookPageVersion', function($ids) {
    return NotebookPageVersions::findByIds($ids);
  });