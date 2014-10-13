<?php

  /**
   * file_versions helper implementation
   *
   * @package activeCollab.modules.files
   * @subpackage helpers
   */

  /**
   * Render file versions table
   * 
   * Parameters:
   * 
   * - file - Selected file
   * - user - User who is viewing the page
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_file_versions($params, &$smarty) {
    $file = array_required_var($params, 'file', true, 'File');
    $user = array_required_var($params, 'user', true, 'User');
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
        
    $smarty->assign(array(
      '_file_versions' => $file->versions()->get(), 
      '_file_versions_file' => $file, 
      '_file_versions_user' => $user, 
      '_file_versions_id' => isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('file_versions'),
      '_file_versions_wrap' => array_var($params, 'wrap', true),
    	'_file_versions_can_add' => $file->canUploadNewVersions($user),
    	'_file_versions_upload_new_version_url' => extend_url($file->getNewVersionUrl(), array('async' => 1))
    ));
    
    return $smarty->fetch(get_view_path('_file_versions', 'files', FILES_MODULE,$interface));
  } // smarty_function_file_versions