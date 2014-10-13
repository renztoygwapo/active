<?php

  // Build on top of system module
  AngieApplication::useController('frontend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level API controller implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwApiController extends FrontendController {
    
    /**
     * Actions that are available through API
     *
     * @var array
     */
    protected $api_actions = array('info');
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(!$this->request->isApiCall()) {
        $this->response->badRequest();
      } // if
    } // __construct
  
    /**
     * Show application info (available only through API)
     */
    function info() {
      $workdays = ConfigOptions::getValue('time_workdays');

      $this->response->respondWithData(array(
        'api_version' => AngieApplication::getApiVersion(),
        'system_version' => AngieApplication::getVersion(),
        'logged_user' => $this->logged_user,
        'read_only' => Authentication::getApiSubscription() instanceof ApiClientSubscription ? Authentication::getApiSubscription()->getIsReadOnly() : true,
        'root_url' => ROOT_URL,
        'assets_url' => ASSETS_URL,
        'loaded_frameworks' => objects_array_extract(AngieApplication::getFrameworks(), 'getName'),
        'enabled_modules' => AngieApplication::getEnabledModuleNames(), 
        'max_upload_size' => get_max_upload_size(),
        'first_week_day' => (integer) ConfigOptions::getValue('time_first_week_day'),
        'workdays' => is_array($workdays) ? implode(',', $workdays) : '',
      ), array('as' => 'info'));
    } // info

    /**
     * Display defined currencies
     */
    function currencies() {
      $this->response->respondWithData(Currencies::find(), array(
        'as' => 'currencies',
      ));
    } // currencies

    /**
     * Display a list of days off
     */
    function days_off() {
      $this->response->respondWithData(DayOffs::find(), array(
        'as' => 'day_offs',
      ));
    } // days_off
    
  }