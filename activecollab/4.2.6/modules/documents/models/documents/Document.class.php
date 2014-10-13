<?php

  /**
   * Document class
   *
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class Document extends BaseDocument implements IAttachments, ICategory, IState, IVisibility, IRoutingContext, ISearchItem, IObjectContext, ICanBeFavorite, IDownload, IPreview, IHistory, IActivityLogs, ISubscriptions {
    
    // Document type
    const TEXT = 'text';
    const FILE = 'file';
    
    /**
     * List of rich text fields
     *
     * @var array
     */
    protected $rich_text_fields = array('body');
    
    /**
     * Cached file path value
     *
     * @var string
     */
    protected $file_path = false;
    
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
     * Return file size
     *
     * @return integer
     */
    function getSize() {
    	return $this->getType() == 'file' ? parent::getSize() : 0;
    } // getSize
    
    /**
     * Return first letter of the file name
     */
    function getFirstLetter() {
      return Inflector::transliterate(strtolower_utf(substr_utf($this->getName(), 0, 1)));
    } //getFirstLetter
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      parent::prepareOptionsFor($user, $options, $interface);

      if($this->canPinUnpin($user)) {
        $options->add('pin_unpin', array(
          'text' => 'Pin/Unpin',
          'url' => '#',
          'icon' => AngieApplication::getImageUrl(($this->getIsPinned() ? 'icons/12x12/unpin.png' : 'icons/12x12/pin.png'), ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT),
          'important' => true,
          'onclick' => new AsyncTogglerCallback(array(
            'text' => lang('Unpin'),
            'url' => $this->getUnpinUrl(),
            'success_message' => lang('Document has been successfully unpinned'),
            'success_event' => 'document_updated',
          ), array(
            'text' => lang('Pin'),
            'url' => $this->getPinUrl(),
            'success_message' => lang('Document has been successfully pinned'),
            'success_event' => 'document_updated',
          ), $this->getIsPinned()),
        ));
      } // if

      if ($this->getType() == 'file') {
        $options->add('download', array(
          'text'      => lang('Download'),
          'url'       => $this->getDownloadUrl(),
          'icon'      => '',
          'important' => true,
          'onclick'   => new TargetBlankCallback()
        ));
      } // if
    } // prepareOptionsFor
    
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

      $result['body'] = $this->getBody();
      $result['first_letter'] = $this->getFirstLetter();
      $result['is_archived'] = $this->getState() == STATE_ARCHIVED ? 1 : 0;
      $result['is_pinned'] = $this->getIsPinned() ? 1 : 0;

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
      $result = parent::describeForApi($user, $detailed);

      $result['body'] = $this->getBody();
      $result['first_letter'] = $this->getFirstLetter();
      $result['is_archived'] = $this->getState() == STATE_ARCHIVED ? 1 : 0;
      $result['is_pinned'] = $this->getIsPinned() ? 1 : 0;

      return $result;
    } // describeForApi
    
    // ---------------------------------------------------
    //  Interface implementation
    // ---------------------------------------------------

    /**
     * Cached attachment implementation instance
     *
     * @var IAttachmentsImplementation
     */
    private $attachments;

    /**
     * Return attachments implementation instance for this object
     *
     * @return IAttachmentsImplementation
     */
    function &attachments() {
      if(empty($this->attachments)) {
        $this->attachments = new IAttachmentsImplementation($this);
      } // if

      return $this->attachments;
    } // attachments

    /**
     * Subscriptions helper instance
     *
     * @var IProjectObjectSubscriptionsImplementation
     */
    private $subscriptions;

    /**
     * Return subscriptions helper for this object
     *
     * @return ISubscriptionsImplementation
     */
    function &subscriptions() {
      if(empty($this->subscriptions)) {
        $this->subscriptions = new IDocumentsSubscriptionsImplementation($this);
      } // if

      return $this->subscriptions;
    } // subscriptions

    /**
     * Cached access log helper instance
     *
     * @var IActivityLogsImplementation
     */
    private $activity_logs = false;

    /**
     * Return activity logs helper instance
     *
     * @return IActivityLogsImplementation
     */
    function activityLogs() {
      if($this->activity_logs === false) {
        $this->activity_logs = new IActivityLogsImplementation($this);
      } // if

      return $this->activity_logs;
    } // activityLogs
    
    /**
     * Category implementation instance
     *
     * @var IDocumentCategoryImplementation
     */
    private $category = false;
    
    /**
     * Return category implementation
     *
     * @return IDocumentCategoryImplementation
     */
    function category() {
      if($this->category === false) {
        $this->category = new IDocumentCategoryImplementation($this);
      } // if
      
      return $this->category;
    } // category
    
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
     * Cached visibility helper
     *
     * @var IVisibilityImplementation
     */
    private $visibility = false;
    
    /**
     * Return visibility helper
     *
     * @return IVisibilityImplementation
     */
    function visibility() {
      if($this->visibility === false) {
        $this->visibility = new IVisibilityImplementation($this);
      } // if
      
      return $this->visibility;
    } // visibility
    
    /**
     * Search helper instance
     *
     * @var IDocumentSearchItemImplementation
     */
    private $search = false;
    
    /**
     * Return search helper instance
     * 
     * @return IDocumentSearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new IDocumentSearchItemImplementation($this);
      } // if
      
      return $this->search;
    } // search
    
    /**
     * Download helper instance
     *
     * @var IDownloadImplementation
     */
    private $download = false;
    
    /**
     * Returns download helper instance
     *
     * @return IDownloadImplementation
     */
    function download() {
      if($this->download === false) {
        $this->download = new IDownloadImplementation($this);
      } // if
      
      return $this->download;
    } // download
    
    /**
     * Preview helper instance
     *
     * @var IDocumentPreviewImplementation
     */
    protected $preview = false;
    
    /**
     * Return preview helper
     *
     * @return IDocumentPreviewImplementation
     */
    function preview() {
      if($this->preview === false) {
        $this->preview = new IDocumentPreviewImplementation($this);
      } // if
      
      return $this->preview;
    } // preview
    
    /**
     * Return object context domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      return 'documents';
    } // getObjectContextDomain
    
    /**
     * Return object context path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return 'documents/' . ($this->getVisibility() == VISIBILITY_PRIVATE ? 'private' : 'normal') . '/' . $this->getId();
    } // getObjectContextPath
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'document';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('document_id' => $this->getId());
    } // getRoutingContextParams
    
    /**
     * Return email notification context ID
     *
     * @return string
     */
    function getNotifierContextId() {
      return 'DOCUMENT/' . $this->getId();
    } // getNotifierContextId
    
    /**
     * Cached history implementation instance
     *
     * @var IHistoryImplementation
     */
    private $history = false;
    
    /**
     * Return history helper instance
     * 
     * @return IHistoryImplementation
     */
    function history() {
      if($this->history === false) {
        $this->history = new IHistoryImplementation($this, array('is_pinned'));
      } // if
      
      return $this->history;
    } // history
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can view specific document
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $user->isAdministrator() || (Documents::canUse($user) && $user->getMinVisibility() <= $this->getVisibility());
    } // canView

    /**
     * Returns true if $user can edit this document
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->getId() == $this->getCreatedById() || Documents::canManage($user);
    } // canEdit

    /**
     * Returns true if $user can delete this document
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->getId() == $this->getCreatedById() || Documents::canManage($user);
    } // canDelete
    
    /**
     * Returns true if $user can pin/unpin this document
     *
     * @param User $user
     * @return boolean
     */
    function canPinUnpin(User $user) {
    	return Documents::canManage($user);
    } // canPin

    /**
     * Returns true if $user can change visibility of the document
     *
     * @param User $user
     * @return boolean
     */
    function canChangeVisibility(User $user) {
      return (Documents::canManage($user) || $user->getId() == $this->getCreatedById()) && $user->canSeePrivate();
    } // canChangeVisibility
        
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return download file url
     *
     * @param boolean $force
     * @return string
     */
    function getDownloadUrl($force = false) {
    	if ($this->getType() == 'file') {
        $params = array(
          'document_id' => $this->getId(),
        );

        if ($force) {
          $params['disposition'] = 'attachment';
          $params['force'] = 1;
        } // if

    		return Router::assemble('document_download', $params);
    	} else {
    		return $this->getViewUrl();
    	} // if
    } // getDownloadUrl
    
    /**
     * Return file document URL
     *
     * @return string
     */
    function getPreviewUrl() {
      return $this->preview()->renderLargePreview();
    } // getPreviewUrl
    
    /**
     * Return edit document URL
     *
     * @return string
     */
    function getEditUrl() {
    	return Router::assemble('document_edit', array(
    		'document_id' => $this->getId(),
    	));
    } // getEditUrl
    
    /**
     * Return delete document URL
     *
     * @return string
     */
    function getDeleteUrl() {
    	return Router::assemble('document_delete', array(
    		'document_id' => $this->getId(),
    	));
    } // getDeleteUrl
    
    /**
     * Return pin document URL
     *
     * @return string
     */
    function getPinUrl() {
    	return Router::assemble('document_pin', array(
        'document_id' => $this->getId(),
    	));
    } // getPinUrl
    
    /**
     * Return unpin document URL
     *
     * @return string
     */
    function getUnpinUrl() {
    	return Router::assemble('document_unpin', array(
        'document_id' => $this->getId(),
    	));
    } // getUnpinUrl
    
    /**
     * Return thumbnail URL
     *
     * @return string
     */
    function getThumbnailUrl() {
      return AngieApplication::getFileIconUrl($this->getName(), '48x48');
    } // getThumbnailUrl
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Return MD5 value
     * 
     * @return string
     */
    function getMd5() {
      $md5 = $this->getMd5();
      
      if(empty($md5)) {
        $path = $this->download()->getPath();
        if(is_file($path)) {
          $md5 = md5_file($path);
          
          if($md5 && $this->isLoaded()) {
            DB::execute('UPDATE ' . TABLE_PREFIX . 'documents SET md5 = ? WHERE id = ?', $md5, $this->getId());
          } // if
        } // if
      } // if
      
      return $md5;
    } // getMd5
    
    /**
     * Set field value
     *
     * If we are setting body purifier will be included and value will be ran
     * through it. Else we will simply inherit behavior
     *
     * @param string $field
     * @param mixed $value
     * @return string
     */
    function setFieldValue($field, $value) {
      if(!$this->isLoading() && $this->getType() == 'text' && ($field == 'body')) {
        $value = HTML::cleanUpHtml($value);
      } // if
      
      return parent::setFieldValue($field, $value);
    } // setFieldValue
    
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
    		$errors->addError(lang('Document name is required'), 'name');
    	} // if
    	
    	parent::validate($errors, true);
    } // validate
    
    /**
     * Delete document
     */
    function delete() {
      $filepath = $this->getFilePath();
      
      try {
        DB::beginWork('Delete document @ ' . __CLASS__);
        
        parent::delete();
        Attachments::deleteByParent($this);
        
        DB::commit('Document deleted @ ' . __CLASS__);
        
        if (is_file($filepath)) {
          @unlink($filepath);
        } // if
      } catch(Exception $e) {
        DB::rollback('Failed to delete document @ ' . __CLASS__);
        
        throw $e;
      } // try
      
      return true;
    } // delete
  
  }