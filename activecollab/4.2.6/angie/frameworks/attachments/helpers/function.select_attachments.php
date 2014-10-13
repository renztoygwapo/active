<?php

  /**
   * attachments_uploader helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render multiupload attachments form
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_attachments($params, &$smarty) {
    static $ids = array();

    if (DiskSpace::isUsageLimitReached()) {
      return '<p class="details">' . lang('Disk Quota Reached. Please consult your system administrator.') . '</p>';
    } // if

    $user = array_var($params, 'user');
    if(!($user instanceof IUser)) {
      throw new InvalidInstanceError('user', $user, 'IUser');
    } // if
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $counter = 1;
      do {
        $id = 'file_uploader_' . $counter++;
      } while(in_array($id, $ids));
    } // if
    
    $id.= '_' .  time();
    $ids[] = $id;
    
    $pending_name = array_var($params, 'name', null);
    if(empty($pending_name)) {
      throw new InvalidParamError('name', $pending_name, 'name attribute is required for select object attachments helper');
    } // if
    $delete_name = $pending_name . '[delete][]';
    $pending_name .= '[pending_parent][]';
   
    $object = array_var($params, 'object', null);
    if($object instanceof IAttachments) {
      $existing_attachments = $object->isLoaded() ? $object->attachments()->get($user) : null;
    } else {
      throw new InvalidParamError('object', $object, '$object does not support attachments');
    } // if
    
    $formated_existing_attachments = array();
    if (is_foreachable($existing_attachments)) {
    	foreach ($existing_attachments as $existing_attachment) {
				$formated_existing_attachments[] = array(
					'id'				=> $existing_attachment->getId(),
					'filename'	=> $existing_attachment->getName()
				);
    	} // foreach
    } // if

    $uploader_options = array(
      'wrapper_id'            => $id,
      'pending_field_name'    => $pending_name,
      'delete_field_name'     => $delete_name,
      'existing_attachments'  => $formated_existing_attachments,
      'max_file_size'         => get_max_upload_size(),
      'upload_url'            => Router::assemble('temporary_attachment_add', array('async' => 1)),
      'upload_name'           => 'attachment'
    );

    // variables needed for flash upload
    $uploader_options = array_merge($uploader_options, array(
      'uploader_runtimes'           => FILE_UPLOADER_RUNTIMES,
      'flash_uploader_url'          => AngieApplication::getAssetUrl('plupload.flash.swf', FILE_UPLOADER_FRAMEWORK, 'flash'),
      'silverlight_uploader_url'    => AngieApplication::getAssetUrl('plupload.silverlight.xap', FILE_UPLOADER_FRAMEWORK, 'silverlight'),
    ));

    $smarty->assign(array(
      '_select_object_attachments_id'                 => $id,
      '_select_object_attachments_show_first_input'   => array_var($params, 'show_first_input', $object->isNew()),
      '_select_object_attachments_uploader_options'   => $uploader_options
    ));

    return $smarty->fetch(get_view_path('_select_attachments', null, ATTACHMENTS_FRAMEWORK));
  } // smarty_function_select_object_attachments