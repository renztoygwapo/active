<?php

  /**
   * Foundation implementation of all application objects
   *
   * @package angie.frameworks.envrionment
   * @subpackage models
   */
  abstract class FwApplicationObject extends DataObject implements IDescribe, IJSON, IInspector {
    
    /**
     * Return name
     *
     * @return string
     */
    function getName() {
      return '-- unknown --';
    } // getName
    
    // ---------------------------------------------------
    //  Type & Context
    // ---------------------------------------------------
    
    /**
     * Return type name for this object
     *
     * @var string
     */
    private $type_name_singular = false;
    
    /**
     * Plural type name
     *
     * @var string
     */
    private $type_name_plural = false;
    
    /**
     * Return base type name
     * 
     * @param boolean $singular
     * @return string
     */
    function getBaseTypeName($singular = true) {
      if($singular) {
        if($this->type_name_singular === false) {
          $this->type_name_singular = Inflector::underscore(get_class($this));
        } // if
        
        return $this->type_name_singular;
      } else {
        if($this->type_name_plural === false) {
          $this->type_name_plural = Inflector::underscore(Inflector::pluralize(get_class($this)));
        } // if
        
        return $this->type_name_plural;
      } // if
    } // getBaseTypeName
    
    /**
     * Return proper type name in user's language
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? 
        lang(strtolower(get_class($this)), null, true, $language) : 
        lang(Inflector::humanize(strtolower(get_class($this))), null, true, $language);
    } // getVerboseType
    
    // ---------------------------------------------------
    //  Events
    // ---------------------------------------------------
    
    /**
     * Return event names prefix
     * 
     * @return string
     */
    function getEventNamesPrefix() {
    	return $this->getBaseTypeName();
    } // getEventNamesPrefix
    
    /**
     * Return created event name for this particular object
     * 
     * @return string
     */
    function getCreatedEventName() {
    	return $this->getEventNamesPrefix() . '_created';
    } // getCreatedEventName
    
    /**
     * Return name of the event that's triggered when object is updated
     * 
     * @return string
     */
    function getUpdatedEventName() {
    	return $this->getEventNamesPrefix() . '_updated';
    } // getUpdatedEventName
    
    /**
     * Return name of the event that's triggered when this object is deleted
     * 
     * @return string
     */
    function getDeletedEventName() {
    	return $this->getEventNamesPrefix() . '_deleted';
    } // getDeletedEventName
    
    // ---------------------------------------------------
    //  Parent
    // ---------------------------------------------------

    /**
     * Return comment parent
     *
     * @return ApplicationObject
     */
    function getParent() {
    	if($this->getParentType() && $this->getParentId()) {
    		return DataObjectPool::get($this->getParentType(), $this->getParentId());
    	} else {
    		return null;
    	} // if
    } // getParent
    
    /**
     * Set parent instance
     *
     * @param ApplicationObject $parent
     * @param boolean $save
     * @return ApplicationObject
     * @throws InvalidInstanceError
     */
    function setParent($parent, $save = false) {			
      if($parent instanceof ApplicationObject) {
        $this->setParentType(get_class($parent));
        $this->setParentId($parent->getId());
      } elseif($parent === null) {
        $this->setParentType(null);
        $this->setParentId(null);        
      } else {
        throw new InvalidInstanceError('parent', $parent, 'ApplicationObject');
      } // if
      
      if($save) {
        $this->save();
      } // if
      
      return $parent;
    } // setParent
    
    /**
     * Returns true if $parent is parent of this object
     *
     * @param ApplicationObject $parent
     * @return boolean
     */
    function isParent(ApplicationObject $parent) {
      return strtolower(get_class($parent)) == strtolower($this->getParentType()) && $parent->getId() == $this->getParentId();
    } // isParent
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Return primary option
     * 
     * If one option needs to be highlighted, what option should that be? This 
     * function returns array where first element is action name and second 
     * element is action itself
     * 
     * @param IUser $user
     * @param string $interface
     * @return array
     */
    function getPrimaryOption(IUser $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($this->canEdit($user)) {
      	$additional = null;
      	
        if($interface == AngieApplication::INTERFACE_PHONE) {
        	$additional = array(
	        	'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? '' : AngieApplication::getImageUrl('layout/buttons/edit.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
	        	'id' => 'wireframe_action_edit',
	        	'primary' => true
	        );
        } // if
        
        return array('edit', new WireframeAction(lang('Edit'), $this->getEditUrl(), $additional));
      } else {
        return array(null, null);
      } // if
    } // getPrimaryOption
    
    /**
     * Cached array of object options
     *
     * @var NamedList
     */
    private $options = array();
    
    /**
     * Return options for $user
     *
     * @param IUser $user
     * @param string $interface
     * @return NamedList
     * @throws InvalidInstanceError
     */
    function getOptions(IUser $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($user instanceof IUser) {
        if(!array_key_exists($user->getEmail(), $this->options)) {
          $options = new NamedList(); // Start with empty list
          $this->prepareOptionsFor($user, $options, $interface);
          $this->options[$user->getEmail()] = $options;
        } // if
        
        return $this->options[$user->getEmail()];
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
    } // getOptions
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      $valid_state = ($this instanceof IState) ? ($this->getState() == STATE_VISIBLE) : true;

      // Edit
      if(!($this instanceof IReadOnly) && $this->canEdit($user) && $valid_state) {
        $options->beginWith('edit', array(
          'url' => $this->getEditUrl(),
          'text' => lang('Edit'),
        	'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) : '',
        	'onclick' => new FlyoutFormCallback($this->getUpdatedEventName()),
        	'important' => true
        ), true);
      } // if
      
      // Avatar
      if($interface == AngieApplication::INTERFACE_DEFAULT && $this instanceof IAvatar && $this->canEdit($user)) {
      	$options->add('change_avatar', array(
      		'text' => lang('Update :avatar_label', array('avatar_label' => $this->avatar()->getAvatarLabelName())),
      		'url'	=> $this->avatar()->getViewUrl(),
      		'onclick' => new FlyoutCallback(),
      	), true);
      } // if
      
      // Complete
      if($this instanceof IComplete) {
      	$this->complete()->prepareObjectOptions($options, $user, $interface);
      } // if
      
      // Comments
      if($this instanceof IComments) {
        $this->comments()->prepareObjectOptions($options, $user, $interface);
      } // if

      // Change state
      if($this instanceof IState) {
        $this->state()->prepareObjectOptions($options, $user, $interface);
      } // if

      // Reminders
      if($this instanceof IReminders) {
      	$this->reminders()->prepareObjectOptions($options, $user, $interface);
      } // if
      
      // Subscriptions
      if($interface == AngieApplication::INTERFACE_DEFAULT && $this instanceof ISubscriptions) {
        if ($this->canEdit($user)) {
          $options->add('manage_subscriptions', array(
            'text' => $this instanceof IState && $this->getState() == STATE_VISIBLE ? lang('Manage Subscriptions') : lang('View Subscribers'),
            'url' => $this->subscriptions()->getSubscriptionsUrl(),
            'onclick' => new FlyoutFormCallback('subscriptions_managed', array('width' => 'narrow')),
          	'icon' => AngieApplication::getImageUrl('icons/12x12/manage-subscribers.png', SUBSCRIPTIONS_FRAMEWORK)
          ), true);
        } else {
          $options->add('subscribe_unsubscribe', array(
            'text' => 'Subscribe/Unsubscribe',
            'url' => '#',
            'onclick' => new AsyncTogglerCallback(array(
              'text' => lang('Unsubscribe'),
              'url' => $this->subscriptions()->getUnsubscribeUrl($user),
              'success_message' => lang('You have unsubscribed from this :type', array('type' => $this->getVerboseType())),
              'success_event' => $this->getUpdatedEventName(),
            ), array(
              'text' => lang('Subscribe'),
              'url' => $this->subscriptions()->getSubscribeUrl($user),
              'success_message' => lang('You have subscribed to this :type', array('type' => $this->getVerboseType())),
              'success_event' => $this->getUpdatedEventName(),
            ), $this->subscriptions()->isSubscribed($user))
          ));
        } // if
      } // if
      
      // Favorites
      if($this instanceof ICanBeFavorite) {
        $options->add('favorites_toggler', array(
          'text' => 'Add/Remove', 
          'url' => '#', 
          'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? '' : AngieApplication::getImageUrl('icons/navbar/pin.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
          'onclick' => new AsyncTogglerCallback(array(
            'text' => lang('Remove from Favorites'), 
            'url' => $user->favorites()->getRemoveFromFavoritesUrl($this),
            'success_message' => lang(':type has been removed from favorites', array('type' => $this->getVerboseType())),
            'success_event' => $this->getUpdatedEventName(),
          ), array(
          	'text' => lang('Add to Favorites'), 
            'url' => $user->favorites()->getAddToFavoritesUrl($this), 
            'success_message' => lang(':type has been added to favorites', array('type' => $this->getVerboseType())),
            'success_event' => $this->getUpdatedEventName(),
          ), Favorites::isFavorite($this, $user)), 
        ));
      } // if

      // Trigger event, so modules can extend or alter the list of options
      EventsManager::trigger('on_object_options', array(&$this, &$user, &$options, $interface));
      
      return $options->count() ? $options : null;
    } // prepareOptionsFor
    
    // ---------------------------------------------------
    //  Created / Updated By
    // ---------------------------------------------------
  
    /**
     * Return user who created this object
     *
     * @return IUser
     */
    function getCreatedBy() {
      return $this->getUserFromFieldSet('created_by');
    } // getCreatedBy
    
    /**
     * Set person who create this object
     *
     * $created_by can be an insance of User or AnonymousUser class or null
     *
     * @param mixed $created_by
     * @return mixed
     */
    function setCreatedBy($created_by) {
      return $this->setUserFromFieldSet($created_by, 'created_by');
    } // setCreatedBy
    
    /**
     * Returns true if this object is created by $user
     *
     * @param IUser $user
     * @return boolean
     */
    function isCreatedBy(IUser $user) {
      if($this->isLoaded() && $this->fieldExists('created_by_id')) {
        if($this->getCreatedById()) {
          return $this->getCreatedById() == $user->getId();
        } else {
          return $this->getCreatedById() == 0 && $this->getCreatedByEmail() == $user->getEmail();
        } // if
      } else {
        return false;
      } // if
    } // isCreatedBy
    
    /**
     * Return user who last updated this object
     *
     * @return IUser
     */
    function getUpdatedBy() {
      return $this->getUserFromFieldSet('updated_by');
    } // getUpdatedBy
    
    /**
     * Set person who updated this object
     *
     * $updated_by can be an insance of User or AnonymousUser class or null
     *
     * @param IUser|null $updated_by
     * @return IUser|null
     */
    function setUpdatedBy($updated_by) {
      return $this->setUserFromFieldSet($updated_by, 'updated_by');
    } // setUpdatedBy
    
    /**
     * Returns user instance (or NULL) for given field set
     * 
     * @param string $field_set_prefix
     * @return IUser
     */
    function getUserFromFieldSet($field_set_prefix) {
      $by_id = $this->getFieldValue("{$field_set_prefix}_id");
      $by_name = $this->getFieldValue("{$field_set_prefix}_name");
      $by_email = $this->getFieldValue("{$field_set_prefix}_email");
      
      return DataObjectPool::get('User', $by_id, function() use ($by_name, $by_email) {
        return $by_name && $by_email ? new AnonymousUser($by_name, $by_email) : null;
      });
    } // getUserFromFieldSet
    
    /**
     * Set by user for given field set
     * 
     * @param IUser $by_user
     * @param string $field_set_prefix
     * @param boolean $optional
     * @param boolean $can_be_anonymous
     * @return mixed
     * @throws InvalidInstanceError
     */
    function setUserFromFieldSet($by_user, $field_set_prefix, $optional = true, $can_be_anonymous = true) {
      if($by_user instanceof IUser) {
        if($by_user instanceof AnonymousUser && !$can_be_anonymous) {
          throw new InvalidInstanceError('by_user', $by_user, 'User');
        } // if
        
        $this->setFieldValue("{$field_set_prefix}_id", $by_user->getId());
        $this->setFieldValue("{$field_set_prefix}_email", $by_user->getEmail());
        $this->setFieldValue("{$field_set_prefix}_name", $by_user->getName());
      } elseif($by_user === null) {
        if($optional) {
          $this->setFieldValue("{$field_set_prefix}_id", 0);
          $this->setFieldValue("{$field_set_prefix}_email", '');
          $this->setFieldValue("{$field_set_prefix}_name", '');
        } else {
          throw new InvalidInstanceError('by_user', $by_user, 'IUser');
        } // if
      } else {
        throw new InvalidInstanceError('by_user', $by_user, 'IUser');
      } // if
      
      return $by_user;
    } // setByUser
    
    // ---------------------------------------------------
    //  Additional properties
    // ---------------------------------------------------
    
    /**
     * Cached log attributes value
     *
     * @var array
     */
    private $additional_properties = false;
    
    /**
     * Return additional log properties as array
     *
     * @return array
     * @throws NotImplementedError
     */
    function getAdditionalProperties() {
      if($this->fieldExists('raw_additional_properties')) {
        if($this->additional_properties === false) {
          $raw = trim($this->getFieldValue('raw_additional_properties'));
          $this->additional_properties = empty($raw) ? array() : unserialize($raw);
          
          if(!is_array($this->additional_properties)) {
            $this->additional_properties = array();
          } // if
        } // if
        
        return $this->additional_properties;
      } else {
        throw new NotImplementedError(__CLASS__ . '::' . __METHOD__);
      } // if
    } // getAdditionalProperties
    
    /**
     * Set attributes value
     *
     * @param mixed $value
     * @return mixed
     * @throws NotImplementedError
     */
    function setAdditionalProperties($value) {
      if($this->fieldExists('raw_additional_properties')) {
        $this->additional_properties = false; // Reset...
        
        if(empty($value)) {
          return $this->setFieldValue('raw_additional_properties', null);
        } else {
          $this->setFieldValue('raw_additional_properties', serialize($value));
          
          return $value;
        } // if
      } else {
        throw new NotImplementedError(__METHOD__);
      } // if
    } // setAdditionalProperties
    
    /**
     * Returna attribute value
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @throws NotImplementedError
     */
    function getAdditionalProperty($name, $default = null) {
      if($this->fieldExists('raw_additional_properties')) {
        $additional_properties = $this->getAdditionalProperties();

        return $additional_properties ? array_var($additional_properties, $name, $default) : $default;
      } else {
        throw new NotImplementedError(__METHOD__);
      } // if
    } // getAttribute
    
    /**
     * Set attribute value
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws NotImplementedError
     */
    function setAdditionalProperty($name, $value) {
      if($this->fieldExists('raw_additional_properties')) {
        $additional_properties = $this->getAdditionalProperties();
        $additional_properties[$name] = $value;
        
        $this->setAdditionalProperties($additional_properties);
        
        return $value;
      } else {
      	throw new NotImplementedError(__METHOD__);
      } // if
    } // setAdditionalProperty

    /**
     * Return array of new mentions
     *
     * @var array
     */
    private $new_mentions = null;

    /**
     * Return array of newly mentioned users
     */
    function getNewMentions() {
      return $this->new_mentions;
    } // getNewMentions

    /**
     * List of rich text fields
     *
     * @var array
     */
    protected $rich_text_fields = null;

    /**
     * Temporary storage for field related attachments
     *
     * @var array
     */
    protected $field_related_attachments = array();

    /**
     * Set field value
     *
     * @param string $field
     * @param mixed $value
     * @return mixed
     */
    function setFieldValue($field, $value) {
      if ($value && in_array($field, (array) $this->rich_text_fields) && !$this->isLoading()) {
        $newly_mentioned_users = array();

        $value = HTML::cleanUpHtml($value, function(simple_html_dom &$dom) use (&$newly_mentioned_users) {
          $elements = $dom->find('span.new_mention');

          if($elements) {
            foreach($elements as $element) {
              $user_id = (integer) array_var($element->attr, 'data-user-id', null);

              if($user_id && !in_array($user_id, $newly_mentioned_users)) {
                $newly_mentioned_users[] = $user_id;
              } // if

              $element->outertext = '<span class="mention">' . $element->innertext . '</span>';
            } // foreach
          } // if
        });

        $this->new_mentions = $newly_mentioned_users;
      } // if

      return parent::setFieldValue($field, $value);
    } // setFieldValue

    // ---------------------------------------------------
    //  Delegates
    // ---------------------------------------------------

    /**
     * Array where we'll keep delegate instances
     *
     * @var array
     */
    private $delegate_instances = array();

    /**
     * Return delegate instance for a given delegate
     *
     * @param string $delegate_name
     * @param Closure|string $delegate_instance_class
     * @param mixed $delegate_instance_params
     * @return mixed
     * @throws
     */
    protected function &getDelegateInstance($delegate_name, $delegate_instance_class, $delegate_instance_params = null) {
      if($delegate_name) {
        if(!isset($this->delegate_instances[$delegate_name])) {
          $class_name = $delegate_instance_class instanceof Closure ? $delegate_instance_class() : $delegate_instance_class;

          if($delegate_instance_params) {
            $this->delegate_instances[$delegate_name] = new $class_name($this, $delegate_instance_params);
          } else {
            $this->delegate_instances[$delegate_name] = new $class_name($this);
          } // if
        } // if

        return $this->delegate_instances[$delegate_name];
      } else {
        throw new InvalidParamError('delegate_name', $delegate_name);
      } // if
    } // getDelegateInstance
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can view this object
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return false;
    } // canView
    
    /**
     * Returns true if $user can update this object
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return false;
    } // canEdit
    
    /**
     * Returns true if $user can delete or move to trash this object
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $this instanceof IState ? $this->state()->canDelete($user) : false;
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view URL
     *
     * @return string
     * @throws NotImplementedError
     */
    function getViewUrl() {
      if($this instanceof IRoutingContext) {
        return Router::assemble($this->getRoutingContext(), $this->getRoutingContextParams());
      } else {
        throw new NotImplementedError(__METHOD__);
      } // if
    } // getViewUrl
    
    /**
     * Return edit object URL
     *
     * @return string
     * @throws NotImplementedError
     */
    function getEditUrl() {
      if(!($this instanceof IReadOnly) && $this instanceof IRoutingContext) {
        return Router::assemble($this->getRoutingContext() . '_edit', $this->getRoutingContextParams());
      } else {
        throw new NotImplementedError(__METHOD__);
      } // if
    } // getEditUrl
    
    /**
     * Return delete object URL
     *
     * @return string
     * @throws NotImplementedError
     */
    function getDeleteUrl() {
      if($this instanceof IRoutingContext) {
        return Router::assemble($this->getRoutingContext() . '_delete', $this->getRoutingContextParams());
      } else {
        throw new NotImplementedError(__METHOD__);
      } // if
    } // getDeleteUrl
    
    // ---------------------------------------------------
    //  Interface implemnetaitons
    // ---------------------------------------------------
    
    /**
     * Cached inspector instance
     *
     * @var IInspectorImplementation
     */
    private $inspector = false;
    
    /**
     * Return inspector helper instance
     * 
     * @return IInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new IInspectorImplementation($this);
      } // if
      
      return $this->inspector;
    } // inspector
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Filters all additional fields before calling DataObject::setAttributes()
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(is_array($attributes)) {

        // Attachments
        if($this instanceof IAttachments) {
          if(isset($attributes['embeded_objects']['attachments']['add']) && $attributes['embeded_objects']['attachments']['add']) {
      	    $this->attachments()->addPendingParent($attributes['embeded_objects']['attachments']['add']);
      	  } // if
      	  
      	  if(isset($attributes['embeded_objects']['attachments']['delete']) && $attributes['embeded_objects']['attachments']['delete']) {
      	    $this->attachments()->addPendingDeletion($attributes['embeded_objects']['attachments']['delete']);
      	  } // if
      	  
          if(isset($attributes['attachments']) && isset($attributes['attachments']['pending_parent']) && $attributes['attachments']['pending_parent']) {
      	    $this->attachments()->addPendingParent($attributes['attachments']['pending_parent']);
      	  } // if
      	  
      	  if(isset($attributes['attachments']) && isset($attributes['attachments']['delete']) && $attributes['attachments']['delete']) {
      	    $this->attachments()->addPendingDeletion($attributes['attachments']['delete']);
      	  } // if
        } // if
        
        // Code snippets
        if($this instanceof ICodeSnippets) {
          if(isset($attributes['embeded_objects']['code_snippets']['add']) && $attributes['embeded_objects']['code_snippets']['add']) {
      	    $this->code_snippets()->addPendingParent($attributes['embeded_objects']['code_snippets']['add']);
      	  } // if
      	  
      	  if(isset($attributes['embeded_objects']['code_snippets']['delete']) && $attributes['embeded_objects']['code_snippets']['delete']) {
      	    $this->code_snippets()->addPendingDeletion($attributes['embeded_objects']['code_snippets']['delete']);
      	  } // if
        } // if
        
        // Assignees
        if($this instanceof IAssignees) {
          if(isset($attributes['assignee_id']) && ($this->isNew() || $this->getAssigneeId() != $attributes['assignee_id'])) {
            if($attributes['assignee_id']) {
              $assignee = Users::findById($attributes['assignee_id']);
              
              if(!($assignee instanceof User)) {
                $assignee = null;
              } // if
            } else {
              $assignee = null;
            } // if
            
            $this->assignees()->setAssignee($assignee, Authentication::getLoggedUser(), false);
          } // if
          
          if(isset($attributes['assignee_id'])) {
            unset($attributes['assignee_id']); // Unset!
          } // if
          
          if(isset($attributes['other_assignees'])) {
            $this->assignees()->setPending($attributes['other_assignees']);
          } // if
        } // if
      } // if
      
	    parent::setAttributes($attributes);
    } // setAttributes
    
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
     * @throws Exception
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      if($this->fieldExists('id')) {
    		$result['id'] = $this->getId();
    	} // if
    	
    	$result['name'] = $this->getName();
    	$result['permalink'] = $this instanceof IRoutingContext && $this->isLoaded() ? $this->getViewUrl() : '#';
    	$result['class'] = get_class($this);
      $result['verbose_type'] = $user instanceof User ? $this->getVerboseType(false, $user->getLanguage()) : $this->getVerboseType(false);
      $result['verbose_type_lowercase'] = $user instanceof User ? $this->getVerboseType(true, $user->getLanguage()) : $this->getVerboseType(true);
      
      $result['urls'] = array();
      $result['permissions'] = array();
      
      if($this->fieldExists('type')) {
        $result['type'] = $this->getType();
      } // if
      
      if($this->fieldExists('created_on')) {
        $result['created_on'] = $this->getCreatedOn();
      } // if
      
      if($this->fieldExists('created_by_id')) {
        $result['created_by_id'] = $this->getCreatedById();
        if($detailed) {
          $result['created_by'] = $this->getCreatedBy() instanceof IUser ? $this->getCreatedBy()->describe($user, false, $for_interface) : null;
        } // if
      } // if
      
      if($this->fieldExists('updated_on')) {
        $result['updated_on'] = $this->getUpdatedOn();
      } // if
      
      if($this->fieldExists('updated_by_id')) {
        $result['updated_by_id'] = $this->getUpdatedById();
        if($detailed) {
          $result['updated_by'] = $this->getUpdatedBy() instanceof IUser ? $this->getUpdatedBy()->describe($user, false, $for_interface) : null;
        } // if
      } // if
      
      // Parent
      if($this->fieldExists('parent_type') && $this->fieldExists('parent_id')) {
        if($this->getParent() instanceof ApplicationObject) {
          $result['parent_class'] = get_class($this->getParent());
        	$result['parent_id'] = $this->getParent()->getId();
        } else {
          $result['parent_class'] = null;
        	$result['parent_id'] = null;
				} // if

        if ($this instanceof FwComment && $detailed && $for_interface) {
          $result['parent'] = $this->getParent()->describe($user, true, AngieApplication::INTERFACE_DEFAULT);
        } // if
      } // if
      
      $result['permissions']['can_edit'] = $this->canEdit($user);
      $result['permissions']['can_delete'] = $this->canDelete($user);
      
      if($this instanceof IRoutingContext && $this->isLoaded()) {
      	$result['urls']['view'] = $this->getViewUrl();
      	
	      try {
	      	if(!($this instanceof IReadOnly)) {
		      	$result['urls']['edit'] = $this->getEditUrl();
		      } // if
	      	$result['urls']['delete'] = $this->getDeleteUrl();
	      } catch(RouteNotDefinedError $e) {
	        
	      	// Supress not defined errors
	      } catch(Exception $e) {
	        throw $e;
	      } // try
	      
	      if($for_interface) {
  	      $result['event_names'] = array(
  	      	'created' => $this->getCreatedEventName(),
  	      	'updated' => $this->getUpdatedEventName(),
  	      	'deleted' => $this->getDeletedEventName()
  	      );
	      } // if
      } // if
      
      // ---------------------------------------------------
      //  Descriptions by helpers
      // ---------------------------------------------------
      
      // State
      if($this instanceof IState) {
        $this->state()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // Visibility
      if($this instanceof IVisibility) {
        $this->visibility()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // Complete
      if($this instanceof IComplete) {
      	$this->complete()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // Avatar
      if($this instanceof IAvatar) {
        $this->avatar()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // Comments
      if($this instanceof IComments) {
        $this->comments()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // Subtasks
      if($this instanceof ISubtasks) {
        $this->subtasks()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // Attachments
      if($this instanceof IAttachments) {
        $this->attachments()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // Assignees
      if($this instanceof IAssignees) {
        $this->assignees()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // Category
      if($this instanceof ICategory) {
        $this->category()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // Label
      if($this instanceof ILabel) {
        $this->label()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // Preview
      if($this instanceof IPreview) {
        $this->preview()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // Subscriptions
      if($this instanceof ISubscriptions) {
        $this->subscriptions()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // Favorites
      if($this instanceof ICanBeFavorite) {
        $user->favorites()->describeObject($this, $detailed, $for_interface, $result);
      } // if

      if($this instanceof ICustomFields) {
        $this->customFields()->describe($user, $detailed, $for_interface, $result);
      } // if

      // Payments
      if($this instanceof IPayments) {
        $this->payments()->describe($user, $detailed, $for_interface, $result);
      } // if
      
      // ---------------------------------------------------
      //  Additional properties
      // ---------------------------------------------------
      
      if($detailed) {
        if($this->fieldExists('body')) {        
          $result['body'] = $this->getBody();
          $result['body_formatted'] = HTML::toRichText($this->getBody(), $for_interface);
        } // if
        
        if($for_interface) {
          $result['options'] = $this->getOptions($user);
        } // if
      } // if
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     * @throws Exception
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = array(
        'id' => $this->getId(),
        'class' => get_class($this),
        'name' => $this->getName(),
        'permalink' =>  $this instanceof IRoutingContext && $this->isLoaded() ? $this->getViewUrl() : '#',
      );

      if($this->fieldExists('created_on')) {
        $result['created_on'] = $this->getCreatedOn();
      } // if

      if($this->fieldExists('created_by_id')) {
        $result['created_by_id'] = $this->getCreatedById();
        if($detailed) {
          $result['created_by'] = $this->getCreatedBy() instanceof IUser ? $this->getCreatedBy()->describe($user, false, $for_interface) : null;
        } // if
      } // if

      if($this->fieldExists('updated_on')) {
        $result['updated_on'] = $this->getUpdatedOn();
      } // if

      if($this->fieldExists('updated_by_id')) {
        $result['updated_by_id'] = $this->getUpdatedById();
        if($detailed) {
          $result['updated_by'] = $this->getUpdatedBy() instanceof IUser ? $this->getUpdatedBy()->describe($user, false, $for_interface) : null;
        } // if
      } // if

      if($this->fieldExists('parent_type') && $this->fieldExists('parent_id')) {
        $parent = $this->getParent();

        if($parent instanceof ApplicationObject) {
          $result['parent_class'] = get_class($parent);
          $result['parent_id'] = $parent->getId();
        } else {
          $result['parent_class'] = $result['parent_id'] = null;
        } // if
      } // if

      if($detailed || $this->additionallyDescribeInBriefApiResponse('basic_urls')) {
        if($this instanceof IRoutingContext && $this->isLoaded()) {
          $result['urls'] = array(
            'view' => $this->getViewUrl(),
          );

          try {
            if(!($this instanceof IReadOnly)) {
              $result['urls']['edit'] = $this->getEditUrl();
            } // if
            $result['urls']['delete'] = $this->getDeleteUrl();
          } catch(RouteNotDefinedError $e) {

            // Supress not defined errors
          } catch(Exception $e) {
            throw $e;
          } // try
        } // if
      } // if

      if($detailed || $this->additionallyDescribeInBriefApiResponse('basic_permissions')) {
        $result['permissions'] = array(
          'can_edit' => $this->canEdit($user),
          'can_delete' => $this->canDelete($user),
        );
      } // if

      if($this->fieldExists('body') && ($detailed || $this->additionallyDescribeInBriefApiResponse('body'))) {
        $result['body'] = $this->getBody();
        $result['body_formatted'] = HTML::toRichText($this->getBody());
      } // if

      // ---------------------------------------------------
      //  Descriptions by helpers
      // ---------------------------------------------------

      // State
      if($this instanceof IState) {
        $this->state()->describeForApi($user, $detailed, $result);
      } // if

      // Visibility
      if($this instanceof IVisibility) {
        $this->visibility()->describeForApi($user, $detailed, $result);
      } // if

      // Complete
      if($this instanceof IComplete) {
        $this->complete()->describeForApi($user, $detailed, $result);
      } // if

      // Avatar
      if($this instanceof IAvatar) {
        $this->avatar()->describeForApi($user, $detailed, $result);
      } // if

      // Comments
      if($this instanceof IComments) {
        $this->comments()->describeForApi($user, $detailed, $result);
      } // if

      // Subtasks
      if($this instanceof ISubtasks) {
        $this->subtasks()->describeForApi($user, $detailed, $result);
      } // if

      // Attachments
      if($this instanceof IAttachments) {
        $this->attachments()->describeForApi($user, $detailed, $result);
      } // if

      // Assignees
      if($this instanceof IAssignees) {
        $this->assignees()->describeForApi($user, $detailed, $result);
      } // if

      // Category
      if($this instanceof ICategory) {
        $this->category()->describeForApi($user, $detailed, $result);
      } // if

      // Label
      if($this instanceof ILabel) {
        $this->label()->describeForApi($user, $detailed, $result);
      } // if

      // Preview
      if($this instanceof IPreview) {
        $this->preview()->describeForApi($user, $detailed, $result);
      } // if

      // Subscriptions
      if($this instanceof ISubscriptions) {
        $this->subscriptions()->describeForApi($user, $detailed, $result);
      } // if

      // Favorites
      if($this instanceof ICanBeFavorite) {
        $user->favorites()->describeObjectForApi($this, $detailed, $result);
      } // if

      // Custom fields
      if($this instanceof ICustomFields) {
        $this->customFields()->describeForApi($user, $detailed, $result);
      } // if

      return $result;
    } // describeForApi

    /**
     * Allow subclasses to include extra details in additional brief describe for API results
     *
     * @param $what
     * @return bool
     */
    function additionallyDescribeInBriefApiResponse($what) {
      return false;
    } // additionallyDescribeInBriefApiResponse
    
    /**
     * Convert current object to JSON
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
     * Checks if object is accessible
     *
     * @return boolean
     */
    function isAccessible() {
      if (!$this->isLoaded()) {
        return false;
      } // if

      if ($this instanceof IState && $this->getState() < STATE_TRASHED) {
        return false;
      } // if

      return true;
    } // isAccessible

    /**
     * Save application object properties to the database
     *
     * @return boolean
     * @throws Exception
     */
    function save() {
      $is_new = $this->isNew();
      
      try {
        DB::beginWork('Saving application object @ ' . __CLASS__);
        
        if(($this instanceof IHistory || $this instanceof ISearchItem || $this instanceof IActivityLogs) && count($this->getModifiedFields())) {
          $modifications = array();
          
          foreach($this->getModifiedFields() as $field) {
            $old_value = $this->getOldFieldValue($field);
            $new_value = $this->getFieldValue($field);
            
            if($old_value != $new_value) {
              $modifications[$field] = array($this->getOldFieldValue($field), $this->getFieldValue($field));
            } // if
          } // if
        } else {
          $modifications = null;
        } // if
        
        if($is_new) {
          if($this->fieldExists('type') && $this->getFieldValue('type') == '') {
            $this->setFieldValue('type', get_class($this));
          } // if
          
          if($this->fieldExists('created_on') && $this->getFieldValue('created_on') === null) {
            $this->setFieldValue('created_on', new DateTimeValue());
          } // if
          
          if($this->fieldExists('created_by_id') && $this->fieldExists('created_by_name') && $this->getFieldValue('created_by_name') == '' && $this->fieldExists('created_by_email') && $this->getFieldValue('created_by_email') == '') {
            $this->setCreatedBy(Authentication::getLoggedUser());
          } // if
        } else {
          if($this->fieldExists('updated_on')) {
            $this->setFieldValue('updated_on', new DateTimeValue());
          } // if
          
          if($this->fieldExists('updated_by_id') && $this->fieldExists('updated_by_name') && !$this->isModifiedField('updated_by_name') && $this->fieldExists('updated_by_email') && !$this->isModifiedField('updated_by_email')) {
            $this->setUpdatedBy(Authentication::getLoggedUser());
          } // if
        } // if

        // Fix problem when is_locked is set to be NULL instead of FALSE @TODO: Fix with default field values on model level
        if($this->fieldExists('is_locked') && $this->getFieldValue('is_locked') === null) {
          $this->setFieldValue('is_locked', false);
        } // if

        parent::save();

	      // subscribe mentioned users
	      if ($this instanceof ISubscriptions) {
		      $mentioned_user_ids = $this->getNewMentions();
		      if (is_foreachable($mentioned_user_ids)) {
			      foreach ($mentioned_user_ids as $mentioned_user_id) {
				      $mentioned_user = Users::findById($mentioned_user_id);
				      if ($mentioned_user instanceof User) {
					      $this->subscriptions()->subscribe($mentioned_user);
				      } // if
			      } // foreach
		      } // if
	      } // if

        if($this instanceof IObjectContext) {
          if($is_new) {
            ApplicationObjects::rememberContext($this);
          } else {
            ApplicationObjects::updateRememberedContext($this);
          } // if
        } // if

        // unsubscribe users that cannot see private objects if this is a private object
        if($this instanceof IVisibility) {
          $this->visibility()->updateUsersWithoutPrivatePermissions();
        } //if
        
        if($this instanceof IAttachments) {
          $attachments_modification = $this->attachments()->commitPending();
        } // if
        
	    	if ($this instanceof ICodeSnippets) {
	    		$code_snippets_modification = $this->code_snippets()->commitPending();
	    	} // if
        
        if($this instanceof IAssignees) {
          $assignees_modification = $this->assignees()->commitPending();
        } // if
        
        if(isset($modifications) && $modifications) {
          if(isset($attachments_modification) && $attachments_modification) {
            $modifications['attachments'] = $attachments_modification;
          } // if
          
          if(isset($assignees_modification) && $assignees_modification) {
            $modifications['assignees'] = $assignees_modification;
          } // if
          
// @todo determine if this is necessary
//          if ($code_snippets_modification) {
//          	$modifications['code_snippets'] =$code_snippets_modification;
//          } // if
          
          // Update modification log
          if($this instanceof IHistory && $modifications && count($modifications)) {
            if($is_new) {
              $by = $this->getCreatedBy();
            } else {
              $by = Authentication::getLoggedUser();
            } // if

            if($by instanceof IUser) {
              $this->history()->commitModifications($modifications, $by, $is_new);
            } // if
          } // if

          // Update search index
          if($this instanceof ISearchItem) {
            if($is_new) {
              $this->search()->create();
            } else {
              if($modifications) {
                $this->search()->update($modifications);
              } // if
            } // if
          } // if
        } // if

        // Log creation or update activity
        if($this instanceof IActivityLogs && !$this->activityLogs()->isGagged()) {
          if($is_new) {
            $created_by = $this->fieldExists('created_by_id') && $this->getCreatedBy() instanceof IUser ? $this->getCreatedBy() : Authentication::getLoggedUser();
            
            if($created_by instanceof IUser) {
              $this->activityLogs()->logCreation($created_by);
            } // if
          } else {
            $updated_by = $this->fieldExists('updated_by_id') && $this->getUpdatedBy() instanceof IUser ? $this->getUpdatedBy() : Authentication::getLoggedUser();
            
            if($updated_by instanceof IUser && $modifications) {
              $this->activityLogs()->logUpdate($updated_by, $modifications);
            } // if
          } // if
        } // if
        
        DB::commit('Application object saved @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to save application object @ ' . __CLASS__);
        throw $e;
      } // try
      
      if ($this->fieldExists('id')) {
      	DataObjectPool::set($this);
      } // if
      
      return true;
    } // save
    
    /**
     * Drop object from database
     *
     * @return boolean
     */
    function delete() {
      if($this instanceof IState) {
        $this->state()->delete(); // Just change the state to deleted
      } else {
        $this->forceDelete(); // Remove from the database
      } // if
    } // delete
    
    /**
     * Skip soft delete and force delete project
     */
    function forceDelete() {
      try {
        DB::beginWork('Removing object from database @ ' . __CLASS__);
        
        // Object context
        if($this instanceof IObjectContext) {
          ApplicationObjects::forgetContexts($this);
        } // if
        
        // Favorites
        if($this instanceof ICanBeFavorite) {
          Favorites::deleteByParent($this);
        } // if
        
        // Attachments
        if($this instanceof IAttachments) {
          Attachments::deleteByParent($this, false);
        } // if
        
        // Comments
        if($this instanceof IComments) {
          Comments::deleteByParent($this, false);
        } // if
        
        // Subtasks
        if($this instanceof ISubtasks) {
          Subtasks::deleteByParent($this, false);
        } // if
        
        // Activity logs
        if($this instanceof IActivityLogs) {
          ActivityLogs::deleteByParent($this, false);
        } // if
        
        // Subscriptions
        if($this instanceof ISubscriptions) {
          Subscriptions::deleteByParent($this);
        } // if
        
        // Assignees
        if($this instanceof IAssignees) {
          Assignments::deleteByParent($this);
        } // if
        
        // Reminders
        if($this instanceof IReminders) {
        	Reminders::deleteByParent($this);
        } // if
        
        // History
        if($this instanceof IHistory) {
          ModificationLogs::deleteByParent($this);
        } // if
        
        parent::delete();
        
        DB::commit('Object removed from database @ ' . __CLASS__);
        
        // Search index (done outside of the transaction)
        if($this instanceof ISearchItem) {
          $this->search()->clear();
        } // if
        
      } catch(Exception $e) {
        DB::rollback('Failed to remove object from database @ ' . __CLASS__);
        throw $e;
      } // try
    } // forceDelete
    
  }