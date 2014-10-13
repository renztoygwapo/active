<?php

  /**
   * Framework level attachment implementation
   *
   * @package angie.frameworks.attachments
   * @subpackage models
   */
  abstract class FwAttachment extends BaseAttachment implements IRoutingContext, ICreatedBy, IState, IDownload, IPreview, IObjectContext {
    
    /**
     * Return base type name
     * 
     * @param boolean $singular
     * @return string
     */
    function getBaseTypeName($singular = true) {
      return $singular ? 'attachment' : 'attachments';
    } // getBaseTypeName
    
    /**
     * Send to browser
     *
     * @param boolean $force
     * @param boolean $die
     */
    function send($force = false, $die = false) {
      download_file($this->getFilePath(), $this->getMimeType(), $this->getName(), $force, $die);
    } // send
    
    /**
     * Cached file path value
     *
     * @var string
     */
    private $file_path = false;
    
    /**
     * Return full path to the file on disk
     * 
     * @return string
     */
    function getFilePath() {
      if($this->file_path === false) {
        $this->file_path = UPLOAD_PATH . '/' . $this->getLocation();
      } // if
      return $this->file_path;
    } // getFilePath
    
    /**
     * Return MD5 hash of this file version
     */
    function getMd5() {
      $md5 = parent::getMd5();
      
      if(empty($md5)) {
        $path = $this->download()->getPath();
        if(is_file($path)) {
          $md5 = md5_file($path);
          
          if($md5 && $this->isLoaded()) {
            DB::execute('UPDATE ' . TABLE_PREFIX . 'attachments SET md5 = ? WHERE id = ?', $md5, $this->getId());
            AngieApplication::cache()->removeByObject($this);
          } // if
        } // if
      } // if
      
      return $md5;
    } // getMd5
    
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
        'text' => lang('Download'),
        'url' => $this->getViewUrl(true),
      	'icon' => AngieApplication::getImageUrl('icons/12x12/download.png', ENVIRONMENT_FRAMEWORK),
      ));
      
      if($this->state()->canTrash($user)) {
        $options->add('trash', array(
          'text' => lang('Trash'),
          'url' => $this->state()->getTrashUrl(), 
          'icon' => AngieApplication::getImageUrl('/icons/12x12/delete.png', ENVIRONMENT_FRAMEWORK), 
        ));
      } // if
      
      EventsManager::trigger('on_attachment_options', array(&$this, &$user, &$options));
    } // prepareOptions

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = array(
        'id'        => $this->getId(),
        'name'      => $this->getName(),
        'mime_type' => $this->getMimeType(),
        'size'      => $this->getSize(),
        'md5'       => $this->getMd5(),
        'permalink' => $this->getViewUrl(),
        'thumbnail' => $this->preview()->getThumbnailUrl()
      );

      $this->state()->describeForApi($user, $detailed, $result);

      return $result;
    } // describeForApi
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      if($this->getParent() instanceof IAttachments) {
        return $this->getParent()->getObjectContextDomain();
      } else {
        return 'temp-attachments';
      } // if
    } // getContextDomain
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      if($this->getParent() instanceof IAttachments) {
        return $this->getParent()->getObjectContextPath() . '/attachments/' . $this->getId();
      } else {
        return 'temp-attachments:attachments/' . $this->getId();
      } // if
    } // getContextPath
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Routing context name
     *
     * @var string
     */
    private $routing_context = false;
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      if($this->routing_context === false) {
        $this->routing_context = $this->getParent() ? $this->getParent()->getRoutingContext() . '_attachment' : 'attachment';
      } // if
      
      return $this->routing_context;
    } // getRoutingContext
    
    /**
     * Routing context parameters
     *
     * @var array
     */
    private $routing_context_params = false;
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      if($this->routing_context_params === false) {
        $this->routing_context_params = $this->getParent() && is_array($this->getParent()->getRoutingContextParams()) ? array_merge($this->getParent()->getRoutingContextParams(), array('attachment_id' => $this->getId())) : array('attachment_id' => $this->getId());
      } // if
      
      return $this->routing_context_params;
    } // foreach
    
    /**
     * Created by implementation instance
     *
     * @var ICreatedByImplementation
     */
    private $created_by = false;
    
    /**
     * Return created by implementation instance
     *
     * @return ICreatedByImplementation
     */
    function createdBy() {
      if($this->created_by === false) {
        $this->created_by = new ICreatedByImplementation($this);
      } // if
      
      return $this->created_by;
    } // createdBy
    
    /**
     * Cached state helper instance
     *
     * @var IStateImplementation
     */
    private $state = false;
    
    /**
     * Return state helper
     *
     * @return IStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new IStateImplementation($this);
      } // if
      
      return $this->state;
    } // state
    
    /**
     * Download helper instance
     *
     * @var IAttachmentDownloadImplementation
     */
    private $download = false;
    
    /**
     * Returns download helper instance
     *
     * @return IAttachmentDownloadImplementation
     */
    function download() {
      if($this->download === false) {
        $this->download = new IAttachmentDownloadImplementation($this);
      } // if
      
      return $this->download;
    } // download
    
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
        $this->preview = new IDownloadPreviewImplementation($this);
      } // if
      
      return $this->preview;
    } // preview
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return download attachment URL
     *
     * @param integer $force
     * @return string
     */
    function getViewUrl($force = null) {
      return $this->download()->getDownloadUrl($force);
    } // getViewUrl
    
    /**
     * Return public view url
     * 
     * @param boolean $force
     * @return string
     */
    function getPublicViewUrl($force = false) {
      return AngieApplication::getProxyUrl("download_attachment", ATTACHMENTS_FRAMEWORK_INJECT_INTO, array(
      	'id' => $this->getId(),
      	'name' => $this->getName(),
      	'size' => $this->getSize(),
      	'timestamp'	=> $this->getCreatedOn()->getTimestamp(),
 				'md5' => $this->getMd5(),
        'force' => $force
      ));
    } // getPublicViewUrl
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can access this attachment
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
    	return $this->getParent() instanceof IAttachments && $this->getParent()->canView($user);
    } // canView
    
    /**
     * Returns true if $user can update this comment
     * 
     * Only administrator and comment author in given timeframe can update 
     * comment text
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $this->getParent() instanceof IAttachments && $this->getParent()->canEdit($user);
    } // canEdit
    
    /**
     * Returns true if $user can delete this comment
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $this->canEdit($user);
    } // canDelete
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('File name is required'), 'name');
      } // if
    } // validate
    
    /**
     * Save to database
     */
    function save() {
      $affects_count = $this->isNew() || $this->isModifiedField('state');
      
      parent::save();
      
      if($affects_count && $this->getParent() instanceof IAttachments) {
        AngieApplication::cache()->removeByObject($this->getParent(), 'attachments_count');
      } // if
    } // save
    
  }