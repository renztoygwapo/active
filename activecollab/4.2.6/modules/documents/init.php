<?php

  /**
   * Documents module initialization file
   * 
   * @package activeCollab.modules.documents
   */
  
  define('DOCUMENTS_MODULE', 'documents');
  define('DOCUMENTS_MODULE_PATH', APPLICATION_PATH . '/modules/documents');

  require_once __DIR__ . '/resources/autoload_model.php';
  
  AngieApplication::setForAutoload(array(
    'DocumentCategory' => DOCUMENTS_MODULE_PATH . '/models/DocumentCategory.class.php', 
    'IDocumentCategoryImplementation' => DOCUMENTS_MODULE_PATH . '/models/IDocumentCategoryImplementation.class.php',

    'IDocumentSearchItemImplementation' => DOCUMENTS_MODULE_PATH . '/models/search/IDocumentSearchItemImplementation.class.php',
    'DocumentsSearchIndex' => DOCUMENTS_MODULE_PATH . '/models/search/DocumentsSearchIndex.class.php',

    'IDocumentPreviewImplementation' => DOCUMENTS_MODULE_PATH . '/models/IDocumentPreviewImplementation.class.php',

    'IDocumentsSubscriptionsImplementation' => DOCUMENTS_MODULE_PATH . '/models/IDocumentsSubscriptionsImplementation.class.php',

    'NewFileDocumentNotification' => DOCUMENTS_MODULE_PATH . '/notifications/NewFileDocumentNotification.class.php',
    'NewTextDocumentDocumentNotification' => DOCUMENTS_MODULE_PATH . '/notifications/NewTextDocumentDocumentNotification.class.php',
  ));

  DataObjectPool::registerTypeLoader('Document', function($ids) {
    return Documents::findByIds($ids);
  });