<?php

  /**
   * Angie application model
   *
   * @package angie.library.application
   */
  class AngieApplicationModel {
    
    /**
     * List of loaded models
     *
     * @var array
     */
    static private $models = array();
    
    /**
     * List of loaded frameworks
     *
     * @var array
     */
    static private $loaded_frameworks = array();
    
    /**
     * Cached array of modules that we loaded
     *
     * @var array
     */
    static private $loaded_modules = array();
    
    /**
     * Load framework and module models
     *
     * @param array $framework_names
     * @param array $module_names
     */
    static public function load($framework_names, $module_names) {
      if(count(self::$loaded_frameworks) && count(self::$loaded_modules)) {
        return; // Model already loaded
      } // if

      // Load framework models
      foreach($framework_names as $framework_name) {
        $framework_class = Inflector::camelize($framework_name) . 'Framework';

        $file = ANGIE_PATH . "/frameworks/$framework_name/$framework_class.class.php";
        if(is_file($file)) {
          require_once $file;

          $framework = new $framework_class();
          if($framework instanceof AngieFramework) {
            self::$loaded_frameworks[] = $framework_name;

            if($framework->getModel() instanceof AngieFrameworkModel) {
              self::$models[$framework->getName()] = $framework->getModel();
            } // if
          } // if
        } // if
      } // foreach

      // Load module models
      foreach($module_names as $module_name) {
        $module_class = Inflector::camelize($module_name) . 'Module';

        $file = APPLICATION_PATH . "/modules/$module_name/$module_class.class.php";

        if(!is_file($file)) {
          $file = CUSTOM_PATH . "/modules/$module_name/$module_class.class.php";
        } // if

        if(is_file($file)) {
          require_once $file;

          $module = new $module_class();
          if($module instanceof AngieModule) {
            self::$loaded_modules[] = $module_name;

            if($module->getModel() instanceof AngieModuleModel) {
              self::$models[$module->getName()] = $module->getModel();
            } // if
          } // if
        } // if
      } // foreach
    } // load
    
    /**
     * Initialize all loaded frameworks and modules for given environment
     *
     * @param string $environment
     */
    static public function init($environment = null) {
      foreach(self::$models as &$model) {
        $model->createTables($environment);
      } // foreach
      unset($model);
      
      foreach(self::$models as &$model) {
        $model->loadInitialData($environment);
      } // foreach
      unset($model);
      
      // If model did not add any modules, insert modules from based on 
      // APPLICATION_MODULES value
      if(DB::executeFirstCell('SELECT COUNT(name) FROM ' . TABLE_PREFIX . 'modules') == 0) {
        $modules = array();
        
        $counter = 1;
        foreach(self::$loaded_modules as $module) {
          $modules[] = DB::prepare('(?, ?, ?)', $module, true, $counter++);
        } // foreach
        
        if(count($modules)) {
          DB::execute('INSERT INTO ' . TABLE_PREFIX . 'modules (name, is_enabled, position) VALUES ' . implode(', ', $modules));
        } // if
      } // if

      $paths_to_scan = array(ANGIE_PATH . '/migrations');

      foreach(DB::executeFirstColumn('SELECT name FROM ' . TABLE_PREFIX . 'modules') as $module_name) {
        $paths_to_scan[] = APPLICATION_PATH . '/modules/' . $module_name . '/migrations';
      } // foreach

      if(!class_exists('AngieModelMigrationDiscoverer', false) && !class_exists('AngieModelMigration', false)) {
        require_once ANGIE_PATH . '/classes/application/migrations/AngieModelMigration.class.php';
        require_once ANGIE_PATH . '/classes/application/migrations/AngieModelMigrationDiscoverer.class.php';
      } // if

      foreach(AngieModelMigrationDiscoverer::discoverFromPaths($paths_to_scan) as $scripts) {
        foreach($scripts as $script) {
          $script->setAsExecuted();
        } // foreach
      } // foreach
    } // init
    
    /**
     * Drop model
     */
    static public function drop() {
      if(count(self::$loaded_frameworks) && count(self::$loaded_modules)) {
        foreach(AngieApplicationModel::getTables() as $table) {
          if($table->exists(TABLE_PREFIX)) {
            $table->delete(TABLE_PREFIX);
          } // if
        } // foreach
      } else {
        throw new Error('Model not loaded');
      } // if
    } // drop
    
    /**
     * Revert to model's original state
     *
     * @param string $environment
     */
    static public function revert($environment = null) {
    	self::drop();
      self::init($environment);
    } // revert
    
    /**
     * Returns true if this model is empty (there are no model instances loaded)
     *
     * @return boolean
     */
    static public function isEmpty() {
      return empty(self::$models);
    } // isEmpty
    
    // ---------------------------------------------------
    //  Getters
    // ---------------------------------------------------
    
    /**
     * Return all tables
     *
     * @return DBTable[]
     */
    static public function getTables() {
      $tables = array();
      
      foreach(self::$models as $model) {
        foreach($model->getTables() as $k => $v) {
          $tables[$k] = $v;
        } // foreach
      } // foreach
      
      return $tables;
    } // getTables

    /**
     * Return specific table
     *
     * @param string $table_name
     * @return DBTable
     * @throws InvalidParamError
     * @throws Exception
     */
    static public function &getTable($table_name) {
      foreach(self::$models as $model) {
        try {
          $table = $model->getTable($table_name);
          if($table instanceof DBTable) {
            return $table;
          } // if
        } catch(InvalidParamError $e) {
          // Skip name error
        } catch(Exception $e) {
          throw $e;
        } // try
      } // foreach
      
      throw new InvalidParamError('table_name', $table_name, "Table '$table_name' is not defined in any of the models");
    } // getTable
    
    /**
     * Return all model builders
     *
     * @return array
     */
    static public function getModelBuilders() {
      $model_builders = array();
      
      foreach(self::$models as $model) {
        foreach($model->getModelBuilders() as $k => $v) {
          $model_builders[$k] = $v;
        } // foreach
      } // foreach
      
      return $model_builders;
    } // getModelBuilders
    
    /**
     * Return model builder for specific table
     *
     * @param string $for_table_name
     * @return AngieFrameworkModelBuilder
     * @throws InvalidParamError
     * @throws Exception
     */
    static public function &getModelBuilder($for_table_name) {
      foreach(self::$models as $model) {
        try {
          $model_builder = $model->getModelBuilder($for_table_name);
          if($model_builder instanceof AngieFrameworkModelBuilder) {
            return $model_builder;
          } // if
        } catch(InvalidParamError $e) {
          // Skip name error
        } catch(Exception $e) {
          throw $e;
        } // try
      } // foreach
      
      throw new InvalidParamError('for_table_name', $for_table_name, "Model builder is not defined for '$for_table_name' table in any of the models");
    } // getModelBuilder
    
  }