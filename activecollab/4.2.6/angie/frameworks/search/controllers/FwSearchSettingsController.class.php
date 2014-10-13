<?php

  // Build on top of admin controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Search settings controller
   *
   * @package activeCollab.frameworks.search
   * @subpackage controllers
   */
  class FwSearchSettingsController extends AdminController {

    /**
     * Show search settings page
     */
    function index() {
      $providers = Search::getAvailableProviders();

      $search_settings_data = $this->request->post('search_settings');
      if(!is_array($search_settings_data)) {
        $search_settings_data = ConfigOptions::getValue(array(
          'search_provider'
        ));

        if($providers) {
          foreach($providers as $provider) {
            $provider_settings = $provider->getSettings();

            if($provider_settings && is_foreachable($provider_settings)) {
              $search_settings_data = array_merge($search_settings_data, $provider_settings);
            } // if
          } // foreach
        } // if
      } // if

      $this->response->assign(array(
        'search_settings_data' => $search_settings_data,
        'available_providers' => $providers,
      ));

      Search::initialize(true);

      if($this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating search settings @ ' . __CLASS__);

          $provider_class = array_var($search_settings_data, 'search_provider', null, true);

          $provider_available = false;
          foreach($providers as $provider) {
            if(get_class($provider) == $provider_class) {
              $provider_available = true;
            } // if

            $provider->setSettings($search_settings_data);
          } // foreach

          if($provider_available) {
            ConfigOptions::setValue('search_provider', $provider_class);
          } // if

          DB::commit('Search settings updated @ ' . __CLASS__);

          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to update search settings @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // index

  }