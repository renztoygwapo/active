<?php

  /**
   * Application level control tower implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ControlTower extends FwControlTower {

    /**
     * Get control tower settings
     *
     * @return array
     */
    function getSettings() {
      parent::getSettings();

      if ($this->settings === false) {
        $this->settings = array();
      } // if

      $system = lang('System');

      if (!isset($this->settings[$system])) {
        $this->settings[$system] = array();
      } // if

      if(!AngieApplication::isOnDemand()) {
        $this->settings[$system]['control_tower_check_for_new_version'] = array(
          'label' => lang('Check for New activeCollab Version'),
          'value' => ConfigOptions::getValue('control_tower_check_for_new_version'),
        );
      } //if
      return $this->settings;
    } // getSettings


  }