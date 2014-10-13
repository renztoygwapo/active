<?php

  /**
   * Assets module initialization file
   *
   * @package activeCollab.modules.files
   */
  
  const FILES_MODULE = 'files';
  const FILES_MODULE_PATH = __DIR__;

  require_once __DIR__ . '/resources/autoload_model.php';
  require_once __DIR__ . '/functions.php';
  
  AngieApplication::setForAutoload(array(
    'ProjectAsset' => FILES_MODULE_PATH . '/models/project_assets/ProjectAsset.class.php', 
    'ProjectAssets' => FILES_MODULE_PATH . '/models/project_assets/ProjectAssets.class.php',

    // Comments
    'IAssetCommentsImplementation' => FILES_MODULE_PATH . '/models/IAssetCommentsImplementation.class.php',
    'AssetComment' => FILES_MODULE_PATH . '/models/AssetComment.class.php',

		// FILES
    'File' => FILES_MODULE_PATH . '/models/files/File.class.php', 
    'Files' => FILES_MODULE_PATH . '/models/files/Files.class.php',
    'IFileDownloadImplementation' => FILES_MODULE_PATH . '/models/files/IFileDownloadImplementation.class.php',
    'IFileVersionsImplementation' => FILES_MODULE_PATH . '/models/files/IFileVersionsImplementation.class.php', 
    'IFileVersionDownloadImplementation' => FILES_MODULE_PATH . '/models/files/IFileVersionDownloadImplementation.class.php',
  	'IFilePreviewImplementation' => FILES_MODULE_PATH . '/models/files/IFilePreviewImplementation.class.php',
  	'IFileActivityLogsImplementation' => FILES_MODULE_PATH . '/models/files/IFileActivityLogsImplementation.class.php', 
  	'IFileStateImplementation' => FILES_MODULE_PATH . '/models/files/IFileStateImplementation.class.php',
    'IncomingMailFileAction' => FILES_MODULE_PATH . '/models/files/IncomingMailFileAction.class.php',
  
  	// TEXT DOCUMENTS
    'TextDocument' => FILES_MODULE_PATH . '/models/text_documents/TextDocument.class.php',
    'TextDocuments' => FILES_MODULE_PATH . '/models/text_documents/TextDocuments.class.php',
		'ITextDocumentPreviewImplementation' => FILES_MODULE_PATH . '/models/text_documents/ITextDocumentPreviewImplementation.class.php',
		'ITextDocumentVersionsImplementation' => FILES_MODULE_PATH . '/models/text_documents/ITextDocumentVersionsImplementation.class.php',
		'ITextDocumentActivityLogsImplementation' => FILES_MODULE_PATH . '/models/text_documents/ITextDocumentActivityLogsImplementation.class.php',
    'IncomingMailTextDocumentAction' => FILES_MODULE_PATH . '/models/text_documents/IncomingMailTextDocumentAction.class.php',
  
    'AssetCategory' => FILES_MODULE_PATH . '/models/AssetCategory.class.php', 
    'IAssetCategoryImplementation' => FILES_MODULE_PATH . '/models/IAssetCategoryImplementation.class.php',

    'FlyoutFileFormCallback' => FILES_MODULE_PATH . '/models/javascript_callbacks/FlyoutFileFormCallback.class.php',
    'FileVersionCreatedActivityLogCallback' => FILES_MODULE_PATH . '/models/javascript_callbacks/FileVersionCreatedActivityLogCallback.class.php',
    'TextDocumentVersionCreatedActivityLogCallback' => FILES_MODULE_PATH . '/models/javascript_callbacks/TextDocumentVersionCreatedActivityLogCallback.class.php',
  
  	'IAssetSearchItemImplementation' => FILES_MODULE_PATH . '/models/IAssetSearchItemImplementation.class.php',
  	'IProjectAssetInspectorImplementation' => FILES_MODULE_PATH . '/models/IProjectAssetInspectorImplementation.class.php',
  
  	// SHARING
  	'IProjectAssetSharingImplementation' => FILES_MODULE_PATH . '/models/IProjectAssetSharingImplementation.class.php',
  	'IFileSharingImplementation' => FILES_MODULE_PATH . '/models/files/IFileSharingImplementation.class.php',
  	'ITextDocumentSharingImplementation' => FILES_MODULE_PATH . '/models/text_documents/ITextDocumentSharingImplementation.class.php',

    // Project exporter
    'FilesProjectExporter' => FILES_MODULE_PATH . '/models/FilesProjectExporter.class.php',

    // Notifications
    'NewFileNotification' => FILES_MODULE_PATH . '/notifications/NewFileNotification.class.php',
    'NewFileVersionNotification' => FILES_MODULE_PATH . '/notifications/NewFileVersionNotification.class.php',
    'MultipleFilesUploadedNotification' => FILES_MODULE_PATH . '/notifications/MultipleFilesUploadedNotification.class.php',
    'NewTextDocumentNotification' => FILES_MODULE_PATH . '/notifications/NewTextDocumentNotification.class.php',
    'NewTextDocumentVersionNotification' => FILES_MODULE_PATH . '/notifications/NewTextDocumentVersionNotification.class.php',
  ));
  
  DataObjectPool::registerTypeLoader(array('File', 'TextDocument'), function($ids) {
    return ProjectAssets::findByIds($ids, STATE_TRASHED, VISIBILITY_PRIVATE);
  });

  DataObjectPool::registerTypeLoader('FileVersion', function($ids) {
    return FileVersions::findByIds($ids);
  });

  DataObjectPool::registerTypeLoader('TextDocumentVersion', function($ids) {
    return TextDocumentVersions::findByIds($ids);
  });
  
  DataObjectPool::registerTypeLoader('AssetComment', function($ids) {
    return Comments::findByIds($ids);
  });