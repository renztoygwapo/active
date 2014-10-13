<?php
	/**
	 * Class that will cache all objects that are used during the export process
	 * 
	 * @package activeCollab.modules.project_exporter
	 * @subpackage model
	 * 
	 * @author godza
	 */
	final class ProjectExporterStorage {
		
		/**
		 * Stored users
		 * 
		 * @var array
		 */
		private static $users;
		
		/**
		 * Project objects
		 * 
		 * @var array
		 */
		private static $project_objects;
		
		/**
		 * Cache variable
		 * 
		 * @var array
		*/
		private static $variables;
		
		/**
		 * Cached object links
		 * 
		 * @var array
		 */
		private static $object_links;
		
		/**
		 * Categories
		 * 
		 * @var array
		 */
		private static $categories;
		
		/**
		 * Get cached user if user exists, or anonymous user if doesn't
		 * 
		 * @param integer $user_id
		 * @return User
		 */
		public static function getUser($user_id) {
			if (!$user_id) {
				return null;	
			} // if
			
			if (!isset(self::$users[$user_id])) {
				self::$users[$user_id] = Users::findById($user_id);
			} // if
			return self::$users[$user_id];
		} // getUser
		
		/**
		 * Get the project object
		 * 
		 * @param integer $object_id
		 * @return ProjectObject
		 */
		public static function getProjectObject($object_id) {
			if (!$object_id) {
				return null;	
			} // if
			
			if (!isset(self::$project_objects[$object_id])) {
				self::$project_objects[$object_id] = ProjectObjects::findById($object_id);
			} // if
			return self::$project_objects[$object_id];
		} // getProjectObject
		
		/**
		 * Get variable
		 * 
		 * @param void
		 * @return mixed
		 */
		public static function getVariable($variable_name) {
			if (!$variable_name) {
				return null;
			} // if
			 
			if (!isset(self::$variables[$variable_name])) {
				self::$variables[$variable_name] = '';
			} // if
			return self::$variables[$variable_name];			
		} // getVariable
		
		/**
		 * Get the object link
		 * 
		 * @param string $object_class
		 * @param mixed $object_id
		 * @return string
		 */
		public static function getObjectLink($object_class, $object_id) {
			if (!isset(self::$object_links[$object_class.'_'.$object_id])) {
				self::$object_links[$object_class.'_'.$object_id] = null;
			} // if
			return self::$object_links[$object_class.'_'.$object_id];		
		} // getObjectLink
		
		/**
		 * Get the category
		 * 
		 * @param integer $id
		 * @param string $type
		 * @return Category
		 */
		public static function getCategory($id) {
			if (!$id) {
				return false;
			} // if
			if (!isset(self::$categories[$id])) { 
				self::$categories[$id] = Categories::findById($id);
			} // if
			return self::$categories[$id];
		} // getCategory

	} // ProjectExporterStorage