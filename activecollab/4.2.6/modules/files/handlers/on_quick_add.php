<?php

  /**
   * Files module on_quick_add event handler
   *
   * @package activeCollab.modules.files
   * @subpackage handlers
   */
  
  /**
   * Handle on quick add event
   *
   * @param NamedList $items
   * @param NamedList $subitems
   * @param array $map
   * @param User $logged_user
   * @param DBResult $projects 
   * @param DBResult $companies
   * @param string $interface
   */
  function files_handle_on_quick_add($items, $subitems, &$map, $logged_user, $projects, $companies, $interface = AngieApplication::INTERFACE_DEFAULT) {
    $files_item_id = 'files';
    $youtube_item_id = 'youtube_video';
    $text_document_item_id = 'text_document';
    
    if(is_foreachable($projects)) {
      foreach($projects as $project) {
        if(ProjectAssets::canAdd($logged_user, $project)) {
        	$map[$files_item_id][] = 'project_' . $project->getId();
        	$map[$youtube_item_id][] = 'project_' . $project->getId();
        	$map[$text_document_item_id][] = 'project_' . $project->getId();
        } // if
      } // foreach
      
      if(isset($map[$files_item_id])) {
      	if($interface == AngieApplication::INTERFACE_DEFAULT) {
      		$items->add($files_item_id, array(
			      'text'	=> lang('File'),
			    	'title' => lang('Upload files in the Project:'),
			    	'dialog_title' => lang('Upload files in the :name Project'),
			    	'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/32x32/file.png', FILES_MODULE) : AngieApplication::getImageUrl('icons/96x96/files.png', FILES_MODULE, $interface),
			      'url'		=> Router::assemble('project_assets_files_add', array('project_slug' => '--PROJECT-SLUG--')),
			    	'group' => QuickAddCallback::GROUP_PROJECT,
            'handler_type' => 'flyoutFileForm',
			    	'event' => 'multiple_assets_created',
			    ));
      	} // if
		    
		    $items->add($text_document_item_id, array(
		      'text'	=> lang('Text Document'),
		    	'title' => lang('Add Text Document to the Project:'),
		    	'dialog_title' => lang('Add Text Document to the :name Project'),
		    	'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/32x32/text-document.png', FILES_MODULE) : AngieApplication::getImageUrl('icons/96x96/text-documents.png', FILES_MODULE, $interface),
		      'url'		=> Router::assemble('project_assets_text_document_add', array('project_slug' => '--PROJECT-SLUG--')),
		    	'group' => QuickAddCallback::GROUP_PROJECT,
		    	'event'	=> 'asset_created',    
		    ));
      } // if
    } // if
  } // files_handle_on_quick_add