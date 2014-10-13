<?php

  /**
   * project_exporter_object_link helper
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */
  
  /**
   * Show a project object link
   *
   * Parameters:
   * 
   * - id - id of the user
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_object_link($params, $template) {
  	$object_id = array_var($params, 'id');
  	
  	$object = ProjectExporterStorage::getProjectObject($object_id);
    if ($object instanceof ProjectObject) {
      $project_object_id = $object instanceof Task ? $object->getTaskId() : $object->getId();
			return '<a href="' . $template->tpl_vars['url_prefix']->value . Inflector::pluralize(strtolower($object->getBaseTypeName())) . '/' . strtolower($object->getBaseTypeName()) . '_' . $project_object_id . '.html">' . clean($object->getName()) . '</a>';
    } // if
    return '';
  } // smarty_function_project_exporter_object_link