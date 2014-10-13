<?php

  /**
   * ProjectTemplates class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  class ProjectTemplates extends BaseProjectTemplates {

	  /**
	   * Returns true if $user can create a new templates
	   *
	   * @param User $user
	   * @return boolean
	   */
	  static function canAdd(User $user) {
		  return self::canManage($user);
	  } // canAdd

	  /**
	   * Returns true if $user can manage templates
	   *
	   * @param User $user
	   * @return boolean
	   */
	  static function canManage(User $user) {
		  return $user instanceof User && ($user->isProjectManager() || $user->isAdministrator() || $user->getSystemPermission('can_add_projects'));
	  } // canManage

	  /**
	   * Find all project templates, and prepare them for objects list
	   *
	   * @param User $user
	   * @return array
	   */
	  static function findForObjectsList($user) {
		  $result = array();

		  $templates = DB::execute("SELECT id, name FROM " . TABLE_PREFIX . "project_templates ORDER BY ISNULL(position) ASC, position ASC");
		  if (is_foreachable($templates)) {
			  $template_url = Router::assemble('project_template', array('template_id' => "--TEMPLATEID--"));
			  $default_avatar_url = ROOT_URL . '/template_covers/default.145x145.png';

			  foreach ($templates as $template) {
				  $template_avatar_path = ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/' . '/template_covers/' . $template['id'] . '.145x145.png';
				  $template_avatar_url = ROOT_URL . '/template_covers/' . $template['id'] . '.145x145.png';

				  $result[] = array(
					  'id' => $template['id'],
					  'name' => $template['name'],
					  'permalink' => str_replace('--TEMPLATEID--', $template['id'], $template_url),
					  'avatar' => array(
						  'large' => is_file($template_avatar_path) ? $template_avatar_url : $default_avatar_url
					  )
				  );
			  } // foreach
		  } // if

		  return $result;
	  } // findForObjectsList

	  /**
	   * Return ID name map of templated
	   *
	   * @return array
	   */
	  static function getIdNameMap() {
		  $result = array();

		  $templates = DB::execute("SELECT id, name FROM " . TABLE_PREFIX . "project_templates ORDER BY ISNULL(position) ASC, position ASC");

		  if(is_foreachable($templates)) {
			  foreach ($templates as $template) {
				  $result[array_var($template, 'id')] = array_var($template, 'name');
			  } // foreach
		  } // if

		  return $result;
	  } // getIdNameMap

	  /**
	   * Delete children by parent
	   *
	   * @param Project $parent
	   * @return bool
	   * @throws Exception
	   */
	  static function deleteChildrenByParent(Project $parent) {
		  $type = ucfirst(strtolower($parent->getType()));

		  // find all attached files
		  $files = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_object_templates WHERE template_id = ? AND type = ?", $parent->getId(), 'File');

		  try {
			  DB::beginWork('Deleting '.$type.' @ ' . __CLASS__);

			  DB::execute("DELETE FROM " . TABLE_PREFIX . "project_object_templates WHERE template_id = ?", $parent->getId());

			  DB::commit($type.' deleted @ ' . __CLASS__);
		  } catch (Exception $e) {
			  DB::rollback('Failed to delete '.$type.' @ ' . __CLASS__);

			  throw $e;
		  } // try

		  if (is_foreachable($files)) {
			  foreach($files as $file) {
				  $value = unserialize(array_var($file, 'value'));
				  @unlink(UPLOAD_PATH . '/' . array_var($value, 'location'));
			  } // foreach
		  } // if

		  return true;
	  } // deleteChildrenByParent
  
  }