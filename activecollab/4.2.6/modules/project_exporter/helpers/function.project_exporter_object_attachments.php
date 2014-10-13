<?php

  /**
   * project_exporter_category_link helper
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */
  
  /**
   * Show a category link
   *
   * Parameters:
   * 
   * - id - id of the category
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_object_attachments($params, $template) {
		$object = array_var($params, 'object', null);
		
		if (!($object instanceof IAttachments)) {
			throw new InvalidInstanceError('object', $object, 'IAttachments');			
		} // if
		
		$visibility = array_var($params, 'visibility', $template->tpl_vars['visibility']->value);
        $attachments = Attachments::findByParent($object);
    
		if (!is_foreachable($attachments)) {
			return false;
		} // if		

		$return = '<div class="attachments"><ul class="attachments">';
		foreach ($attachments as $attachment) {
			$return .= '<li>' . $template->tpl_vars['exporter']->value->storeFile($attachment->getName(), UPLOAD_PATH . '/' . $attachment->getLocation(), true) . '</li>';
		} // foreach
		$return .= '</ul></div>';
    
    return $return;
  } // smarty_function_project_exporter_category_link