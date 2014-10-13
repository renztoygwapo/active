<?php

  /**
   * Interface that all objects which want to have avatar attached to them
   * need to implement
   *
   * @package angie.frameworks.avatar
   * @subpackage models
   */
  interface IAvatar {
    
    /**
     * Return avatar helper for this object
     *
     * @return IAvatarImplementation
     */
    public function avatar();
    
  }