<?php

  /**
   * Angie application initialization file
   *
   * @package angie.library.application
   */
  
  require_once ANGIE_PATH . '/classes/application/AngieApplication.class.php';
  
  spl_autoload_register(array('AngieApplication', 'autoload'));
  
  require_once ANGIE_PATH . '/classes/application/AngieApplicationAdapter.class.php';
  require_once ANGIE_PATH . '/classes/application/AngieFramework.class.php';
  require_once ANGIE_PATH . '/classes/application/AngieModule.class.php';

  AngieApplication::setForAutoload(array(
    'AngieApplicationModel' => ANGIE_PATH . '/classes/application/model/AngieApplicationModel.class.php', 
    'AngieFrameworkModel' => ANGIE_PATH . '/classes/application/model/AngieFrameworkModel.class.php', 
    'AngieModuleModel' => ANGIE_PATH . '/classes/application/model/AngieModuleModel.class.php', 
    'AngieFrameworkModelBuilder' => ANGIE_PATH . '/classes/application/model/AngieFrameworkModelBuilder.class.php',

    'AngieDelegate' => ANGIE_PATH . '/classes/application/delegates/AngieDelegate.class.php',
    'AngieBehaviourDelegate' => ANGIE_PATH . '/classes/application/behaviour/AngieBehaviourDelegate.class.php',
    'AngieCacheDelegate' => ANGIE_PATH . '/classes/application/delegates/AngieCacheDelegate.class.php',
    'AngieElasticaDelegate' => ANGIE_PATH . '/classes/application/delegates/AngieElasticaDelegate.class.php',
    'AngieExperimentsDelegate' => ANGIE_PATH . '/classes/application/delegates/AngieExperimentsDelegate.class.php',
    'AngieLauncherDelegate' => ANGIE_PATH . '/classes/application/delegates/AngieLauncherDelegate.class.php',
    'AngieMigrationDelegate' => ANGIE_PATH . '/classes/application/delegates/AngieMigrationDelegate.class.php',

    'AngieWidgetLoader' => ANGIE_PATH . '/classes/application/AngieWidgetLoader.class.php',
  ));