<?php

  /**
   * Framework level comment implementation
   *
   * @package angie.frameworks.comments
   * @subpackage models
   */
  abstract class FwComment extends BaseComment implements IRoutingContext, ICreatedBy, IAttachments, IActivityLogs, IHistory, IState, IObjectContext {
    
    // List of available comment sources
    const SOURCE_WEB = 'web';
    const SOURCE_EMAIL = 'email';
    const SOURCE_API = 'api';
    
    /**
     * List of rich text fields
     *
     * @var array
     */
    protected $rich_text_fields = array('body');
    
    /**
     * Return comment name
     *
     * @return string
     */
    function getName() {
      return $this->getParent() instanceof IComments ? 
        lang('Comment on :name', array('name' => $this->getParent()->getName()), false) : 
        lang('Comment');
    } // getName
    
    /**
     * Return base type name
     * 
     * @param boolean $singular
     * @return string
     */
    function getBaseTypeName($singular = true) {
      return $singular ? 'comment' : 'comments';
    } // getBaseTypeName
    
    /**
     * Return proper type name in user's language
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('comment', $language) : lang('Comment', $language);
    } // getVerboseType

    /**
     * Check if comment can be viewed
     *
     * @return bool
     */
    function isAccessible() {
      if (!$this->isLoaded()) {
        return false;
      } // if

      if ($this->getState() < STATE_TRASHED) {
        return false;
      } // if

      if ($this->getParent() instanceof IState) {
        return $this->getParent()->getState() == $this->getState(); // if parent and comment's state don't match, we can't see the comment
      } else {
        return $this->getState() == STATE_VISIBLE; // if parent doesn't use state, then we can see only 'visible' comments
      } // if
    } // isAccessible
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($this->canEdit($user)) {
        $options->add('edit', array(
        	'text' => lang('Edit'),
          'url' => $this->getEditUrl(),
        	'icon' => AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK),  
        ));
      } // if
      
      if($this->state()->canTrash($user)) {
        $options->add('trash', array(
          'text' => lang('Trash'),
          'url' => $this->state()->getTrashUrl(), 
          'icon' => AngieApplication::getImageUrl('/icons/12x12/delete.png', ENVIRONMENT_FRAMEWORK), 
        ));
      } // if
      
      EventsManager::trigger('on_comment_options', array(&$this, &$user, &$options));
    } // prepareOptions
    
    /**
     * If true email notification on comment creation will be sent
     *
     * @var boolean
     */
    var $send_notification = true;
    
    /**
     * Return comment subsribers (uses parents subscribers)
     *
     * @return array
     */
    function getSubscribers() {
      return $this->getParent() instanceof ISubscriptions ? $this->getParent()->subscriptions()->get() : array();
    } // getSubscribers

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describeForApi($user, $detailed);

      unset($result['name']); // not needed

      return $result;
    } // describeForApi

    /**
     * Allow subclasses to include extra details in additional brief describe for API results
     *
     * @param $what
     * @return bool
     */
    function additionallyDescribeInBriefApiResponse($what) {
      return in_array($what, array('basic_urls', 'basic_permissions', 'body', 'state', 'attachments'));
    } // additionallyDescribeInBriefApiResponse
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      return $this->getParent()->getObjectContextDomain();
    } // getContextDomain
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return $this->getParent()->getObjectContextPath() . '/comments/' . $this->getId();
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
        $this->routing_context = $this->getParent()->getRoutingContext() . '_comment';
      } // if
      
      return $this->routing_context;
    } // getRoutingContext
    
    /**
     * Routing context parameters
     *
     * @var mixed
     */
    private $routing_context_params = false;
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      if($this->routing_context_params === false) {
        $this->routing_context_params = is_array($this->getParent()->getRoutingContextParams()) ? array_merge($this->getParent()->getRoutingContextParams(), array('comment_id' => $this->getId())) : array('comment_id' => $this->getId());
      } // if
      
      return $this->routing_context_params;
    } // getRoutingContextParams
    
    /**
     * Created by implementation instance
     *
     * @var ICreatedByImplementation
     */
    private $created_by;
    
    /**
     * Return created by implementation instance
     *
     * @return ICreatedByImplementation
     */
    function createdBy() {
      if(empty($this->created_by)) {
        $this->created_by = new ICreatedByImplementation($this);
      } // if
      
      return $this->created_by;
    } // createdBy
    
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
     * Cached instance of activity logs implementation
     *
     * @var ICommentActivityLogsImplementation
     */
    private $activity_logs = false;
    
    /**
     * Return activity logs implementation
     *
     * @return ICommentActivityLogsImplementation
     */
    function activityLogs() {
      if($this->activity_logs === false) {
        $this->activity_logs = new ICommentActivityLogsImplementation($this);
      } // if
      
      return $this->activity_logs;
    } // activityLogs
    
    /**
     * History helper instance
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
        $this->history = new IHistoryImplementation($this);
      } // if
      
      return $this->history;
    } // history
    
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
    	return $this->getParent() instanceof IComments && $this->getParent()->canView($user);
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
      if($this->getState() <= STATE_TRASHED) {
        return false; // Can't update deleted comments
      } // if

      if($user->isAdministrator()) {
        return true;
      } elseif($user->getId() == $this->getCreatedById()) {
        return ($this->getCreatedOn()->getTimestamp() + 1800) > DateTimeValue::now()->getTimestamp();
      } else {
        return false;
      } // if
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
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return real view URL
     *
     * @param string
     */
    function getRealViewUrl() {
      return $this->getParent() instanceof IComments ? $this->getParent()->getViewUrl() : '#';
    } // getRealViewUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!HTML::validateHTML($this->getBody(), 1)) {
        $errors->addError(lang('Minimum content length is 1 characters'), 'body');
      } // if
      
      if(!$this->validatePresenceOf('created_by_name')) {
        $errors->addError(lang('Author name is required'), 'created_by_name');
      } // if
      
      if($this->validatePresenceOf('created_by_email')) {
        if(!is_valid_email($this->getCreatedByEmail())) {
          $errors->addError(lang('Authors email address is not valid'), 'created_by_email');
        } // if
      } else {
        $errors->addError(lang('Authors email address is required'), 'created_by_email');
      } // if
    } // validate
    
    /**
     * Save comment into database
     *
     * @return boolean
     * @throws Exception
     */
    function save() {
      $count_cache_affected = $this->isNew() || $this->isModifiedField('state');
      $search_index_affected = $this->isNew() || $this->isModifiedField('state') || $this->isModifiedField('body');
      
      if($this->isNew()) {
        $event_name = 'on_comment_created';
      } else {
        $event_name = 'on_comment_updated';
      } // if
      
      try {
        DB::beginWork('Save comment @ ' . __CLASS__);

        parent::save();

        $parent = $this->getParent();
        EventsManager::trigger($event_name, array(&$this, &$parent));

	      // subscribe mentioned users to parent
	      $mentioned_user_ids = $this->getNewMentions();
	      if ($parent instanceof ISubscriptions && is_foreachable($mentioned_user_ids)) {
		     foreach ($mentioned_user_ids as $mentioned_user_id) {
			     $mentioned_user = Users::findById($mentioned_user_id);
			     if ($mentioned_user instanceof User) {
				     $parent->subscriptions()->subscribe($mentioned_user);
			     } // if
		     } // foreach
	      } // if

        if($search_index_affected && $parent instanceof ISearchItem) {
          $parent->search()->create();
        } // if
        
        if($count_cache_affected && $parent instanceof IComments) {
          AngieApplication::cache()->removeByObject($parent, 'comments_count');
        } // if
        
        DB::commit('Comment saved @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to save comment @ ' . __CLASS__);
        throw $e;
      } // try
      
      return true;
    } // save
    
    /**
     * Remove comment from database
     *
     * @return boolean
     * @throws Exception
     */
    function delete() {
      try {
        DB::beginWork('Deleting comment @ ' . __CLASS__);
        
        $parent = $this->getParent();
        
        parent::delete();
        ActivityLogs::deleteByParentAndAdditionalProperty($parent, 'comment_id', $this->getId());
        
        EventsManager::trigger('on_comment_deleted', array(&$this, &$parent));
        
        DB::commit('Comment deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete comment @ ' . __CLASS__);
        throw $e;
      } // try
      
      return true;
    } // delete
    
  }