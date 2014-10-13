<?php

  use Stash\Pool;
  use Stash\Driver\FileSystem;
  use Stash\Driver\Memcache;
  use Stash\Driver\Apc;

  /**
   * Angie cache delegate implementation
   *
   * @package angie.library.application
   * @subpackage delegates
   */
  class AngieCacheDelegate extends AngieDelegate {

    // List of supported backends
    const FILESYSTEM_BACKEND = 'filesystem';
    const MEMCACHED_BACKEND = 'memcached';
    const APC_BACKEND = 'apc';

    /**
     * Return true if $key is cached
     *
     * @param mixed $key
     * @return bool
     */
    function isCached($key) {
      $stash = $this->getStash($this->getKey($key));
      $stash->get();

      return !$stash->isMiss();
    } // isCached

    /**
     * Return value for a given key
     *
     * @param string $key
     * @param mixed $default
     * @param bool $force_refresh
     * @param int|null $lifetime
     * @return mixed|null
     */
    function get($key, $default = null, $force_refresh = false, $lifetime = null) {
      $stash = $this->getStash($this->getKey($key));

      $data = $stash->get();

      if($force_refresh || $stash->isMiss()) {
        if($force_refresh) {
          $this->logForceRefresh($key);
        } else {
          $this->logMiss($key);
        } // if

        $data = $default instanceof Closure ? $default->__invoke() : $default;
        $stash->set($data, $this->getLifetime($lifetime));
      } else {
        $this->logHit($key);
      } // if

      return $data;
    } // get

    /**
     * Return by object
     *
     * @param object|array $object
     * @param string $sub_namespace
     * @param Closure|mixed $default
     * @param boolean $force_refresh
     * @param integer $lifetime
     * @return mixed
     * @throws InvalidParamError
     */
    function getByObject($object, $sub_namespace = null, $default = null, $force_refresh = false, $lifetime = null) {
      if($this->isValidObject($object)) {
        return $this->get($this->getCacheKeyForObject($object, $sub_namespace), $default, $force_refresh, $lifetime);
      } else {
        throw new InvalidParamError('object', $object, '$object is not a valid cache context');
      } // if
    } // getByModel

    /**
     * Return true if $object is instance that we can work with
     *
     * @param object $object
     * @return bool
     */
    function isValidObject($object) {
      if($object instanceof DataObject) {
        return $object->isLoaded();
      } elseif(is_array($object) && count($object) == 2) {
        return true;
      } else {
        return is_object($object) && method_exists($object, 'getId') && method_exists($object, 'getModelName') && $object->getId();
      } // if
    } // isValidObject

    /**
     * Cache given value
     *
     * @param mixed $key
     * @param mixed $value
     * @param mixed $lifetime
     * @return mixed
     */
    function set($key, $value, $lifetime = null) {
      $this->getStash($this->getKey($key))->set($value, $this->getLifetime($lifetime));

      return $value;
    } // set

    /**
     * Set value by given object
     *
     * @param object|array $object
     * @param mixed $sub_namespace
     * @param mixed $value
     * @param integer $lifetime
     * @return mixed
     */
    function setByObject($object, $sub_namespace = null, $value, $lifetime = null) {
      if($this->isValidObject($object)) {
        return $this->set($this->getCacheKeyForObject($object, $sub_namespace), $value, $lifetime);
      } else {
        return false; // Not supported for objects that are not persisted
      } // if
    } // setByObject

    /**
     * Remove value and all sub-nodes
     *
     * @param $key
     */
    function remove($key) {
      $this->getStash($key)->clear();
    } // remove

    /**
     * Remove data by given object
     *
     * $sub_namespace let you additionally specify which part of object's cache should be removed, instead of entire
     * object cache. Example:
     *
     * AngieApplication::cache()->removeByObject($user, 'permissions_cache');
     *
     * @param $object
     * @param mixed $sub_namespace
     */
    function removeByObject($object, $sub_namespace = null) {
      $this->remove($this->getCacheKeyForObject($object, $sub_namespace));
    } // removeByObject

    /**
     * Remove model name
     *
     * @param string $model_name
     */
    function removeByModel($model_name) {
      $this->remove(array('models', $model_name));
    } // removeByModel

    /**
     * Clear entire cache
     */
    function clear() {
      $this->getPool()->flush();
    } // clear

    /**
     * Clear model cache
     */
    function clearModelCache() {
      $this->remove('models');
    } // clearModelCache

    // ---------------------------------------------------
    //  Data Object Related
    // ---------------------------------------------------

    /**
     * Return cache key for given object
     *
     * This function receives either an object instance, or array where first element is model name and second is
     * object ID
     *
     * Optional $sub_namespace can be used to additionally dig into object's cache. String value and array of string
     * values are accepted
     *
     * @param object $object
     * @param mixed $subnamespace
     * @return array
     * @throws InvalidParamError
     */
    function getCacheKeyForObject($object, $subnamespace = null) {

      // Data object
      if($object instanceof DataObject) {
        return get_data_object_cache_key($object->getModelName(true), $object->getId(), $subnamespace);

      // Data object as array
      } elseif(is_array($object) && count($object) == 2) {
        list($model_name, $object_id) = $object;

        return get_data_object_cache_key($model_name, $object_id, $subnamespace);

      // Class that has getId() method
      } elseif(is_object($object) && method_exists($object, 'getId')) {
        return get_data_object_cache_key(Inflector::pluralize(Inflector::underscore(get_class($object))), $object->getId(), $subnamespace);

      // Invalid object
      } else {
        throw new InvalidParamError('object', $object, '$object is expected to be loaded object instance with getId method defined or an array that has model name and object ID');
      } // if

    } // getCacheKeyForObject

    // ---------------------------------------------------
    //  Internal, Stash Related Functions
    // ---------------------------------------------------

    /**
     * Cache pool instance
     *
     * @var Pool
     */
    private $pool;

    /**
     * Return cache pool
     *
     * @return Pool
     */
    private function &getPool() {
      if(empty($this->pool)) {
        $backend = self::FILESYSTEM_BACKEND; // Default cache backend

        if(defined('CACHE_BACKEND') && CACHE_BACKEND) {
          switch(CACHE_BACKEND) {
            case 'MemcachedCacheBackend':
            case self::MEMCACHED_BACKEND:
              $backend = self::MEMCACHED_BACKEND;
              break;

            case 'APCCacheBackend':
            case self::APC_BACKEND:
              $backend = self::APC_BACKEND;
              break;
          } // switch
        } // if

        $this->pool = new Pool();

        switch($backend) {
          case self::MEMCACHED_BACKEND:
            $this->pool->setDriver($this->getMemcacheDriver());
            break;
          case self::APC_BACKEND:
            $this->pool->setDriver($this->getApcDriver());
            break;
          default:
            $this->pool->setDriver($this->getFileSystemDriver(CACHE_PATH));
        } // switch
      } // if

      return $this->pool;
    } // getPool

    /**
     * Return stash instance
     *
     * @param string $key
     * @return \Stash\Interfaces\ItemInterface
     */
    private function getStash($key) {
      return $this->getPool()->getItem($key);
    } // getStash

    /**
     * Initialize memcached backend
     *
     * @return Memcache
     */
    function getMemcacheDriver() {
      defined('CACHE_MEMCACHED_SERVERS') or define('CACHE_MEMCACHED_SERVERS', '');

      return new Memcache(array(
        'servers' => $this->parseMemcachedServersList(CACHE_MEMCACHED_SERVERS), // Return array of memcached servers
        'prefix_key' => defined('CACHE_MEMCACHED_PREFIX') && CACHE_MEMCACHED_PREFIX ? CACHE_MEMCACHED_PREFIX : APPLICATION_UNIQUE_KEY,
      ));
    } // initializeMemcachedBackend

    /**
     * Parse memcached servers list
     *
     * Note: This method is public so we can test it
     *
     * @param string $list
     * @return array
     */
    function parseMemcachedServersList($list) {
      $result = array();

      if($list) {
        foreach(explode(',', $list) as $server) {
          if(strpos($server, '/') !== false) {
            list($server_url, $weight) = explode('/', $server);
          } else {
            $server_url = $server;
            $weight = 1;
          } // if

          $parts = parse_url($server_url);

          if(empty($parts['host'])) {
            if(empty($parts['path'])) {
              Logger::log("Ignored memcached server: '$server'. Invalid host", Logger::WARNING);
              continue; // Ignore
            } else {
              $host = $parts['path'];
            } // if
          } else {
            $host = $parts['host'];
          } // if

          $result[] = array($host, array_var($parts, 'port', '11211'), $weight);
        } // foreach
      } // if

      return $result;
    } // parseMemcachedServersList

    /**
     * Initialize APC cache backend
     *
     * @param string $namespace
     * @return Apc
     */
    function getApcDriver($namespace = null) {
      return new Apc(array(
        'ttl' => $this->getLifetime(),
        'namespace' => empty($namespace) ? md5(APPLICATION_UNIQUE_KEY) : $namespace,
      ));
    } // getApcDriver

    /**
     * Initialize file system based cache backend
     *
     * @param string $path
     * @return FileSystem
     */
    function getFileSystemDriver($path) {
      return new FileSystem(array(
        'dirSplit' => 1,
        'path' => $path,
        'filePermissions' => 0777,
        'dirPermissions' => 0777,
      ));
    } // getFileSystemDriver

    /**
     * Return backend type
     *
     * @return string
     */
    function getBackendType() {
      if($this->pool) {
        if($this->pool->getDriver() instanceof Memcache) {
          return self::MEMCACHED_BACKEND;
        } elseif($this->pool->getDriver() instanceof Apc) {
          return self::APC_BACKEND;
        } elseif($this->pool->getDriver() instanceof FileSystem) {
          return self::FILESYSTEM_BACKEND;
        } // if
      } // if

      return null;
    } // getBackendType

    // ---------------------------------------------------
    //  Input Converters
    // ---------------------------------------------------

    /**
     * Prepare and return key that Stash understands
     *
     * @param $key
     * @return array
     */
    private function getKey($key) {
      return $key;
    } // getKey

    /**
     * Default cache lifetime
     *
     * @var int
     */
    private $lifetime = 3600;

    /**
     * Return lifetime
     *
     * @param $lifetime
     * @return mixed
     */
    function getLifetime($lifetime = null) {
      return $lifetime ? $lifetime : $this->lifetime;
    } // getLifetime

    /**
     * Set default cache lifetime
     *
     * @param $value
     */
    function setLifetime($value) {
      $this->lifetime = (integer) $value;
    } // setLifetime

    // ---------------------------------------------------
    //  Logger
    // ---------------------------------------------------

    /**
     * Should we log stuff or not
     *
     * @var boolean
     */
    private $log = null;

    /**
     * Log hit
     *
     * @param $key
     */
    function logHit($key) {
      if($this->log === null) {
        $this->log = AngieApplication::isInDevelopment();
      } // if

      if($this->log) {
        Logger::log("Cache hit '" . (is_array($key) ? implode('/', $key) : $key) . "'", Logger::INFO, 'cache');
      } // if
    } // logHit

    /**
     * Log miss
     *
     * @param $key
     */
    function logMiss($key) {
      if($this->log === null) {
        $this->log = AngieApplication::isInDevelopment();
      } // if

      if($this->log) {
        Logger::log("Cache miss '" . (is_array($key) ? implode('/', $key) : $key) . "'", Logger::INFO, 'cache');
      } // if
    } // logMiss

    /**
     * Log force refresh
     *
     * @param $key
     */
    function logForceRefresh($key) {
      if($this->log === null) {
        $this->log = AngieApplication::isInDevelopment();
      } // if

      if($this->log) {
        Logger::log("Cache '" . (is_array($key) ? implode('/', $key) : $key) . "' forcefully refreshed", Logger::INFO, 'cache');
      } // if
    } // logForceRefresh

  }