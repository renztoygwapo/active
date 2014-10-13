<?php

  /**
   * Discussion class
   *
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class Discussion extends ProjectObject implements IComments, ICategory, ISubscriptions, IAttachments, ISharing, ISearchItem, ICanBeFavorite {
    
    /**
     * Permission name
     * 
     * @var string
     */
    protected $permission_name = 'discussion';
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    protected $fields = array(
      'id', 
      'type', 'source', 'module', 
      'project_id', 'milestone_id', 'category_id', 
      'name', 'body',
      'state', 'original_state', 'visibility', 'original_visibility', 'is_locked', 
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email', 
      'datetime_field_1', // cached value of last comment date...
      'boolean_field_1', // flag that indicates whether this discussion is pinned or not
      'integer_field_1', // cached value of last comment author's ID
      'version',
    );
    
    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'last_comment_on' => 'datetime_field_1',
      'is_pinned' => 'boolean_field_1',
    );
    
    /**
     * Construct a new discussion
     *
     * @param mixed $id
     * @return Discussion
     */
    function __construct($id = null) {
      $this->setModule(DISCUSSIONS_MODULE);
      parent::__construct($id);
    } // __construct
    
    /**
     * Cached inspector instance
     * 
     * @var IDiscussionInspectorImplementation
     */
    private $inspector = false;
    
    /**
     * Return inspector helper instance
     * 
     * @return IDiscussionInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new IDiscussionInspectorImplementation($this);
      } // if
      
      return $this->inspector;
    } // inspector

    /**
     * Return verbose type name
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('discussion', null, true, $language) : lang('Discussion', null, true, $language);
    } // getVerboseType
    
    /**
     * Check if object has meaningful body
     * 
		 * return boolean
     */
    function hasBody() {
    	$body = $this->getBody();
      if($body) {
      	return (boolean) trim(strip_tags(str_replace(array("\n", "\r", "\n\r", "\r\n",), '', $body)));
      } else {
        return false;
      } // if
    } // hasBody
    
    /**
     * Return category name
     * 
     * @return string 
     */
    function getCategoryName() {
      return $this->category()->get() instanceof DiscussionCategory ? $this->category()->get()->getName() : lang('Uncategorized');
    } // getCategoryName
    
    /**
     * Return milestone name
     * 
     * @return string
     */
    function getMilestoneName() {
      return $this->getMilestone() instanceof Milestone ? $this->getMilestone()->getName() : lang('Uncategorized');
    } // getCategoryName

    /**
     * Cached value of "is read" state for discussion
     *
     * @var null|bool
     */
    private $is_read = null;

    /**
     * Returns true if this comment is read by $user
     * 
     * Returns true if $user viewed this discussion since last comment was 
     * posted or if last comment is posted more than 30 days ago
     * 
     * @param User $user
     * @return boolean
     */
    function isRead($user) {
      if (is_null($this->is_read)) {
        $this->is_read = Discussions::isRead($this, $user);
      } // if

      return $this->is_read;
    } // isRead

    /**
     * Override 'is read' status
     *
     * @param bool $is_read
     */
    function setIsRead($is_read = true) {
      $this->is_read = (boolean) $is_read;
    } // setIsRead
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($this->canPinUnpin($user)) {
      	if($interface == AngieApplication::INTERFACE_DEFAULT) {
	      	$options->add('pin_unpin', array(
		        'text' => 'Pin/Unpin', 
		        'url' => '#', 
	          'icon' => AngieApplication::getImageUrl(($this->getIsPinned() ? 'icons/12x12/unpin.png' : 'icons/12x12/pin.png'), ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT),
	        	'important' => true,
		        'onclick' => new AsyncTogglerCallback(array(
		          'text' => lang('Unpin'), 
		          'url' => $this->getUnpinUrl(), 
		          'success_message' => lang('Discussion has been successfully unpinned'),
		          'success_event' => 'discussion_updated',
		        ), array(
		          'text' => lang('Pin'), 
		          'url' => $this->getPinUrl(),
	
		          'success_message' => lang('Discussion has been successfully pinned'),
		          'success_event' => 'discussion_updated',
		        ), $this->getIsPinned()),
		      ));
	      } elseif($interface == AngieApplication::INTERFACE_PHONE) {
	      	$options->add('pin_unpin', array(
		        'text' => 'Pin/Unpin',
		        'url' => $this->getIsPinned() ? $this->getUnpinUrl() : $this->getPinUrl(),
	          'icon' => AngieApplication::getImageUrl(($this->getIsPinned() ? 'icons/navbar/unpin.png' : 'icons/navbar/pin.png'), ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_PHONE),
		      ));
	      } // if
      } // if
            
      parent::prepareOptionsFor($user, $options, $interface);
      
      // mark archive as important
      if ($options->exists('archive')) {
      	$archive = $options->get('archive');
      	$archive['important'] = true;
      	$options->add('archive', $archive);
      } // if
    } // prepareOptionsFor
    
    /**
     * Return discussion icon URL
     *
     * @param User $user
     * @return string
     */
    function getIconUrl($user) {
    	return Discussions::getIconUrl($this->getIsPinned(), $this->isRead($user));
    } // getIconUrl
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return parent::getObjectContextPath() . '/discussions/' . ($this->getVisibility() == VISIBILITY_PRIVATE ? 'private' : 'normal') . '/' . $this->getId();
    } // getContextPath
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Comments implementation helper
     *
     * @var IDiscussionCommentsImplementation
     */
    private $comments = false;
    
    /**
     * Return comments implementation
     * 
     * @return IDiscussionCommentsImplementation
     */
    function &comments() {
      if($this->comments === false) {
        $this->comments = new IDiscussionCommentsImplementation($this);
      } // if
      
      return $this->comments;
    } // comments
    
    /**
     * Category implementation instance
     *
     * @var IDiscussionCategoryImplementation
     */
    private $category = false;
    
    /**
     * Return category implementation
     *
     * @return IDiscussionCategoryImplementation
     */
    function category() {
      if($this->category === false) {
        $this->category = new IDiscussionCategoryImplementation($this);
      } // if
      
      return $this->category;
    } // category
    
    /**
     * Cached attachment manager instance
     *
     * @var IAttachmentsImplementation
     */
    private $attachments;
    
    /**
     * Return attachments manager instance for this object
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
    private $subscriptions = false;
    
    /**
     * Return subscriptions helper for this object
     *
     * @return ISubscriptionsImplementation
     */
    function &subscriptions() {
      if($this->subscriptions === false) {
        $this->subscriptions = new IProjectObjectSubscriptionsImplementation($this);
      } // if
      
      return $this->subscriptions;
    } // subscriptions
    
    /**
     * Return history helper instance
     *
     * @return IHistoryImplementation
     */
    function history() {
      return parent::history()->alsoTrackFields('boolean_field_1');
    } // history
    
    /**
     * Sharing helper instance
     *
     * @var IDiscussionSharingImplementation
     */
    private $sharing = false;
    
    /**
     * Return sharing helper instance
     * 
     * @return IDiscussionSharingImplementation
     */
    function sharing() {
      if($this->sharing === false) {
        $this->sharing = new IDiscussionSharingImplementation($this);
      } // if
      
      return $this->sharing;
    } // sharing
    
    /**
     * Cached search helper instance
     *
     * @var IDiscussionSearchItemImplementation
     */
    private $search = false;
    
    /**
     * Return search helper instance
     * 
     * @return IDiscussionSearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new IDiscussionSearchItemImplementation($this);
      } // if
      
      return $this->search;
    } // search
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view discussion URL
     *
     * @param integer $page
     * @return string
     */
    function getViewUrl($page = null) {
      $params = $page === null ? null : array('page' => $page);
      return parent::getViewUrl($params);
    } // getViewUrl
    
    /**
     * Return portal view discussion URL
     *
     * @param Portal $portal
     * @param integer $page
     * @return string
     */
    function getPortalViewUrl($portal, $page = null) {
    	$params = $page === null ? null : array('page' => $page);
    	return parent::getPortalViewUrl($portal, $params);
    } // getPortalViewUrl
    
    /**
     * Return pin discussion url
     *
     * @return string
     */
    function getPinUrl() {
      return Router::assemble('project_discussion_pin', array(
        'project_slug' => $this->getProject()->getSlug(),
        'discussion_id' => $this->getId(),
      ));
    } // getPinUrl
    
    /**
     * Return unpin discussion url
     *
     * @return string
     */
    function getUnpinUrl() {
      return Router::assemble('project_discussion_unpin', array(
        'project_slug' => $this->getProject()->getSlug(),
        'discussion_id' => $this->getId(),
      ));
    } // getPinUrl
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get last_comment_on
     *
     * @return DateTimeValue
     */
    function getLastCommentOn() {
      return $this->getDatetimeField1();
    } // getLastCommentOn
    
    /**
     * Set last_comment_on value
     *
     * @param DateTimeValue $value
     */
    function setLastCommentOn($value) {
      return $this->setDatetimeField1($value);
    } // setLastCommentOn

    /**
     * Set last_comment_by_id
     *
     * @return DatetTimeValue
     */
    function getLastCommentById() {
      return $this->getIntegerField1();
    } // getLastCommentById

    /**
     * Set last_commment_by_id value
     *
     * @param integer $value
     */
    function setLastCommentById($value) {
      return $this->setIntegerField1($value);
    } // setLastCommentById
    
    /**
     * Get is_pinned
     *
     * @return boolean
     */
    function getIsPinned() {
      return $this->getBooleanField1();
    } // getIsPinned
    
    /**
     * Set is_pinned value
     *
     * @param boolean $value
     */
    function setIsPinned($value) {
      return $this->setBooleanField1($value);
    } // setIsPinned
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can change pin state of discussion
     *
     * @param User $user
     * @param Project $project
     */
    function canPinUnpin($user) {
      return $this->canEdit($user);      
    } // canPinUnpin
    
    // ---------------------------------------------------
    //  System
    // --------------------------------------------------
    
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
      
      $result['is_pinned'] = $this->getIsPinned() ? 1 : 0;
      $result['is_read'] = $this->isRead($user) ? 1 : 0;
      $result['icon'] = Discussions::getIconUrl($this->getIsPinned(), $this->isRead($user));
      $result['last_comment_on'] = $this->getLastCommentOn();
      
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

      $result['is_pinned'] = $this->getIsPinned() ? 1 : 0;
      $result['is_read'] = $this->isRead($user) ? 1 : 0;
      $result['last_comment_on'] = $this->getLastCommentOn();

      return $result;
    } // describeForApi
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Summary is required'), 'name');
      } // if
      
      if(!$this->validatePresenceOf('body')) {
        $errors->addError(lang('Message is required'), 'body');
      } // if
      
      parent::validate($errors, true);
    } // validate
    
    /**
     * Save this discussion into database
     *
     * @return boolean
     */
    function save() {
      if($this->isNew()) {
        $this->setIsPinned((boolean) $this->getIsPinned()); // Make sure we have 0 or 1 instead of NULL
        // $this->setLastCommentOn(new DateTimeValue());
      } // if
      
      return parent::save();
    } // save
    
  }