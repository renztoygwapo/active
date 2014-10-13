<?php

  /**
   * Project exporter
   * 
   * @package activeCollab.modules.project_exporter
   * @subpackage models
   */
	class ProjectExporter implements IDescribe, IJSON {
		
		/**
		 * Current section to export
		 * 
		 * @var string
		 */
		protected $section;
		
		/**
		 * Wireframe for exported page
		 * 
		 * @var string
		 */
		protected $wireframe;
		
		/**
		 * Project which needs to be exported
		 * 
		 * @var Project
		 */
		protected $project;
		
		/**
		 * Logged user
		 * 
		 * @var User
		 */
		protected $logged_user;
				
		/**
		 * Objects visibility
		 * 
		 * @var integer
		 */
		protected $objects_visibility;
		
		/**
		 * Set the base path where files will be exported
		 * 
		 * @var String
		 */
		protected $base_path;
		
		/**
		 * Relative path where exported files will be stored
		 * 
		 * @var String
		 */
		protected $relative_path = '';
		
		/**
		 * Relative path where uploaded files will be stored
		 * 
		 * @var String
		 */
		protected $files_relative_path = '_uploaded_files';
		
		/**
		 * Relative path where avatar images will be stored
		 * 
		 * @var String
		 */
		protected $avatars_relative_path = '_avatars';
		
        /**
         * Url prefix
         * 
         * @var String
         */
        protected $url_prefix = '../';
		
		/**
		 * Currently active module
		 * 
		 * @var string
		 */
		protected $active_module = SYSTEM_MODULE;
		
		/**
		 * Smarty instance
		 * 
		 * @var Smarty
		 */
		protected $smarty;
		
		/**
		 * Cached templates
		 * 
		 * @var array
		 */
		protected $templates;
		
		/**
		 * We use this storage to be able to use memory available wisely
		 * 
		 * @var array
		 */
		protected $storage;
		
		/**
		 * Users cache
		 * 
		 * @var array
		 */
		protected $users;
		
		/**
		 * Main navigation
		 * 
		 * @var string
		 */
		protected $navigation;
		
		/**
		 * Navigation sections
		 * 
		 * @var array
		 */
		protected $navigation_sections;
		
		/**
		 * Errors ocurred
		 * 
		 * @var array
		 */
		protected $errors;
		
		/**
		 * Warnings ocurred while exporting
		 * 
		 * @var array
		 */
		protected $warnings;
		
		/**
		 * Constructor
     *
     * @param string $current_section
     * @param mixed $all_sections
		 */
		function __construct($current_section = null, $all_sections = null) {
			$this->section = $current_section;
			if ($this instanceof SystemProjectExporter) {
			  $this->url_prefix = '';
			} //if
			$this->smarty = SmartyForAngie::getInstance();

			
			// load wireframe
			$this->wireframe = file_get_contents(PROJECT_EXPORTER_MODULE_PATH . '/resources/exported_wireframe.tpl');

			$helpers = get_files(PROJECT_EXPORTER_MODULE_PATH . '/helpers', 'php', false);
			foreach ($helpers as $helper) {
				require_once $helper;
			} // foreach
			
			// build navigation
			$this->navigation_sections = $all_sections;
			$this->buildNavigation();

      $this->smarty->assign(array(
        'url_prefix' => $this->url_prefix,
        'exporter' => $this,
        'navigation_sections' => $this->navigation_sections
      ));
			
			// init users cache
			$this->users = array();			
		} // __construct
		
		/**
		 * build the navigation DOM
		 */
		public function buildNavigation() {
			if (is_foreachable($this->navigation_sections)) {
				$this->navigation = '<ul id="exported_modules">';
				foreach ($this->navigation_sections as $id => $navigation_element) {
				  $checked = ($id === $this->section) ? 'class = "selected"' : ''; 
				  if ($id === 'system') {
				  	$this->navigation.= '<li><a ' . $checked . ' href="' . $this->url_prefix . 'index.html">' . clean($navigation_element['name']) . '</a></li>';
				  } else {
				    $this->navigation.= '<li><a ' . $checked . ' href="' . $this->url_prefix . $id . '/index.html">' . clean($navigation_element['name']) . '</a></li>';
				  }
				} // foreach
				$this->navigation.= '</ul>';
			} // if
		} // buildNavigation

		/**
		 * Set the project
		 * 
		 * @param Project $project
		 */
		function setProject(Project $project) {
			$this->project = $project;
			$this->smarty->assignByRef('project', $this->project);
		} // setProject
		
		/**
		 * Get the project
		 * 
		 * @return Project
		 */
		function getProject() {
			return $this->project;
		} // getProject

		/**
		 * set the logged user
		 * 
		 * @param User $project
		 */
		function setLoggedUser(User $user) {
			$this->logged_user = $user;
			$this->smarty->assignByRef('logged_user', $this->logged_user);
		} // setLoggedUser
		
		/**
		 * Get the logged user
		 * 
		 * @return User
		 */
		function getLoggedUser() {
			return $this->logged_user;
		} // setLoggedUser
		
		/**
		 * Sets the min-visibility of objects that will be exported 
		 * 
		 * @param integer $visibility
		 */
		public function setObjectsVisibility($visibility) { 
			$this->objects_visibility = $visibility;
			$this->smarty->assign('visibility', $visibility);
		} // setObjectsVisibility
		
		/**
		 * Get the min-visibility
		 * 
		 * @return integer
		 */
		public function getObjectsVisibility() {
			return $this->objects_visibility;
		} // getObjectsVisibility
		
		/**
		 * Sets the base path for exported files
		 * 
		 * @param string $path
		 */
		public function setBasePath($path) {
			$this->base_path = $path;	
		} // setBasePath
		
		/**
		 * Return get base path
		 * 
		 * @return string
		 */
		public function getBasePath() {
			return $this->base_path;
		} // getBasePath
		
		/**
		 * Returns the destination path for $file if provided, if not it returns directory
		 * 
		 * @param string $file
		 * @return string
		 */
		public function getDestinationPath($file, $relative_path = true) {
			if ($this->relative_path && $relative_path) {
				return $this->getBasePath() . '/' . $this->project->getSlug() . '/' . $this->relative_path . '/' . $file;
			} else {
				return $this->getBasePath() . '/' . $this->project->getSlug() . '/' . $file;
			} // if
		} // getDestinationPath
		
		/**
		 * Returns the destination path for compressed project export
		 * 
		 * @return string
		 */
		public function getCompressedPath() {
		  return $this->getBasePath() . '/' . $this->project->getSlug() . '.zip';
		} // getCompressedPath
		
		/**
		 * Returns the destination directory
		 * 
		 * @return string
		 */
		public function getDestinationDirectory($relative_path = true) {
			if ($this->relative_path && $relative_path) {
				return $this->getBasePath() . '/' . $this->project->getSlug() . '/' . $this->relative_path;
			} else {
				return $this->getBasePath() . '/' . $this->project->getSlug();
			} // if
		} // getDestinationDirectory
		
		/**
		 * Destination path for the specified file 
		 * 
		 * @param string $file
		 * @return string
		 */
		public function getUploadedFilePath($file) {
			return $this->getBasePath() . '/' . $this->project->getSlug() . '/' . $this->files_relative_path . '/' . $file;
		} // getUploadedFilePath
		
		/**
		 * Uploaded files directory
		 * 
		 * @param void
		 * @return string
		 */
		public function getUploadedFilesDirectory() {
			return $this->getBasePath() . '/' . $this->project->getSlug() . '/' . $this->files_relative_path;
		} // getUploadedFilesDirectory
		
		/**
		 * Destination path for the specified avatar 
		 * 
		 * @param string $avatar
		 * @return string
		 */
		public function getUploadedAvatarPath($avatar) {
			return $this->getBasePath() . '/' . $this->project->getSlug() . '/' . $this->files_relative_path . '/'. $this->avatars_relative_path .  '/' . $avatar;
		} // getUploadedAvatarPath
		
		/**
		 * Uploaded avatars directory
		 * 
		 * @param void
		 * @return string
		 */
		public function getUploadedAvatarDirectory() {
			return $this->getBasePath() . '/' . $this->project->getSlug() . '/' . $this->files_relative_path . '/' . $this->avatars_relative_path;
		} // getUploadedAvatarDirectory
		
		/**
		 * Store file in uploads folder
		 * 
		 * @param string $original_filename
		 * @param string $filename_on_the_system
		 * @return string
		 */
		public function storeFile($original_filename, $source_path, $return_link = false) {
			$pathinfo = pathinfo($original_filename);

			$new_filename = $pathinfo['filename'] . '_' . make_string(15) . ($pathinfo['extension'] ? '.' . $pathinfo['extension'] : null);
		  $destination_path = $this->getUploadedFilePath($new_filename);
		    
			if (copy($source_path, $destination_path)) {
  			  if ($return_link) {
  				return '<a href="' . $this->url_prefix . $this->files_relative_path . '/' . $new_filename . '" target="_blank">' . $original_filename . '</a>';
  			  } else {
  				return $this->url_prefix . $this->files_relative_path . '/' . $new_filename;
  			  } // if
			} // if
			
			$this->addWarning(lang('Failed to copy :file file', array('file' => $source_path)));
			return false;
		} // storeFile
		
		/**
		 * Store avatar in avatars folder
		 * 
		 * @param string $original_filename
		 * @param string $filename_on_the_system
		 * @return string
		 */
		public function storeAvatar($original_filename, $source_path, $return_link = false) {
			$pathinfo = pathinfo($original_filename);
			$new_filename = 'avatar' . '_' . $pathinfo['filename'] . ($pathinfo['extension'] ? '.' . $pathinfo['extension'] : null);
		    $destination_path = $this->getUploadedAvatarPath($new_filename);
			if (copy($source_path, $destination_path)) {
  			  if ($return_link) {
  				return '<img src="' . $this->url_prefix . $this->files_relative_path . '/' . $this->avatars_relative_path . '/' . $new_filename . '" alt="'.$new_filename.'" />';
  			  } else {
  				return $new_filename;
  			  } // if
			} // if
			
			$this->addWarning(lang('Failed to copy :avatar avatar', array('avatar' => $source_path)));
			return false;
		} // storeFile
		
		/**
		 * Display user links, and cache users
		 */
		public function getUserLink($user_id, $name, $email) {
			if (!isset($this->users[$user_id])) {
				$this->users[$user_id] = Users::findById($user_id);
				
				if (!($this->users[$user_id] instanceof User)) {
					$this->users[$user_id] = new AnonymousUser($name, $email);
				} // if
			} // if
			
			return '<a href="' . $this->users[$user_id]->getEmail() . '">' . $this->users[$user_id]->getDisplayName() . '</a>';
		} // if
		
		/**
		 * Render template into destination file
		 * 
		 * @param string $template_name
		 * @param array $variables
		 * @param string $destination
		 */
		public function renderTemplate($template_name, $destination) {
// 			SMARTY OPTION

			$this->storage['template_file'] = get_view_path($template_name, 'exporter', $this->active_module, AngieApplication::INTERFACE_DEFAULT);
			$this->storage['fetched'] = $this->smarty->fetch($this->storage['template_file']);

			$this->storage['compiled'] = str_replace('{$content_for_layout}', $this->storage['fetched'], $this->wireframe);
			$this->storage['compiled'] = str_replace('{$main_navigation}', $this->navigation, $this->storage['compiled']);
			$this->storage['compiled'] = str_replace('{$url_prefix}', $this->url_prefix, $this->storage['compiled']);
			$this->storage['compiled'] = str_replace('{$project_name}', $this->project->getName(), $this->storage['compiled']);
      $this->storage['compiled'] = str_replace('{$year}', DateTimeValue::now()->getYear(), $this->storage['compiled']);
			
			file_put_contents($destination, $this->storage['compiled']);
		
// 			PHP OPTION
//			file_put_contents($destination, $this->fetchTemplate($template_name, $variables));
		} // renderTemplate
		
		/**
		 * Fetch the specified template
		 * 
		 * @param string $template_name
		 * @param array $variables
		 * @return string
		 */
		public function fetchTemplate($template_name, $variables) {
			extract($variables);
			$template_file = get_view_path($template_name, 'exporter', $this->active_module, AngieApplication::INTERFACE_DEFAULT);
			ob_start();
			include $template_file;
			$fetch = ob_get_contents();
			ob_end_clean();
			return $fetch;
		} // fetchTemplate
		
		/**
		 * Do the export
		 */
		public function export() {
			if (!$this->getBasePath()) {
				throw new Error(lang('Base export directory for :exporter_class has not been set', array('exporter_class' => get_class($this))));
			} // if
			
			// create folder for this exporter 
			if ($this instanceof SystemProjectExporter) {
			  $this->cleanup();
			} //if
			
			// create folder structure for this exporter
			if (!is_dir($this->getDestinationDirectory())) {
				if (!recursive_mkdir($this->getDestinationDirectory(), 0777, $this->getBasePath())) {
					throw new DirectoryCreateError($this->getDestinationDirectory());
				} // if
			} // if
			
			if (!is_dir($this->getUploadedFilesDirectory())) {
				if (!recursive_mkdir($this->getUploadedFilesDirectory(), 0777, $this->getBasePath())) {
					throw new DirectoryCreateError($this->getUploadedFilesDirectory());
				} // if
			} //if
			
		    if (!is_dir($this->getUploadedAvatarDirectory())) {
        	  	if (!recursive_mkdir($this->getUploadedAvatarDirectory(), 0777, $this->getBasePath())) {
        			  throw new DirectoryCreateError($this->getUploadedAvatarDirectory());
        		} // if
        	} //if
			
			if ($this instanceof SystemProjectExporter) {
				// copy assets 
				copy_dir(PROJECT_EXPORTER_MODULE_PATH . '/resources/html_assets', $this->getDestinationPath('_assets', false), null, true);
			} // if
		} // export
		
		/***
		 * Do the pre/post export cleanup
		 * 
		 * @param void
		 * @return null
		 */
		public function cleanup() {
		  if (is_dir($this->getDestinationDirectory(false))) {
    		if (!delete_dir($this->getDestinationDirectory(false))) {
    		  throw new DirectoryDeleteError($this->getDestinationDirectory(false));
    		} // if
		  } //if
		  return true;
		} // cleanup
		
		/**
		 * Finalize export
		 * 
		 * @param int $compress
		 * @return null
		 */
		public function finalize($compress) {
		  $return = array(
		      'compress' => 0,
		      'info_path' => $this->getDestinationDirectory(false)
		  );
		  if ($compress) {
    		if (!CAN_USE_ZIP) {
    		  throw new Error(lang('Zlib extension not loaded, could not create archive file :archive. Exported files are available in folder :folder', array("archive" => $this->getCompressedPath(), "folder" => $this->getDestinationDirectory())));
    		} else {
    		  require_once ANGIE_PATH . '/classes/PclZip.class.php';
    		  $archive = new PclZip($this->getCompressedPath());
    		  if ($archive->create($this->getDestinationDirectory(),PCLZIP_OPT_REMOVE_PATH,$this->getBasePath()) === 0) {
    		    throw new Error(lang('Could not create archive file :archive. Exported files are available in folder :folder', array("archive" => $this->getCompressedPath(), "folder" => $this->getDestinationDirectory())));
    		  } else {
    		    $this->cleanup();
    		    $return['compress'] = 1;
    		    $return['info_path'] = Router::assemble('project_exporter_download_export',array('project_slug' => $this->project->getSlug()));
    		  }//if
    		}//if
		  } //if
		  return $return;
		} // finalize
		
		public static function exportExists($project,$base_path) {
		  return is_file($base_path . '/project_' . $project->getId() . '.zip');
		} //exportExists
		
		/**
		 * Add warning message
		 * 
		 * @param string $warning_message
		 * @return null
		 */
		function addWarning($warning_message) {
			$this->warnings[] = $warning_message;
		} // addWarning
		
		/**
		 * Add fatal error
		 * 
		 * @param string $error_message
		 */
		function addError($error_message) {
			throw new Error($error_message);
		} // addError
		
    /**
     * Convert current exporter to JSON
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return string
     */
    function toJSON(IUser $user, $detailed = false, $for_interface = false) {
      return JSON::encode($this->describe($user, $detailed, $for_interface));
    } // toJSON
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
    	$return['type'] = get_class($this);
    	$return['warnings'] = $this->warnings;
    	return $return;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi

	}