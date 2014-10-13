<?php

  /**
   * Return $object class
   *
   * @package angie.frameworks.environment
   * @subpackage helpers
   */
  function smarty_modifier_class($object) {
    return is_object($object) ? get_class($object) : '';
  } // smarty_modifier_class