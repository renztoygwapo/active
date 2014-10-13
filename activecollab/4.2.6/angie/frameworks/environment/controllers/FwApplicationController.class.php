<?php

  /**
   * Abstract application controller that application can inherit
   *
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwApplicationController extends Controller {
    
    /**
     * Logged in user
     *
     * @var User
     */
    protected $logged_user;
    
    /**
     * Wireframe instance
     *
     * @var BackendWireframe
     */
    protected $wireframe;
    
    // ---------------------------------------------------
    //  Overrideable controller settings
    // ---------------------------------------------------
    
    /**
     * User is required to be logged in
     * 
     * If true user will need to be logged in in order to execute controller 
     * actions. If user is not logged in he will be redirected to login route. 
     * Override in subclasses for controllers that does not require for user to 
     * be logged in
     *
     * @var boolean
     */
    protected $login_required = true;
    
    /**
     * Name of the login route
     *
     * @var string
     */
    protected $login_route_name = 'login';
    
    /**
     * This controller restricts access to people who are not logged in when 
     * system is in maintenance mode
     *
     * @var boolean
     */
    protected $restrict_access_in_maintenance_mode = true;
    
    /**
     * Construct framework level application controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
      
      // Wireframe
      $this->wireframe = $this->getWireframeInstance();
      
      if($this->response instanceof WebInterfaceResponse) {
        $this->response->assignByRef('wireframe', $this->wireframe);
      
        // Provide required variables to templates
        $this->response->assign(array(
          'root_url' => ROOT_URL, 
          'assets_url' => ASSETS_URL,
          'prefered_interface' => AngieApplication::getPreferedInterface(),
        ));

        $config_options = ConfigOptions::getValue(array('format_date', 'format_time'));

        $default_currency = Currencies::getDefault();

        $variables = array(
          'homepage_url' => ROOT_URL,
          'assets_url' => ASSETS_URL,
          'branding_url' => AngieApplication::getBrandImageUrl(''),
          'branding_removed' => false,
          'branding_title' => 'Powered by ' . AngieApplication::getName(),
          'branding_description' => 'Another Fine Angie Powered Application',
          'branding_website' => AngieApplication::getUrl(),
          'days_off' => Globalization::getDaysOffMappedForJs(),
          'work_days' => Globalization::getWorkdays(),
          'wireframe_updates_url' => Router::assemble('wireframe_updates'),
          'url_base' => URL_BASE,
          'path_info_through_query_string' => PATH_INFO_THROUGH_QUERY_STRING,
          'default_module' => DEFAULT_MODULE,
          'default_controller' => DEFAULT_CONTROLLER,
          'default_action' => DEFAULT_ACTION,
          'prefered_interface' => AngieApplication::getPreferedInterface(),
          'max_upload_size' => get_max_upload_size(),
          'month_names' => Globalization::getMonthNames(),
          'short_month_names' => Globalization::getShortMonthNames(),
          'day_names' => Globalization::getDayNames(),
          'short_day_names' => Globalization::getShortDayNames(),
          'application_mode' => APPLICATION_MODE,
          'identity_name' => ConfigOptions::getValue('identity_name'),
          'application_version' => AngieApplication::getVersion(),
          'format_date' => $config_options['format_date'] ? $config_options['format_date'] : FORMAT_DATE,
          'format_datetime' => $config_options['format_time'] ? $config_options['format_time'] : FORMAT_DATETIME,
          'default_currency' => array(
            'id' => $default_currency->getId(),
            'name' => $default_currency->getName(),
            'code' => $default_currency->getCode(),
            'is_default' => $default_currency->getIsDefault(),
            'decimal_spaces' => $default_currency->getDecimalSpaces(),
            'urls' => array(
              'set_as_default' => $default_currency->getSetAsDefaultUrl()
            )
          ),
          'visual_editor' => array(
            'quick_search_url' => Router::assemble('quick_backend_search'),
            'temporary_attachments_upload_url'  => Router::assemble('temporary_attachment_add'),
            'add_script_url'					          => Router::assemble('code_snippets_add'),
            'view_script_url'					          => Router::assemble('code_snippet', array('code_snippet_id' => '--SNIPPET-ID--')),
            'edit_script_url'					          => Router::assemble('code_snippet_edit', array('code_snippet_id' => '--SNIPPET-ID--')),
            'preview_script_url'                => Router::assemble('code_snippet_preview'),
            'supported_script_syntaxes'         => HyperlightForAngie::getAvailableLanguages(),
            'whitelisted_tags'                  => HTML::getWhitelistedTagsForEditor()
          )
        );

        EventsManager::trigger('on_initial_javascript_assign', array(&$variables));

        $this->wireframe->javascriptAssign($variables);
      } // if
      
      // Authentication
      if(AngieApplication::isFrameworkLoaded('authentication')) {
        $this->logged_user =& Authentication::getLoggedUser();
        
        // Maintenance mode
        if(ConfigOptions::getValue('maintenance_enabled')) {
          if($this->logged_user instanceof User && !$this->logged_user->isAdministrator()) {
            Authentication::getProvider()->logUserOut();
            $this->response->redirectTo('homepage');
          } // if
        } // if
        
        // Check permissions
        if($this->login_required && !($this->logged_user instanceof User)) {
          $this->response->requireAuthentication();
        } // if

        $this->wireframe->javascriptAssign(array(
          'login_url' => Router::assemble('login'),
          'logout_url' => Router::assemble('logout'),
          'session_timestamp_cookie' => Authentication::getProvider()->getSessionTimestampVarName() ? Cookies::getVariableName(Authentication::getProvider()->getSessionTimestampVarName()) : null
        ));
      } // if

      $language = null;
      
      if($this->response instanceof WebInterfaceResponse) {
        $this->response->assignByRef('logged_user', $this->logged_user);
        
        // If we have user, provide user specific values
        if ($this->logged_user instanceof User) {
          $language = $this->logged_user->getLanguage();

          $this->wireframe->javascriptAssign(array(
            'first_week_day' => ConfigOptions::getValueFor('time_first_week_day', $this->logged_user, 0),
            'month_names' => Globalization::getMonthNames($language),
            'short_month_names' => Globalization::getShortMonthNames($language),
            'day_names' => Globalization::getDayNames($language),
            'short_day_names' => Globalization::getShortDayNames($language),
            'locale' => $language instanceof Language ? $language->getLocale() : BUILT_IN_LOCALE
          ));
        } // if

        if (!($language instanceof Language)) {
          $language = Languages::findDefault();
        } // if

        if ($language instanceof Language) {
          $locale_code = $language->getLocaleCode();
          $decimal_separator = $language->getDecimalSeparator();
          $thousands_separator = $language->getThousandsSeparator();
        } else {
          $locale_code = Languages::getLocaleCode(BUILT_IN_LOCALE);
          $decimal_separator = '.';
          $thousands_separator = ',';
        } // if

        $this->wireframe->javascriptAssign(array(
          'decimal_separator' => $decimal_separator,
          'thousands_separator' => $thousands_separator,
        ));

        $this->response->assign('locale_code', $locale_code);
      } // if
    } // __construct
    
    /**
     * Prepare controller delegate
     *
     * @param Controller $delegate
     * @param array $additional
     */
    function __prepareDelegate(Controller &$delegate, $additional = null) {
      parent::__prepareDelegate($delegate, array(
        'logged_user' => &$this->logged_user, 
        'wireframe' => &$this->wireframe, 
      ));
    } // __prepareDelegate

    /**
     * Execute before any action
     */
    function __before() {
      parent::__before();

      if($this->request->isInlineCall()) {
        $layout = 'inline';
      } else if ($this->request->isQuickViewCall()) {
        $layout = 'quick_view';
      } else if($this->request->isSingleCall()) {
        $layout = 'single';
      } else if($this->request->isPrintCall()) {
        $layout = 'print';
        AngieApplication::setPreferedInterface(AngieApplication::INTERFACE_PRINTER);
      } else {
        $layout = $this->getDefaultLayout();
      } // if

      $this->setLayout(array(
        'module' => SYSTEM_MODULE,
        'layout' => $layout,
      ));
    } // __before
    
    /**
     * Mass edit objects
     *
     * @param array
     */
    protected $mass_edit_objects;
    
    /**
     * Mass edit functionality
     */
    function mass_edit() {
      if (!$this->request->isAsyncCall() || !$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if

      if (!is_foreachable($this->mass_edit_objects)) {
        $this->response->operationFailed();
      } // if

      $actions = $this->request->post('actions');
      if (!is_foreachable($actions)) {
        $this->response->ok();
      } // if
            
      $objects_type = $this->request->post('objects_type');
      if (!$objects_type) {
        $this->response->badRequest();
      } // if
      
      $variables = $this->request->post('variables');
            
      try {
        $manager = new MassManager($this->logged_user, $this->mass_edit_objects[0]);
        $response = $manager->performUpdate($this->mass_edit_objects, $actions, $variables);
        $this->response->respondWithData($response, array(
          'detailed' => true
        ));
      } catch (Exception $e) {
        DB::rollback('Failed to update ' . $objects_type . ' @ ' . __CLASS__);
        $this->response->exception($e);
      } // try
    } // mass_edit
    
    // ---------------------------------------------------
    //  Internals
    // ---------------------------------------------------
    
    /**
     * Return response instance for this particular controller
     * 
     * @return Response
     */
    abstract protected function getResponseInstance();

    /**
     * Return default layout
     */
    function getDefaultLayout() {
      return 'frontend';
    } // getDefaultLayout
    
    /**
     * Return wireframe instance for this controller
     *
     * @return Wireframe
     */
    abstract protected function getWireframeInstance();
    
  }