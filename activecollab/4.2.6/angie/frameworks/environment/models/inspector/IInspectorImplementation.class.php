<?php

  /**
   * Base IInspectorImplementation implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class IInspectorImplementation implements IDescribe {

    const RENDER_SCOPE_SINGLE = 'single';
    const RENDER_SCOPE_QUICK_VIEW = 'quick_view';

    /**
     * Parent object instance
     * 
     * @var IInspector
     */
    protected $object;

    /**
     * Custom name format
     *
     * @var null|string
     */
    protected $name_format;
    
    /**
     * Construct IInspectorImplementation instance
     * 
     * @param IInspector $object
     */
    function __construct(IInspector $object) {
      $this->object = $object;
      $this->setRenderScope(self::RENDER_SCOPE_SINGLE);
      $this->setEventScope('single');
    } // __construct
    
    /**
     * Body Field
     * 
     * @var string
     */
    protected $body_field = 'body';
    
    /**
     * Supports body
     * 
     * @var boolean
     */
    protected $supports_body = true;
    
    /**
     * Supports properties
     * 
     * @var boolean
     */
    protected $supports_properties = true;
    
    /**
     * Supports widgets
     * 
     * @var boolean
     */
    protected $supports_widgets = true;
    
    /**
     * Supports indicators
     * 
     * @var boolean
     */
    protected $supports_indicators = true;
    
    /**
     * Supports actions
     * 
     * @var boolean
     */
    protected $supports_actions = true;
    
    /**
     * Supports bars
     * 
     * @var boolean
     */
    protected $supports_bars = true;
    
    /**
     * Supports titlebar widgets
     * 
     * @var boolean
     */
    protected $supports_titlebar_widgets = true;
    
    /**
     * Custom renderer
     * 
     * @var InspectorElement
     */
    protected $custom_renderer = false;
    
    /**
     * Active interface
     * 
     * @var string
     */
    protected $active_interface = false;
    
    /**
     * Optional body content
     * 
     * @var boolean
     */
    protected $optional_body_content = false;

    /**
     * Render scope
     *
     * @var bool
     */
    protected $render_scope = false;

    /**
     * Event scope
     *
     * @var bool
     */
    protected $event_scope = false;
    
    /**
     * Set's the active interface
     * 
     * @param string $interface
     */
    function setActiveInterface($interface = AngieApplication::INTERFACE_DEFAULT) {
      $this->active_interface = $interface;
    } // setActiveInterface
    
    /**
     * Return's the active interface
     * 
     * @return string
     */
    function getActiveInterface() {
      if ($this->active_interface === false) {
        $this->active_interface = AngieApplication::INTERFACE_DEFAULT;
      } // if
      return $this->active_interface;
    } // getActiveInterface

    /**
     * Return render scope
     *
     * @return string
     */
    function getRenderScope() {
      return $this->render_scope;
    } // getRenderScope

    /**
     * Set rendering scope
     *
     * @param string $scope
     */
    function setRenderScope($scope) {
      $this->render_scope = $scope;
    } // setRenderScope

    /**
     * Return event scope
     *
     * @return string
     */
    function getEventScope() {
      return $this->event_scope;
    } // getEventScope

    /**
     * Set's the event scope
     *
     * @param string $scope
     */
    function setEventScope($scope) {
      $this->event_scope = $scope;
    } // setEventScope
    
    // ---------------------------------------------------
    //  Elements
    // ---------------------------------------------------
    
    /**
     * Cached list of properties, indexed by interface
     *
     * @var NamedList
     */
    protected $properties = false;
    
    /**
     * Return list of inspected properties
     * 
     * @param IUser $user
     * @return NamedList
     */
    function getProperties(IUser $user) {
      return $this->properties;
    } // getProperties
    
    /**
     * Cached list of indicators, indexed by interface
     *
     * @var NamedList
     */
    protected $indicators = false;
    
    /**
     * Return indicators
     * 
     * @param IUser $user
     * @return NamedList
     */
    function getIndicators(IUser $user) {
      return $this->indicators;
    } // getIndicators
    
    /**
     * Cached list of widgets, indexed by interface
     * 
     * @var array
     */
    protected $widgets = false;
    
    /**
     * Return widgets
     * 
     * @param IUser $user
     * @return NamedList
     */
    function getWidgets(IUser $user) {
      return $this->widgets;
    } // getWidgets
    
    /**
     * Cached list of bars, indexed by interface
     * 
     * @var array
     */
    protected $bars = false;
    
    /**
     * Return bars
     * 
     * @param IUser $user
     * @return NamedList
     */
    function getBars(IUser $user) {
      return $this->bars;
    } // getBars
    
    /**
     * Cached list of titlebar widgets
     * 
     * @var $array
     */
    protected $titlebar_widgets = false;
    
    /**
     * Return titlebar widgets
     * 
     * @param IUser $user
     * @return NamedList
     */
    function getTitlebarWidgets(IUser $user) {
      return $this->titlebar_widgets;
    } // getBars

    /**
     * Returns true if this object has body text
     *
     * @param string $interface
     * @return boolean
     */
    function hasBody($interface = AngieApplication::INTERFACE_DEFAULT) {
      return $this->object->fieldExists('body');
    } // hasBody
    
    /**
     * Returns true if parent object has body value set
     *
     * @param string $interface
     * @return boolean
     */
    function hasBodyValue($interface = AngieApplication::INTERFACE_DEFAULT) {
      return $this->hasBody() && $this->object->getBody();
    } // hasBodyValue
    
    /**
     * Return prepared body text
     *
     * @param string $interface
     * @return string
     */
    function getBody($interface = AngieApplication::INTERFACE_DEFAULT) {
      return $this->hasBodyValue() ? HTML::toRichText($this->object->getBody(), $interface) : '';
    } // getBody
    
    /**
     * Returns true if parent object has icon
     *
     * @param string $interface
     * @return boolean
     */
    function hasIcon($interface = AngieApplication::INTERFACE_DEFAULT) {
      return $this->object instanceof IAvatar;
    } // hasIcon
    
    /**
     * Returns icon URL
     * 
     * @param integer $size
     * @param string $interface
     * @return string
     */
    function getIconUrl($size = null, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if(empty($size)) {
        $size = IAvatarImplementation::SIZE_SMALL;
      } // if
      
      return $this->object->avatar()->getUrl($size);
    } // getIconUrl
    
    /**
     * Add Widget
     * 
     * @param string $name
     * @param string $label
     * @param InspectorElement $handler
     */
    function addWidget($name, $label, InspectorElement $handler) {
      if ($this->widgets === false) {
        $this->widgets = new NamedList();
      } // if
      
      $this->widgets->add($name, array(
        'label' => $label,
        'handler' => $handler
      ));
    } // addWidget
    
    /**
     * Remove widget 
     * 
     * @param string $name
     */
    function removeWidget($name) {
      $this->widgets->remove($name);
    } // removeWidget
    
    /**
     * Add property
     * 
     * @param string $name
     * @param string $label 
     * @param InspectorElement $handler
     */
    function addProperty($name, $label, InspectorElement $handler) {
      if ($this->properties === false) {
        $this->properties = new NamedList();
      } // if
      
      $this->properties->add($name, array(
        'label' => $label,
        'handler' => $handler
      ));
    } // addProperty
    
    /**
     * Remove property 
     * 
     * @param string $name
     */
    function removeProperty($name) {
      $this->properties->remove($name);
    } // removeProperty
    
    /**
     * Add indicator
     * 
     * @param string $name
     * @param string $label
     * @param InspectorElement $handler
     */
    function addIndicator($name, $label, InspectorElement $handler) {
      if ($this->indicators === false) {
        $this->indicators = new NamedList();
      } // if
      
      $this->indicators->add($name, array(
        'label' => $label,
        'handler' => $handler
      ));
    } // addIndicator
    
    /**
     * Remove indicator 
     * 
     * @param string $name
     */
    function removeIndicator($name) {
      $this->indicators->remove($name);
    } // removeIndicator
    
    /**
     * Add bar
     * 
     * @param string $name
     * @param InspectorElement $handler
     * @param boolean $important
     */
    function addBar($name, InspectorElement $handler, $important = false) {
      if ($this->bars === false) {
        $this->bars = new NamedList();
      } // if
      
      $this->bars->add($name, array(
        'handler' => $handler,
        'important' => $important
      ));
    } // addBar
    
    /**
     * Remove bar 
     * 
     * @param string $name
     */
    function removeBar($name) {
      $this->bars->remove($name);
    } // removeBar
    
    
    /**
     * Adds a titlebar widget
     * 
     * @param string $name
     * @param InspectorElement $handler
     * @param boolean $left
     */
    function addTitlebarWidget($name, InspectorElement $handler, $left = true) {
      if ($this->titlebar_widgets === false) {
        $this->titlebar_widgets = new NamedList();
      } // if
      
      $this->titlebar_widgets->add($name, array(
        'handler' => $handler,
        'left' => $left
      ));
    } // addTitlebarWidget
    
    /**
     * Remove titlebar widget 
     * 
     * @param string $name
     */
    function removeTitlebarWidget($name) {
      $this->titlebar_widgets->remove($name);
    } // removeTitlebarWidget
    
    
    // ---------------------------------------------------
    //  Load data for given interface
    // ---------------------------------------------------

    /**
     * Load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    public function load(IUser $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if ($interface != AngieApplication::INTERFACE_DEFAULT) {
        $this->supports_indicators = false;
        $this->supports_actions = false;
        $this->supports_bars = false;
      } // if
      
      // set's this interface as active interface
      $this->setActiveInterface($interface);
      
      // do the inherited loading
      $this->do_load($user, $interface);
    } // load
    
    /**
     * does the real loading
     * 
     * @param IUser $user
     * @param string $interface
     */
    protected function do_load(IUser $user, $interface) {
      if ($this->object->fieldExists('visibility')) {
        if($interface == AngieApplication::INTERFACE_PHONE) { // Visibility is a property in phone interface
          $this->addProperty('visibility', lang('Visibility'), new VisibilityInspectorIndicator($this->object));
        } else {
          $this->addIndicator('visibility', lang('Visibility'), new VisibilityInspectorIndicator($this->object));
        } // if
      } // if
      
      if ($this->object instanceof IState) {
        $this->addBar('state_bar', new StateInspectorBar($this->object));
      } // if

      // created on property
      if ($this->object->fieldExists('created_on') && $this->object->getCreatedOn()) {
        $this->addProperty('created_on', lang('Created') , new ActionOnByInspectorProperty($this->object));
      } // if
           
      // completed property
      if (AngieApplication::isFrameworkLoaded('complete') && $this->object instanceof IComplete) {
        $this->addProperty('completed_on', lang('Completed') , new ActionOnByInspectorProperty($this->object, 'completed'));
        if ($this->object->fieldExists('priority')) {
          if($interface == AngieApplication::INTERFACE_PHONE) { // Priority is a property in phone interface
            $this->addProperty('priority', lang('Priority') , new PriorityInspectorTitlebarWidget($this->object), true);
          } else {
            $this->addTitlebarWidget('priority', new PriorityInspectorTitlebarWidget($this->object), true);
          } // if
        } // if
      } // if
      
      // label titlebar widget
      if (AngieApplication::isFrameworkLoaded('labels') && $this->object instanceof ILabel) {
        if($interface == AngieApplication::INTERFACE_PHONE) { // Label is a property in phone interface
          $this->addProperty('label', lang('Label'), new LabelInspectorTitlebarWidget($this->object), true);
        } else {
          $this->addTitlebarWidget('label', new LabelInspectorTitlebarWidget($this->object), true);
        } // if
      } // if
      
      // Category
      if (AngieApplication::isFrameworkLoaded('categories') && $this->object instanceof ICategory) {
        $this->addProperty('category', lang('Category'), new CategoryInspectorProperty($this->object));
      } // if
      
      // Favorite
      if (AngieApplication::isFrameworkLoaded('favorites') && $this->object instanceof ICanBeFavorite) {
        $this->addIndicator('favorite', lang('Favorite'), new FavoriteInspectorIndicator($this->object));
      } // if
      
      // Subscription indicator
      if (AngieApplication::isFrameworkLoaded('subscriptions') && $this->object instanceof ISubscriptions) {
        $this->addIndicator('subscribed', lang('Subscribed'), new SubscribeInspectorIndicator($this->object));
      } // if
           
      EventsManager::trigger('on_object_inspector', array(&$this, &$this->object, &$user, &$interface));
    } // load
    
    /**
     * Unload the inspector
     * 
     */
    protected function unload() {
      $this->properties = false;
      $this->widgets = false;
      $this->indicators = false;
      $this->titlebar_widgets = false;
      $this->bars = false;
    } // unload

    /**
     * Set's the custom renderer
     * 
     * @param InspectorElement $renderer
     */
    function setCustomRenderer(InspectorElement $renderer) {
      $this->custom_renderer = $renderer;
    } // setCustomerRenderer
    
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
      $result = array(
        'object' => $this->object->describe($user, $detailed, $for_interface),
        'interface' => $this->getActiveInterface(),
        'bars' => $this->getBars($user),
        'widgets' => $this->getWidgets($user),
        'properties' => $this->getProperties($user),
        'indicators' => $this->getIndicators($user),
        'titlebar_widgets' => $this->getTitlebarWidgets($user),
        'supports_properties' => $this->supports_properties,
        'supports_widgets' => $this->supports_widgets,
        'supports_indicators' => $this->supports_indicators,
        'supports_actions' => $this->supports_actions,
        'supports_bars' => $this->supports_bars,
        'supports_titlebar_widgets' => $this->supports_titlebar_widgets,
        'supports_body' => $this->supports_body,
        'body_field' => $this->body_field,
        'body_optional' => $this->optional_body_content,
        'name_format' => $this->name_format,
        'renderer' => $this->custom_renderer,
        'render_scope' => $this->render_scope,
        'event_scope' => $this->event_scope
      );
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     * @throws NotImplementedError
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
  
  }