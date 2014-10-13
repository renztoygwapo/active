<?php

  /**
   * Environments framework intialization file
   *
   * @package angie.frameworks.environments
   */

  const ENVIRONMENT_FRAMEWORK = 'environment';
  const ENVIRONMENT_FRAMEWORK_PATH = __DIR__;

  // Inject environment framework into given module
  defined('ENVIRONMENT_FRAMEWORK_INJECT_INTO') or define('ENVIRONMENT_FRAMEWORK_INJECT_INTO', 'system');

  // define path to custom ca file
  defined('JSON_API_COMPATIBILITY_RESPONSE') or define('JSON_API_COMPATIBILITY_RESPONSE', false);
  defined('VERIFY_APPLICATION_VENDOR_SSL') or define('VERIFY_APPLICATION_VENDOR_SSL', true);
  defined('CUSTOM_CA_FILE') or define('CUSTOM_CA_FILE', ENVIRONMENT_FRAMEWORK_PATH . '/resources/ca-bundle.crt');

  // Environment functions
  require_once ENVIRONMENT_FRAMEWORK_PATH . '/functions.php';

  // Project object visibility
  const VISIBILITY_PRIVATE = 0;
  const VISIBILITY_NORMAL = 1;
  const VISIBILITY_PUBLIC = 2;

  // Available application object states
  const STATE_DELETED = 0;
  const STATE_TRASHED = 1;
  const STATE_ARCHIVED = 2;
  const STATE_VISIBLE = 3;

  // Project object priority
  const PRIORITY_LOWEST = -2;
  const PRIORITY_LOW = -1;
  const PRIORITY_NORMAL = 0;
  const PRIORITY_HIGH = 1;
  const PRIORITY_HIGHEST = 2;

  // Scheduled task types
  const SCHEDULED_TASK_FREQUENTLY = 'frequently';
  const SCHEDULED_TASK_HOURLY = 'hourly';
  const SCHEDULED_TASK_DAILY = 'daily';

  // Charts
  const NON_WORK_DAY_COLOR_CHART = '#F7F7F7';
  const DAY_OFF_COLOR_CHART = '#FFEDED';

  AngieApplication::setForAutoload(array(
    'ConfigOptions' => ENVIRONMENT_FRAMEWORK_PATH . '/models/config_options/ConfigOptions.class.php',
    'IConfigContext' => ENVIRONMENT_FRAMEWORK_PATH . '/models/config_options/IConfigContext.class.php',
    'ConfigOptionDnxError' => ENVIRONMENT_FRAMEWORK_PATH . '/models/config_options/errors/ConfigOptionDnxError.class.php',

    'IObjectContext' => ENVIRONMENT_FRAMEWORK_PATH . '/models/IObjectContext.class.php',
    'AngieDescribeDelegate' => ENVIRONMENT_FRAMEWORK_PATH . '/models/AngieDescribeDelegate.class.php',

    'FwModule' => ENVIRONMENT_FRAMEWORK_PATH . '/models/modules/FwModule.class.php',
    'FwModules' => ENVIRONMENT_FRAMEWORK_PATH . '/models/modules/FwModules.class.php',

    // Response
    'WebInterfaceResponse' => ENVIRONMENT_FRAMEWORK_PATH . '/models/response/WebInterfaceResponse.class.php',
    'FwBackendWebInterfaceResponse' => ENVIRONMENT_FRAMEWORK_PATH . '/models/response/FwBackendWebInterfaceResponse.class.php',
    'FwFrontendWebInterfaceResponse' => ENVIRONMENT_FRAMEWORK_PATH . '/models/response/FwFrontendWebInterfaceResponse.class.php',

    // Wireframes
    'FwWireframe' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/FwWireframe.class.php',
    'FwFrontendWireframe' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/FwFrontendWireframe.class.php',

    'FwBackendWireframe' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/FwBackendWireframe.class.php',
    'FwWebBrowserBackendWireframe' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/FwWebBrowserBackendWireframe.class.php',
    'FwTabletBackendWireframe' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/FwTabletBackendWireframe.class.php',
    'FwPhoneBackendWireframe' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/FwPhoneBackendWireframe.class.php',

    // Wireframe elements
    'IWireframeElement' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/elements/IWireframeElement.class.php',
    'WireframeActions' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/elements/WireframeActions.class.php',
    'WireframeAction' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/elements/WireframeAction.class.php',
    'WireframeBreadcrumbs' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/elements/WireframeBreadcrumbs.class.php',
    'WireframeFeeds' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/elements/WireframeFeeds.class.php',
    'WireframePrint' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/elements/WireframePrint.class.php',
    'WireframeTabs' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/elements/WireframeTabs.class.php',
    'WireframeListMode' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/elements/WireframeListMode.class.php',

    'DefaultWireframeActions' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/elements/default/DefaultWireframeActions.class.php',

    'PhoneWireframeBreadcrumbs' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/elements/phone/PhoneWireframeBreadcrumbs.class.php',
    'PhoneWireframeActions' => ENVIRONMENT_FRAMEWORK_PATH . '/models/wireframe/elements/phone/PhoneWireframeActions.class.php',

    'FwApplicationObject' => ENVIRONMENT_FRAMEWORK_PATH . '/models/application_objects/FwApplicationObject.class.php',
    'FwApplicationObjects' => ENVIRONMENT_FRAMEWORK_PATH . '/models/application_objects/FwApplicationObjects.class.php',

    // Errors
    'InMaintenanceModeError' => ENVIRONMENT_FRAMEWORK_PATH . '/models/errors/InMaintenanceModeError.class.php',

    // Access Log
    'FwAccessLog' => ENVIRONMENT_FRAMEWORK_PATH . '/models/access_logs/FwAccessLog.class.php',
    'FwAccessLogs' => ENVIRONMENT_FRAMEWORK_PATH . '/models/access_logs/FwAccessLogs.class.php',

    'IAccessLog' => ENVIRONMENT_FRAMEWORK_PATH . '/models/access_logs/IAccessLog.class.php',
    'IAccessLogImplementation' => ENVIRONMENT_FRAMEWORK_PATH . '/models/access_logs/IAccessLogImplementation.class.php',

    // Created by
    'ICreatedBy' => ENVIRONMENT_FRAMEWORK_PATH . '/models/ICreatedBy.class.php',
    'ICreatedByImplementation' => ENVIRONMENT_FRAMEWORK_PATH . '/models/ICreatedByImplementation.class.php',

    // State
    'IState' => ENVIRONMENT_FRAMEWORK_PATH . '/models/state/IState.class.php',
    'IStateImplementation' => ENVIRONMENT_FRAMEWORK_PATH . '/models/state/IStateImplementation.class.php',

    // Visibility
    'IVisibility' => ENVIRONMENT_FRAMEWORK_PATH . '/models/visibility/IVisibility.class.php',
    'IVisibilityImplementation' => ENVIRONMENT_FRAMEWORK_PATH . '/models/visibility/IVisibilityImplementation.class.php',

    // Inspector
    'IInspector' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/IInspector.class.php',
    'IInspectorImplementation' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/IInspectorImplementation.class.php',
    'InspectorElement' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/InspectorElement.class.php',
    'InspectorIndicator' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/InspectorIndicator.class.php',
    'InspectorProperty' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/InspectorProperty.class.php',
    'InspectorBar' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/InspectorBar.class.php',
    'InspectorWidget' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/InspectorWidget.class.php',
    'SimpleFieldInspectorProperty' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/SimpleFieldInspectorProperty.class.php',
    'SimplePermalinkInspectorProperty' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/SimplePermalinkInspectorProperty.class.php',
    'SimpleBooleanInspectorProperty' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/SimpleBooleanInspectorProperty.class.php',
    'ActionOnByInspectorProperty' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/ActionOnByInspectorProperty.class.php',
    'StateInspectorBar' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/StateInspectorBar.class.php',
    'HyperlinkInspectorIndicator' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/HyperlinkInspectorIndicator.class.php',
    'MoneyFieldInspectorProperty' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/MoneyFieldInspectorProperty.class.php',
    'PermalinkInspectorTitlebarWidget' => ENVIRONMENT_FRAMEWORK_PATH . '/models/inspector/PermalinkInspectorTitlebarWidget.class.php',

    // JavaScript callbacks
    'AsyncLinkCallback' => ENVIRONMENT_FRAMEWORK_PATH . '/models/javascript_callbacks/AsyncLinkCallback.class.php',
    'AsyncTogglerCallback' => ENVIRONMENT_FRAMEWORK_PATH . '/models/javascript_callbacks/AsyncTogglerCallback.class.php',
    'FlyoutCallback' => ENVIRONMENT_FRAMEWORK_PATH . '/models/javascript_callbacks/FlyoutCallback.class.php',
    'FlyoutFormCallback' => ENVIRONMENT_FRAMEWORK_PATH . '/models/javascript_callbacks/FlyoutFormCallback.class.php',
    'TargetBlankCallback' => ENVIRONMENT_FRAMEWORK_PATH . '/models/javascript_callbacks/TargetBlankCallback.class.php',

    // Main menu
    'FwMainMenu' => ENVIRONMENT_FRAMEWORK_PATH . '/models/FwMainMenu.class.php',

    // Status bar
    'FwStatusBar' => ENVIRONMENT_FRAMEWORK_PATH . '/models/FwStatusBar.class.php',

    // Admin panel
    'FwAdminPanel' => ENVIRONMENT_FRAMEWORK_PATH . '/models/admin_panel/FwAdminPanel.class.php',
    'IAdminPanelRow' => ENVIRONMENT_FRAMEWORK_PATH . '/models/admin_panel/IAdminPanelRow.class.php',
    'ToolsAdminPanelRow' => ENVIRONMENT_FRAMEWORK_PATH . '/models/admin_panel/ToolsAdminPanelRow.class.php',

    // Control Tower
    'FwControlTower' => ENVIRONMENT_FRAMEWORK_PATH . '/models/FwControlTower.class.php',

    // color schemes
    'FwColorSchemes' => ENVIRONMENT_FRAMEWORK_PATH . '/models/FwColorSchemes.class.php',

    // Control Tower
    'FwDiskSpace' => ENVIRONMENT_FRAMEWORK_PATH . '/models/FwDiskSpace.class.php',

    // Trash
    'FwTrash' => ENVIRONMENT_FRAMEWORK_PATH . '/models/FwTrash.class.php',

    // Mass Manager
    'FwMassManager' => ENVIRONMENT_FRAMEWORK_PATH . '/models/FwMassManager.class.php',

    // Charts
    'ChartPoint' => ENVIRONMENT_FRAMEWORK_PATH . '/models/charts/ChartPoint.class.php',
    'ChartSerie' => ENVIRONMENT_FRAMEWORK_PATH . '/models/charts/ChartSerie.class.php',
    'Chart' => ENVIRONMENT_FRAMEWORK_PATH . '/models/charts/Chart.class.php',
    'LineChart' => ENVIRONMENT_FRAMEWORK_PATH . '/models/charts/LineChart.class.php',
    'BarChart' => ENVIRONMENT_FRAMEWORK_PATH . '/models/charts/BarChart.class.php',
    'PieChart' => ENVIRONMENT_FRAMEWORK_PATH . '/models/charts/PieChart.class.php',

    //notification
    'DiskSpaceNotification' => ENVIRONMENT_FRAMEWORK_PATH . '/notifications/DiskSpaceNotification.class.php',
    'LowDiskSpaceNotification' => ENVIRONMENT_FRAMEWORK_PATH . '/notifications/LowDiskSpaceNotification.class.php',
    'DiskSpaceQuotaReachedNotification' => ENVIRONMENT_FRAMEWORK_PATH . '/notifications/DiskSpaceQuotaReachedNotification.class.php',

	  // Firewall
	  'AngieFirewallDelegate' => ENVIRONMENT_FRAMEWORK_PATH . '/models/AngieFirewallDelegate.class.php',

	  // Errors
	  'FirewallError' => ENVIRONMENT_FRAMEWORK_PATH . '/errors/FirewallError.class.php',
  ));