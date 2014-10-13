<?php

  /**
   * File uploaded to project assets area
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class File extends ProjectAsset implements IDownload, IPreview, ISharing {
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    protected $fields = array(
      'id',
      'type', 'module',
      'project_id', 'milestone_id', 'category_id',
      'name', 'body',
      'state', 'original_state', 'visibility', 'original_visibility', 'is_locked',
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email',
      'integer_field_1', // version number
      'integer_field_2', // size
      'integer_field_3', // updated by id
      'varchar_field_1', // MIME type
      'varchar_field_2', // location
      'varchar_field_3', // md5
      'datetime_field_1', // version date and time
    	'text_field_1', // last_version_by_name
    	'text_field_2', // last_version_by_email
      'version', 'position',
    );
    
    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'version_num' => 'integer_field_1',
      'size' => 'integer_field_2',
      'mime_type' => 'varchar_field_1',
      'location' => 'varchar_field_2',
    	'md5' => 'varchar_field_3',
      'last_version_on' => 'datetime_field_1',
    	'last_version_by_id' => 'integer_field_3',
    	'last_version_by_name' => 'text_field_1',
    	'last_version_by_email' => 'text_field_2'
    );

    /**
     * Get verbose type for File object
     *
     * @param bool $lowercase
     * @param null $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('file', null, true, $language) : lang('File', null, true, $language);
    } // getVerboseType
    
    /**
     * Return path to view template
     *
     * @return string
     */
    function getViewTemplatePath() {
      return get_view_path('view', 'files', FILES_MODULE);
    } // getViewTemplatePath
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      $options->add('download', array(
        'url' => $this->getDownloadUrl(true),
        'text' => lang('Download'),
        'onclick' => new TargetBlankCallback(),
      	'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/download.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) : '',
      	'important' => true
      ), true);
      
      parent::prepareOptionsFor($user, $options, $interface);
    } // prepareOptionsFor
    
    // ---------------------------------------------------
    //  Move / copy
    // ---------------------------------------------------
    
    /**
     * Copy this object to $project
     *
     * @param Project $project
     * @param array $update_attributes
     * @param boolean $bulk
     * @return File
     * @throws Exception
     */
    function copyToProject(Project $project, $update_attributes = null, $bulk = false) {
      try {
        $copy = parent::copyToProject($project, $update_attributes, $bulk);
      
        if($copy instanceof File) {
          $path = $this->download()->getPath();
          
          if(is_file($path)) {
            $new_path = AngieApplication::getAvailableUploadsFileName();
            
            if(copy($path, $new_path)) {
              $copy->setLocation(basename($new_path));
              $copy->save();
            } // if
          } // if
        } // if
      } catch(Exception $e) {
        if(isset($new_path)) {
          @unlink($new_path);
        } // if
        
        throw $e;
      } // try
      
      return $copy;
    } // copyToProject
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return version number
     *
     * @return integer
     */
    function getVersionNum() {
      return $this->getIntegerField1();
    } // getVersionNum
    
    /**
     * Set version number
     *
     * @param integer $value
     * @return integer
     */
    function setVersionNum($value) {
      return $this->setIntegerField1($value);
    } // setVersionNum
    
    /**
     * Return size
     *
     * @return integer
     */
    function getSize() {
    	return $this->getIntegerField2();
    } // getSize
    
    /**
     * Set size
     *
     * @param integer $value
     */
    function setSize($value) {
      return $this->setIntegerField2($value);
    } // setSize
    
    /**
     * Return MIME type
     *
     * @return string
     */
    function getMimeType() {
    	return $this->getVarcharField1();
    } // getMimeType
    
    /**
     * Set MIME type
     *
     * @param string $value
     */
    function setMimeType($value) {
      return $this->setVarcharField1($value);
    } // setMimeType
    
    /**
     * Return location
     *
     * @return string
     */
    function getLocation() {
    	return $this->getVarcharField2();
    } // getLocation
    
    /**
     * Set location
     *
     * @param string $value
     */
    function setLocation($value) {
      return $this->setVarcharField2($value);
    } // setLocation
    
    /**
     * Return MD5 value
     * 
     * @return string
     */
    function getMd5() {
      $md5 = $this->getVarcharField3();
      
      if(empty($md5)) {
        $path = $this->download()->getPath();
        if(is_file($path)) {
          $md5 = md5_file($path);
          
          if($md5 && $this->isLoaded()) {
            DB::execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET varchar_field_3 = ? WHERE id = ?', $md5, $this->getId());
            AngieApplication::cache()->removeByObject($this);
          } // if
        } // if
      } // if
      
      return $md5;
    } // getMd5
    
    /**
     * Set file's MD5 hash value
     * 
     * @param string $value
     * @return string
     */
    function setMd5($value) {
      return $this->setVarcharField3($value);
    } // setMd5
    
    /**
     * Return last_version_on
     *
     * @return DateTimeValue
     */
    function getLastVersionOn() {
    	if ($this->getDatetimeField1() instanceof DateValue) {
    		return $this->getDatetimeField1();
    	} // if
    	return $this->getCreatedOn();
    } // getLastVersionOn
    
    /**
     * Set last_version_on
     *
     * @param DateTimeValue $value
     */
    function setLastVersionOn($value) {
      return $this->setDatetimeField1($value);
    } // setLastVersionOn
    
    /**
     * Get last_version_by
     * 
     * @return IUser|null
     */
    function getLastVersionBy() {
      return $this->getUserFromFieldSet('last_version_by') instanceof IUser ? $this->getUserFromFieldSet('last_version_by') : $this->getCreatedBy();
    } // getLastVersionBy
    
    /**
     * Set last version by
     * 
     * @param IUser|null $user
     */
    function setLastVersionBy($user) {
    	$this->setUserFromFieldSet($user, 'last_version_by');
    } // setLastVersionBy
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Download helper instance
     *
     * @var IFileDownloadImplementation
     */
    private $download = false;
    
    /**
     * Returns download helper instance
     *
     * @return IFileDownloadImplementation
     */
    function download() {
      if($this->download === false) {
        $this->download = new IFileDownloadImplementation($this);
      } // if
      
      return $this->download;
    } // download

    /**
     * Cached sharing implementation helper
     *
     * @var IFileSharingImplementation
     */
    private $sharing = false;

    /**
     * Return sharing helper instance
     *
     * @return IFileSharingImplementation
     */
    function sharing() {
      if($this->sharing === false) {
        $this->sharing = new IFileSharingImplementation($this);
      } // if

      return $this->sharing;
    } // sharing
    
    /**
     * Preview helper instance
     *
     * @var IPreviewImplementation
     */
    protected $preview = false;
    
    /**
     * Return preview helper
     *
     * @return IDownloadPreviewImplementation
     */
    function preview() {
      if($this->preview === false) {
        $this->preview = new IFilePreviewImplementation($this);
      } // if
      
      return $this->preview;
    } // preview
    
    /**
     * File versions helper implementation
     *
     * @var IFileVersionsImplementation
     */
    private $versions = false;
    
    /**
     * Return file versions helper implementation
     *
     * @return IFileVersionsImplementation
     */
    function versions() {
      if($this->versions === false) {
        $this->versions = new IFileVersionsImplementation($this);
      } // if
      
      return $this->versions;
    } // versions
    
    /**
     * Cached activity logs helpers instance
     *
     * @var IFileActivityLogsImplementation
     */
    private $activity_logs = false;
    
    /**
     * Return activity logs helper instance
     * 
     * @return IFileActivityLogsImplementation
     */
    function activityLogs() {
      if($this->activity_logs === false) {
        $this->activity_logs = new IFileActivityLogsImplementation($this);
      } // if
      
      return $this->activity_logs;
    } // activityLogs

    /**
     * State helper instance
     *
     * @var IFileStateImplementation
     */
    private $state = false;

    /**
     * Return state helper instance
     *
     * @return IFileStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new IFileStateImplementation($this);
      } // if

      return $this->state;
    } // state
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can upload a new file version
     *
     * @param User $user
     * @return boolean
     */
    function canUploadNewVersions($user) {
      return $this->canEdit($user);
    } // canUploadNewVersions
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Get the url to the page where file preview is rendered
     * 
     * @param boolean $large
     * @return tring
     */
    function getPreviewUrl($large = true) {
    	return Router::assemble('project_assets_file_preview', array(
        'project_slug' => $this->getProject()->getSlug(),
        'asset_id' => $this->getId(),
        'large' => $large,
    	)); 
    } // getPreviewUrl
    
    /**
     * Return download file URL
     *
     * @param boolean $force
     * @return string
     */
    function getDownloadUrl($force = false) {
      return Router::assemble('project_assets_file_download', array(
        'project_slug' => $this->getProject()->getSlug(),
        'asset_id' => $this->getId(),
        'disposition' => $force ? 'attachment' : 'inline',
      ));
    } // getDownloadUrl
    
    /**
     * Return new file version URL
     *
     * @return string
     */
    function getNewVersionUrl() {
      return Router::assemble('project_assets_file_versions_add', array(
        'project_slug' => $this->getProject()->getSlug(),
        'asset_id' => $this->getId(),
      ));
    } // getNewVersionUrl
    
    /**
     * Return refresh file details URL
     *
     * @return string
     */
    function getRefreshDetailsUrl() {
      return Router::assemble('project_assets_file_refresh_details', array(
        'project_slug' => $this->getProject()->getSlug(),
        'asset_id' => $this->getId(),
      ));
    } // getRefreshDetailsUrl
    
    /**
     * Return icon URL
     *
     * @return string
     */
    function getIconUrl() {
      return AngieApplication::getImageUrl('icons/32x32/file.png', FILES_MODULE, AngieApplication::INTERFACE_PHONE);
    } // getIconUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
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
      $result = parent::describe($user, $detailed, $for_interface);
      
			$result['version'] = $this->getVersionNum();
      $result['last_version_on'] = $this->getLastVersionOn();
      $result['last_version_by'] = $result['last_version_on'] ? $this->getLastVersionBy() : null;
      
			$result['mime_type'] = $this->getMimeType();
			$result['size'] = $this->getSize();
			$result['formated_size'] = format_file_size($this->getSize());
			$result['md5'] = $this->getMd5();
			
			$result['urls']['download'] = $this->getDownloadUrl(true);
			$result['urls']['preview'] = $this->getPreviewUrl();
			      
      if ($detailed) {
      	$result['versions'] = $this->versions()->describe($user, $detailed, $for_interface);
      	$previous_version = $this->versions()->previous();      	
      	$result['previous_version'] = $previous_version instanceof FileVersion ? $previous_version->describe($user, false, $for_interface) : null;
      } // if
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describe($user, $detailed);

      $result['version'] = $this->getVersionNum();

      $result['last_version_on'] = $this->getLastVersionOn();
      $result['last_version_by'] = null;

      if($result['last_version_on'] && $this->getLastVersionBy() instanceof IUser) {
        $result['last_version_by'] = $this->getLastVersionBy()->describeForApi($user, false);
      } // if

      $result['mime_type'] = $this->getMimeType();
      $result['size'] = $this->getSize();
      $result['formated_size'] = format_file_size($this->getSize());
      $result['md5'] = $this->getMd5();

      $result['urls']['download'] = $this->getDownloadUrl(true);
      $result['urls']['preview'] = $this->getPreviewUrl();

      $result['versions_count'] = $this->versions()->count();

      return $result;
    } // describeForApi
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('name', 1)) {
        $errors->addError(lang('Required'), 'name');
      } // if
      
      parent::validate($errors, true);
    } // validate
    
  }