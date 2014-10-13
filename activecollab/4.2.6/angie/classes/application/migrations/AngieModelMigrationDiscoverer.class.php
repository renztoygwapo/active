<?php

  /**
   * Angie model migration discoverer
   *
   * NOTE: This class needs to be fully independent because it is used by upgrade script as well
   *
   * @package angie.library.application
   * @subpackage model
   */
  final class AngieModelMigrationDiscoverer {

    /**
     * Discover migrations for a given version
     *
     * @param string $for_version
     * @return AngieModelMigration[]
     * @throws Error
     */
    static function discover($for_version = null) {
      $paths_to_scan = array();

      if($for_version) {
        $paths_to_scan[] = ROOT . '/' . $for_version . '/angie/migrations';
      } else {
        $paths_to_scan[] = ANGIE_PATH . '/migrations';
      } // if

      foreach(AngieApplication::getInstalledModules() as $module) {
        if($for_version) {
          $module_migrations_path = ROOT . '/' . $for_version . '/modules/' . $module->getName() . '/migrations';
        } else {
          $module_migrations_path = $module->getPath() . '/migrations';
        } // if

        if(is_dir($module_migrations_path)) {
          $paths_to_scan[] = $module_migrations_path;
        } // if
      } // foreach

      return AngieModelMigrationDiscoverer::discoverFromPaths($paths_to_scan);
    } // discover

    /**
     * Discover migrations in list of paths
     *
     * @param array $paths_to_scan
     * @return AngieModelMigration[]
     * @throws Error
     */
    static function discoverFromPaths($paths_to_scan) {
      $result = array();

      foreach($paths_to_scan as $path) {
        $scripts_dirs = get_folders($path);

        if(is_array($scripts_dirs)) {
          sort($scripts_dirs);

          foreach($scripts_dirs as $scripts_dir) {
            $changeset = basename($scripts_dir);

            if(self::isValidScriptsDirName($changeset)) {
              $files = get_files($scripts_dir, 'php');

              if(is_array($files)) {
                foreach($files as $file) {
                  $basename = basename($file);

                  if(str_starts_with($basename, 'Migrate') && str_ends_with($basename, '.class.php')) {
                    require_once $file;

                    $class_name = first(explode('.', $basename));

                    if(class_exists($class_name, false)) {
                      if(empty($result[$changeset])) {
                        $result[$changeset] = array();
                      } // if

                      $migration = new $class_name();

                      if($migration instanceof AngieModelMigration) {
                        $result[$changeset][get_class($migration)] = $migration;
                      } // if
                    } else {
                      throw new Error("Class '$class_name' definition not found in '$file");
                    } // if
                  } else {
                    throw new Error("Migration definition file '$file' is not properly named");
                  } // if
                } // foreach
              } // if
            } // if
          } // foreach
        } // if
      } // foreach

      ksort($result);

      return $result;
    } // discoverFromPaths

    /**
     * Check if name is valid scripts dir
     *
     * @param $name
     * @return bool
     */
    static private function isValidScriptsDirName($name) {
      return (boolean) preg_match('/^(\d{4})-(\d{2})-(\d{2})-(.*)$/', $name);
    } // isValidScriptsDirName

  }