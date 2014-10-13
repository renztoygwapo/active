<?php

  defined('DESCRIBE_CACHE_ENABLED') or define('DESCRIBE_CACHE_ENABLED', true);

  /**
   * Describe delegate
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class AngieDescribeDelegate extends AngieDelegate {

    /**
     * Describe a given object
     *
     * @param IDescribe|ApplicationObject $object
     * @param IUser $user
     * @param bool $detailed
     * @param mixed $for_interface
     * @return array
     * @throws InvalidInstanceError
     */
    function object($object, IUser $user, $detailed = false, $for_interface = false) {
      if($object instanceof IDescribe) {
        if(empty($for_interface)) {
          $for_interface = AngieApplication::INTERFACE_DEFAULT;
        } // if

        $detailed = (integer) $detailed;

        if(DESCRIBE_CACHE_ENABLED && AngieApplication::cache()->isValidObject($object)) {
          return AngieApplication::cache()->getByObject($object, array('describe', $user->getEmail(), $for_interface, $detailed), function() use ($object, $user, $detailed, $for_interface) {
            return $object->describe($user, $detailed, $for_interface);
          });
        } else {
          return $object->describe($user, $detailed, $for_interface);
        } // if
      } else {
        throw new InvalidInstanceError('object', $object, 'IDescribe');
      } // if
    } // object

    /**
     * Describe a given object
     *
     * @param IDescribe|ApplicationObject $object
     * @param IUser $user
     * @param bool $detailed
     * @return array
     * @throws InvalidInstanceError
     * @throws Exception
     */
    function objectForApi($object, IUser $user, $detailed = false) {
      if($object instanceof IDescribe) {
        $detailed = (integer) $detailed;

        $cache_disabled_for_object = method_exists($object, 'disableDescribeCache') && $object->disableDescribeCache();

        if(DESCRIBE_CACHE_ENABLED && empty($cache_disabled_for_object) && AngieApplication::cache()->isValidObject($object)) {
          return AngieApplication::cache()->getByObject($object, array('describe_for_api', $user->getEmail(), $detailed), function() use ($object, $user, $detailed) {
            try {
              return $object->describeForApi($user, $detailed);
            } catch(NotImplementedError $e) {
              return null;
            } catch(Exception $e) {
              throw $e;
            } // try
          });
        } else {
          try {
            return $object->describeForApi($user, $detailed);
          } catch(NotImplementedError $e) {
            return null;
          } catch(Exception $e) {
            throw $e;
          } // try
        } // if
      } else {
        throw new InvalidInstanceError('object', $object, 'IDescribe');
      } // if
    } // objectForApi

  }