<?php

  /**
   * Text document added to project's assets section
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class TextDocument extends ProjectAsset implements ISharing {
    
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
      'integer_field_1', 'integer_field_2', 'datetime_field_1', 'varchar_field_1','varchar_field_2', 
      'version', 'position',
    );
    
    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'version_num' => 'integer_field_1',
      'last_version_on' => 'datetime_field_1',
    	'last_version_by_id' => 'integer_field_2',
    	'last_version_by_name' => 'varchar_field_1',
    	'last_version_by_email' => 'varchar_field_2'
    );
    
    /**
     * Return verbose type name
     * 
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('document', null, true, $language) : lang('Document', null, true, $language);
    } // getVerboseType
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view asset URL
     *
     * @return string
     */
    function getViewUrl() {
      return Router::assemble('project_assets_text_document', array(
        'project_slug' => $this->getProject()->getSlug(),
        'asset_id' => $this->getId(),
      ));
    } // getViewUrl
    
    /**
     * Return icon URL
     *
     * @return string
     */
    function getIconUrl() {
      return AngieApplication::getImageUrl('icons/32x32/text-document.png', FILES_MODULE, AngieApplication::INTERFACE_PHONE);
    } // getIconUrl

    /**
     * Get revert to URL
     *
     * @param TextDocumentVersion || string $version
     * @return string
     */
    function getRevertUrl($version) {
      $to = $version instanceof TextDocumentVersion ? $version->getVersionNum() : $version;

      return Router::assemble('project_assets_text_document_version_revert', array(
        'project_slug' => $this->getProject()->getSlug(),
        'asset_id' => $this->getId(),
        'to' => $to,
      ));
    } // getRevertUrl
    
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
     * @return DateTimeValue
     */
    function setLastVersionOn($value) {
      return $this->setDatetimeField1($value);
    } // setLastVersionOn
    
    /**
     * Get last_version_by
     * 
     * @return IUser
     */
    function getLastVersionBy() {
      return $this->getUserFromFieldSet('last_version_by') instanceof IUser ? $this->getUserFromFieldSet('last_version_by') : $this->getCreatedBy();
    } // getLastVersionBy
    
    /**
     * Set last version by
     * 
     * @param IUser $user
     */
    function setLastVersionBy($user) {
    	$this->setUserFromFieldSet($user, 'last_version_by');
    } // setLastVersionBy
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------

    /**
     * Cached preview interface
     * 
     * @param ITextDocumentPreviewImplementation
     */
    var $preview = false;

    /**
     * Return preview helper
     *
     * @return IDownloadPreviewImplementation
     */
    function preview() {
      if($this->preview === false) {
        $this->preview = new ITextDocumentPreviewImplementation($this);
      } // if
      
      return $this->preview;
    } // preview

    /**
     * Cached sharing implementation helper
     *
     * @var ITextDocumentSharingImplementation
     */
    private $sharing = false;

    /**
     * Return sharing helper instance
     *
     * @return ITextDocumentSharingImplementation
     */
    function sharing() {
      if($this->sharing === false) {
        $this->sharing = new ITextDocumentSharingImplementation($this);
      } // if

      return $this->sharing;
    } // sharing
    
    /**
     * Document versions helper implementation
     *
     * @var ITextDocumentVersionsImplementation
     */
    private $versions = false;
    
    /**
     * Return text document versions helper implementation
     *
     * @return ITextDocumentVersionsImplementation
     */
    function versions() {
      if($this->versions === false) {
        $this->versions = new ITextDocumentVersionsImplementation($this);
      } // if
      
      return $this->versions;
    } // versions
    
    /**
     * Cached activity logs helpers instance
     *
     * @var ITextDocumentActivityLogsImplementation
     */
    private $activity_logs = false;
    
    /**
     * Return activity logs helper instance
     * 
     * @return ITextDocumentActivityLogsImplementation
     */
    function activityLogs() {
      if($this->activity_logs === false) {
        $this->activity_logs = new ITextDocumentActivityLogsImplementation($this);
      } // if
      
      return $this->activity_logs;
    } // activityLogs
    
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
			
			if ($detailed) {
				$result['versions'] = $this->versions()->describe($user, false, $for_interface);
				$previous_version = $this->versions()->previous();
      	$result['previous_version'] = $previous_version instanceof TextDocumentVersion ? $previous_version->describe($user, false, $for_interface) : null;
        $result['urls']['revert'] = $this->getRevertUrl('--REVERT-TO-VERSION--');
      } // if
      return $result;
    } // describe

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
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describeForApi($user, $detailed);

      $result['version'] = $this->getVersionNum();
      $result['last_version_on'] = $this->getLastVersionOn();
      $result['last_version_by'] = $result['last_version_on'] ? $this->getLastVersionBy() : null;

      if ($detailed) {
        $result['versions'] = $this->versions()->describeForApi($user);
        $previous_version = $this->versions()->previous();
        $result['previous_version'] = $previous_version instanceof TextDocumentVersion ? $previous_version->describeForApi($user) : null;
        $result['urls']['revert'] = $this->getRevertUrl('--REVERT-TO-VERSION--');
      } // if
      return $result;
    } // describeForApi
    
    /**
     * Save to database
     *
     * @return boolean
     */
    function save() {
      if($this->isNew()) {
        $this->setVersionNum(1);
      } // if
      
      return parent::save();
    } // save
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('body', 1)) {
        $errors->addError(lang('Text is required'), 'body');
      } // if
      
      parent::validate($errors);
    } // validate
    
  }