<?php

  /**
   * Interface that all project assets implement
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class ProjectAsset extends ProjectObject implements IComments, ICategory, IAttachments, ISubscriptions, IPreview, ISearchItem, ICanBeFavorite {
    
    /**
     * Permission name
     * 
     * @var string
     */
    protected $permission_name = 'file';
    
    /**
     * Construct project asset
     *
     * @param mixed $id
     */
    function __construct($id = null) {
      $this->setModule(FILES_MODULE);
      parent::__construct($id);
    } // __constructs
    
    /**
     * Routing context name
     *
     * @var string
     */
    protected $routing_context = false;
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      if($this->routing_context === false) {
        $this->routing_context = 'project_assets_' . Inflector::underscore(get_class($this));
      } // if
      
      return $this->routing_context;
    } // getRoutingContext
    
    /**
     * Routing context parameters
     *
     * @var array
     */
    protected $routing_context_params = false;
    
    /**
     * Return routing context parameters
     *
     * @return array
     */
    function getRoutingContextParams() {
      if($this->routing_context_params === false) {
        $this->routing_context_params = array(
          'project_slug' => $this->getProject()->getSlug(),
          'asset_id' => $this->getId(),
        );
      } // if
      
      return $this->routing_context_params;
    } // getRoutingContextParams
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return parent::getObjectContextPath() . '/files/' . ($this->getVisibility() == VISIBILITY_PRIVATE ? 'private' : 'normal') . '/' . $this->getId();
    } // getContextPath
    
    /**
     * Cached inspector instance
     * 
     * @var IProjectAssetInspectorImplementation
     */
    private $inspector = false;
    
    /**
     * Return inspector helper instance
     * 
     * @return IProjectAssetInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new IProjectAssetInspectorImplementation($this);
      } // if
      
      return $this->inspector;
    } // inspector
    
    /**
     * Return first letter of the file name
     * 
     * @return string
     */
    function getFirstLetter() {
      return Inflector::transliterate(strtolower_utf(substr_utf($this->getName(), 0, 1)));
    } // getFirstLetter

    /**
     * Return project asset created on date
     *
     * @return string
     */
    function getCreatedOnDate() {
      $created_on = $this->getCreatedOn();

      if($created_on instanceof DateTimeValue) {
        $created_on_date = dateval($created_on);
        if($created_on_date->isToday()) {
          return 'today';
        } elseif($created_on_date->isYesterday()) {
          return 'yesterday';
        } else {
          return $created_on_date->toMySQL();
        } // if
      } // if

      return '';
    } // getCreatedOnDate

    /**
     * Return project asset updated on date
     *
     * @return string
     */
    function getUpdatedOnDate() {
      $updated_on = $this->getUpdatedOn();

      if($updated_on instanceof DateTimeValue) {
        $updated_on_date = dateval($updated_on);
        if($updated_on_date->isToday()) {
          return 'today';
        } elseif($updated_on_date->isYesterday()) {
          return 'yesterday';
        } else {
          return $updated_on_date->toMySQL();
        } // if
      } // if

      return '';
    } // getUpdatedOnDate
    
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
      
      if($for_interface) {
			  $result['first_letter'] = $this->getFirstLetter();
			  $result['created_on_date'] = $this->getCreatedOnDate();
			  $result['updated_on_date'] = $this->getUpdatedOnDate();
      } // if

      $result['icon'] = $this->preview()->getSmallIconUrl();
      
      return $result;
    } // describe
    
    /**
     * Return path to view template
     *
     * @return string
     */
    function getViewTemplatePath() {
      return get_view_path('view', 'assets', FILES_MODULE);
    } // getViewTemplatePath
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
        
    /**
     * UserAvatar implementation instance for this object
     *
     * @var IFilePreviewImplementation
     */
  	private $preview;
    
    /**
     * Return subtasks implementation for this object
     *
     * @return IUserAvatarImplementation
     */
    function preview() {
      if(empty($this->preview)) {
        $this->preview = new IFilePreviewImplementation($this);
      } // if
      
      return $this->preview;
    } // preview
    
    /**
     * Comment interface instance
     *
     * @var IAssetCommentsImplementation
     */
    private $comments;
    
    /**
     * Return project object comments interface instance
     *
     * @return IAssetCommentsImplementation
     */
    function &comments() {
      if(empty($this->comments)) {
        $this->comments = new IAssetCommentsImplementation($this);
      } // if
      return $this->comments;
    } // comments
    
    /**
     * Category implementation instance
     *
     * @var IAssetCategoryImplementation
     */
    private $category = false;
    
    /**
     * Return category implementation
     *
     * @return IAssetCategoryImplementation
     */
    function category() {
      if($this->category === false) {
        $this->category = new IAssetCategoryImplementation($this);
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
    private $subscriptions;
    
    /**
     * Return subscriptions helper for this object
     *
     * @return ISubscriptionsImplementation
     */
    function &subscriptions() {
      if(empty($this->subscriptions)) {
        $this->subscriptions = new IProjectObjectSubscriptionsImplementation($this);
      } // if
      
      return $this->subscriptions;
    } // subscriptions
    
    /**
     * Cached search helper instance
     *
     * @var IAssetSearchItemImplementation
     */
    private $search = false;
    
    /**
     * Return search helper instance
     * 
     * @return IAssetSearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new IAssetSearchItemImplementation($this);
      } // if
      
      return $this->search;
    } // search
    
    /**
     * Return event name prefix
     * 
     * @return string
     */
    function getEventNamesPrefix() {
      return 'asset';
    } // getEventNamesPrefix
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
    	if(!($this instanceof IReadOnly) && $this->canEdit($user)) {
        $options->beginWith('edit', array(
          'url' => $this->getEditUrl(),
          'text' => lang('Edit'),
        	'onclick' => new FlyoutFormCallback('asset_updated'),
        	'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) : '',
        	'important' => true
        ), true);
      } // if
      
      parent::prepareOptionsFor($user, $options, $interface);
    } // prepareOptionsFor
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    /**
     * Validate before save
     * 
     * @package ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name', 'project_id')) {
        	// generate unique name        	
        	$this->setName(ProjectAssets::checkNameUniqueness($this->getName(), $this->getProjectId()));
        } // if
      } else {
        $errors->addError(lang(':name name is required', array(
          'name' => $this->getVerboseType(), 
        )), 'name');
      } // if
      
      // Validate project object flags
      parent::validate($errors, true);
    } // validate
    
  }