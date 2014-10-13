<?php

  /**
   * Data object pool
   *
   * Static class that's used to cache object instancess acorss the application
   *
   * @package angie.library.database
   */
  final class DataObjectPool {

    /**
     * Cache all objects in this variable, indexed by type and ID
     *
     * @var array
     */
    static $pool = array();

    /**
     * Return object by type -> id pair
     *
     * @param string $type
     * @param integer $id
     * @param Closure $alternative
     * @param boolean $force_reload
     * @return DataObject
     */
    static function &get($type, $id, $alternative = null, $force_reload = false) {
      if($id) {
        if(isset(self::$pool[$type]) && isset(self::$pool[$type][$id]) && empty($force_reload)) {
          return self::$pool[$type][$id];
        } else {
          $object = null;

          if(AngieApplication::classExists($type, true, true)) {
            $object = DataObjectPool::loadById($type, $id);
          } // if

          if($object instanceof DataObject && $object->isLoaded()) {
            self::$pool[$type][$id] = $object;
          } else {
            self::$pool[$type][$id] = null;
          } // if

          return self::$pool[$type][$id];
        } // if
      } // if

      if($alternative instanceof Closure) {
        $result = $alternative();
      } else {
        $result = $alternative;
      } // if

      return $result;
    } // get

    /**
     * set object cache
     *
     * @param DataObject $object
     */
    static function set($object) {
      self::$pool[get_class($object)][$object->getId()] = $object;
    } // set

    /**
     * Remove object from the pool
     *
     * @param string $type
     * @param integer $id
     */
    static function forget($type, $id) {
      if(isset(self::$pool[$type]) && isset(self::$pool[$type][$id])) {
        unset(self::$pool[$type][$id]);
      } // if
    } // forget

    /**
     * Return objects by type -> ids map
     *
     * @param array $map
     * @return array
     */
    static function getByTypeIdsMap($map) {
      if($map && is_foreachable($map)) {
        $result = array();

        foreach($map as $type => $ids) {
          $result[$type] = self::getByIds($type, $ids);

          if(empty($result[$type])) {
            unset($result[$type]);
          } // if
        } // foreach

        return $result;
      } else {
        return null;
      } // if
    } // getByTypeIdsMap

    /**
     * Registered type loaders
     *
     * @var array
     */
    static $type_loaders = array();

    /**
     * Register type loader
     *
     * $type can be a signle type or an array of types
     *
     * $callback can be a closure or callback array
     *
     * @param array|string $type
     * @param mixed $callback
     */
    static function registerTypeLoader($type, $callback) {
      if(is_array($type)) {
        foreach($type as $v) {
          self::$type_loaders[$v] = $callback;
        } // foreach
      } else {
        self::$type_loaders[$type] = $callback;
      } // if
    } // registerTypeLoader

    /**
     * Return type loader
     *
     * @param string $type
     * @return Closure|null
     */
    static function getTypeLoader($type) {
      return isset(self::$type_loaders[$type]) && self::$type_loaders[$type] instanceof Closure ? self::$type_loaders[$type] : null;
    } // getTypeLoader

    /**
     * Load first object by ID
     *
     * @param string $type
     * @param integer $id
     * @return DataObject
     */
    static private function loadById($type, $id) {
      $type_loader = self::getTypeLoader($type);

      if($type_loader) {
        $loader_result = $type_loader(array($id));

        if($loader_result) {
          foreach($loader_result as $v) {
            return $v;
          } // foreach
        } // if
      } // if

      return null;
    } // loadById

    /**
     * Return instances by $type and $ids
     *
     * @param $type
     * @param $ids
     * @return array|null
     */
    static function getByIds($type, $ids) {
      $type_loader = self::getTypeLoader($type); // isset(self::$type_loaders[$type]) && self::$type_loaders[$type] instanceof Closure ? self::$type_loaders[$type] : null;

      if($type_loader) {
        $loader_result = $type_loader($ids);

        if($loader_result) {
          $objects = array();

          foreach($loader_result as $v) {
            $objects[$v->getId()] = $v;
          } // foreach
        } else {
          $objects = null;
        } // if
      } else {
        $objects = array();

        foreach($ids as $id) {
          $object = self::get($type, $id);

          if($object) {
            $objects[$id] = $object;
          } // if
        } // foreach
      } // if

      return empty($objects) ? null : $objects;
    } // getByIds

    /**
     * Clear data object pool
     */
    static function clear() {
      self::$pool = array();
    } // clear

  }