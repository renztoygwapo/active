<?php

  /**
   * Framework level control tower implementation
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class FwControlTower {

    /**
     * Show control tower for a given user
     *
     * @var User
     */
    protected $user;

    /**
     * Indicators array
     *
     * @var NamedList
     */
    protected $indicators;

    /**
     * List of tower widgets
     *
     * @var NamedList
     */
    protected $widgets;

    /**
     * Actions array
     *
     * @var NamedList
     */
    protected $actions;

    /**
     * Construct new control tower instance
     *
     * @param User $user
     * @throws InvalidInstanceError
     */
    function __construct(User $user) {
      if($user instanceof User && $user->isAdministrator()) {
        $this->user = $user;
      } else {
        throw new InvalidInstanceError('user', $user, 'User');
      } // if

      $this->indicators = new NamedList();
      $this->widgets = new NamedList();
      $this->actions = new NamedList();
    } // __construct

    /**
     * Load control tower data
     */
    function load() {
      EventsManager::trigger('on_load_control_tower', array(&$this, &$this->user));

      if (!AngieApplication::isOnDemand()) {
        if (ConfigOptions::getValue('control_tower_check_scheduled_tasks') && !AngieApplication::areScheduledTasksRunning()) {
          $this->indicators()->add('scheduled_tasks', array(
            'label' => lang('Scheduled Tasks'),
            'value' => null,
            'url' => Router::assemble('scheduled_tasks_admin'),
            'is_ok' => false,
            'onclick' => new FlyoutCallback(),
          ));
        } // if

        if (ConfigOptions::getValue('control_tower_check_performance') && !AngieApplication::getAdapter()->testPlatformForOptimalPerformance()) {
          $this->indicators()->add('performance_checklist', array(
            'label' => lang('Potential performance issues found'),
            'value' => null,
            'url' => Router::assemble('control_tower_performance_checklist'),
            'is_ok' => false,
          ));
        } // if
      } // if

      if(ConfigOptions::getValue('control_tower_check_disk_usage')) {
        $this->widgets()->add('disk_usage', array(
          'label' => lang('Disk Usage'),
          'renderer' => function() {
            $used_disk_space = 0;
            if(AngieApplication::isOnDemand()) {
              if(OnDemand::getAccountStatus()->getStatus() != OnDemand::STATUS_ACTIVE_FREE) {
                $available_disk_space = DiskSpace::getLimit();
              } //if
            } else {
              $available_disk_space = DiskSpace::getLimit();
            } //if

            $smarty = null;

            AngieApplication::useHelper('progressbar', ENVIRONMENT_FRAMEWORK);
            return '<span id="disk_space_admin_popup_wrapper">' . smarty_function_progressbar(array(
              'max_value'     => $available_disk_space,
              'value'         => $used_disk_space,
              'icon'          => AngieApplication::getImageUrl('control-tower/disk-space.png', ENVIRONMENT_FRAMEWORK),
              'href'          => Router::assemble('disk_space_admin'),
              'label'         => $available_disk_space ? lang('<span>:used</span> of <span>:available</span> used', array(
                'used' => format_file_size($used_disk_space),
                'available' => format_file_size($available_disk_space),
              )) : lang('<span>:used</span> disk space used', array(
                'used' => format_file_size($used_disk_space),
              )),
              'class'         => 'menu_navigation_item'
            ), $smarty) . '</span><script type="text/javascript">App.widgets.diskSpaceAdminPopup.init("disk_space_admin_popup_wrapper", ' . JSON::encode($available_disk_space) . ', ' . JSON::encode(Router::assemble('disk_space_usage')) .');</script>';
          }
        ));
      } // if

      if (!AngieApplication::isOnDemand() && DiskSpace::logsNeedRemoving()) {
        $this->indicators()->add('log_files', array(
          'label' => lang('Log files are taking lots of disk space'),
          'value' => null,
          'url' => Router::assemble('disk_space_admin'),
          'is_ok' => false,
        ));
      } // if

      $this->actions()->add('empty_cache', array(
        'label' => lang('Empty Cache'),
        'success_message' => lang('Application cache has been cleared'),
        'error_message' => lang('Failed to clear application cache'),
        'url' => Router::assemble('control_tower_empty_cache'),
        'icon' => AngieApplication::getImageUrl('icons/16x16/proceed.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT),
      ));

      if (!AngieApplication::isOnDemand()) {
        $this->actions()->add('clear_compiled_templates', array(
          'label' => lang('Clear Compiled Templates'),
          'success_message' => lang('Compiled templates have been cleared'),
          'error_message' => lang('Failed to clear compiled templates'),
          'url' => Router::assemble('control_tower_delete_compiled_templates'),
          'icon' => AngieApplication::getImageUrl('icons/16x16/proceed.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT),
        ));
      } // if

      if (!(defined('PROTECT_ASSETS_FOLDER') && PROTECT_ASSETS_FOLDER) && !AngieApplication::isOnDemand() || (AngieApplication::isOnDemand() && (AngieApplication::isInDevelopment() || AngieApplication::isInDebugMode()))) {
        $this->actions()->add('rebuild_assets', array(
          'label' => lang('Rebuild Assets (Images, Fonts, Flash files etc)'),
          'success_message' => lang('Application assets have been rebuilt'),
          'error_message' => lang('Failed to rebuild application assets'),
          'url' => Router::assemble('control_tower_rebuild_images'),
          'icon' => AngieApplication::getImageUrl('icons/16x16/proceed.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT),
        ));

        $this->actions()->add('rebuild_localization', array(
          'label' => lang('Rebuild Localization Dictionaries'),
          'success_message' => lang('Localization dictionaries have been rebuilt'),
          'error_message' => lang('Failed to rebuild localization dictionaries'),
          'url' => Router::assemble('control_tower_rebuild_localization'),
          'icon' => AngieApplication::getImageUrl('icons/16x16/proceed.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT),
        ));
      } // if
    } // load

    /**
     * Cached badge value
     *
     * @var integer
     */
    private $badge_value = false;

    /**
     * Load and return badge value
     */
    function loadBadgeValue() {
      if($this->badge_value === false) {
        $badge_value = 0;

        if (!AngieApplication::isOnDemand()) {
          if (ConfigOptions::getValue('control_tower_check_scheduled_tasks') && !AngieApplication::areScheduledTasksRunning()) {
            $badge_value++;
          } // if

          if (ConfigOptions::getValue('control_tower_check_performance') && !AngieApplication::getAdapter()->testPlatformForOptimalPerformance()) {
            $badge_value++;
          } // if
        } // if

        if (!AngieApplication::isOnDemand() && DiskSpace::logsNeedRemoving()) {
          $badge_value++;
        } // if

        if(ConfigOptions::getValue('control_tower_check_disk_usage') && DiskSpace::isUsageLimitReached()) {
          $badge_value++;
        } // if

        EventsManager::trigger('on_load_control_tower_badge', array(&$badge_value, &$this->user));

        $this->badge_value = $badge_value;
      } // if

      return $this->badge_value;
    } // loadBadgeValue

    /**
     * Control tower settings
     *
     * @var mixed
     */
    protected $settings = false;

    /**
     * Get control tower settings
     *
     * @return array
     */
    function getSettings() {
      if($this->settings === false) {
        $system = lang('System');

        if(!AngieApplication::isOnDemand()) {
          $settings[$system]['control_tower_check_scheduled_tasks'] = array(
            'label' => lang('Check Scheduled Tasks'),
            'value' => ConfigOptions::getValue('control_tower_check_scheduled_tasks'),
          );

          $settings[$system]['control_tower_check_performance'] = array(
            'label' => lang('Check for Potential Performance Issues'),
            'value' => ConfigOptions::getValue('control_tower_check_performance'),
          );
        } //if

        $settings[$system]['control_tower_check_disk_usage'] = array(
          'label' => lang('Check Disk Space Usage'),
          'value' => ConfigOptions::getValue('control_tower_check_disk_usage'),
        );

        EventsManager::trigger('on_load_control_tower_settings', array(&$settings, &$this->user));

        $this->settings = $settings;
      } // if

      return $this->settings;
    } // getSettings
    
    /**
     * Return indicators list
     *
     * @return NamedList
     */
    function &indicators() {
      return $this->indicators;
    } // indicators

    /**
     * Return widgets list
     *
     * @return NamedList
     */
    function &widgets() {
      return $this->widgets;
    } // widgets

    /**
     * Return actions list
     *
     * @return NamedList
     */
    function &actions() {
      return $this->actions;
    } // actions

    // ---------------------------------------------------
    //  Renderers
    // ---------------------------------------------------

    /**
     * Render control tower
     *
     * @return string
     */
    function render() {
      return $this->renderIndicators() . $this->renderWidgets() . $this->renderActions();
    } // render

    /**
     * Render indicators and counts
     *
     * @return string
     */
    private function renderIndicators() {
      if($this->indicators->count()) {
        $result= '<ul class="control_tower_indicators menu_navigation_group">';

        foreach($this->indicators as $name => $indicator) {
          $classes = isset($indicator['is_ok']) && $indicator['is_ok'] ? 'control_tower_indicator ok' : 'control_tower_indicator nok';
          $classes.= ' menu_navigation_item';

          $result .= '<li class="menu_navigation_row"><a href="' . clean($indicator['url']) . '" class="' . $classes . '" id="control_tower_indicator_' . clean($name) . '">
            <span class="indicator_value_wrapper"><span class="indicator_value"><span class="indicator_value_inner">' . ($indicator['value'] !== null ? clean($indicator['value']) : '&nbsp') . '</span></span></span>
            <span class="indicator_label">' . clean($indicator['label']) . '</span>
          </a></li>';
        } // foreach

        return $result . '</ul>';
      } // if

      return '';
    } // renderIndicators

    /**
     * Render widgets
     *
     * @return string
     */
    function renderWidgets() {
      if($this->widgets->count()) {
        $result = '';

        $result.= '<div class="control_tower_widgets menu_navigation_group"><div class="control_tower_widgets_inner">';

        $render_widget = function ($name, $widget) {
          $rendered_widget = '<div class="control_tower_widget menu_navigation_row" id="control_tower_widget_' . clean($name) . '">';

          if(isset($widget['renderer']) && $widget['renderer'] instanceof Closure) {
            $rendered_widget .= $widget['renderer']->__invoke();
          } // if

          $rendered_widget .= '</div>';

          return $rendered_widget;
        };

        // render standard widgets
        foreach($this->widgets as $name => $widget) {
         $result .= $render_widget($name, $widget);
        } // foreach

        $result.= '</div></div>';

        return $result;
      } // if

      return '';
    } // renderWidgets

    /**
     * Render actions
     *
     * @return string
     */
    private function renderActions() {
      if($this->actions->count()) {
        $result = '<ul class="popup_item_list menu_navigation_group control_tower_quick_actions">';
        foreach($this->actions() as $name => $action) {
          $result.= '<li class="menu_navigation_row menu_navigation_item"><a href="' . clean($action['url']) . '" success_message="' . clean($action['success_message']) . '" error_message="' . clean($action['error_message']) . '" class="do_not_close"><img src="' . clean($action['icon']) . '">' . clean($action['label']) . '</a></li>';
        } // foreach
        $result.= '</ul>';
        return $result;
      } // if

      return '';
    } // renderActions

  }