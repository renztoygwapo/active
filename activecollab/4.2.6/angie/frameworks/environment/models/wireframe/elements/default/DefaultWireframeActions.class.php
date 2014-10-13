<?php

  /**
   * Wireframe actions for default interface
   * 
   * @package angie.frameworks.envirnment
   * @subpackage models
   */
  class DefaultWireframeActions extends WireframeActions {
    
    /**
     * Event that is triggered when page object is set in wireframe
     * 
     * @param ApplicationObject $object
     * @param IUser $user
     */
    function onPageObjectSet($object, IUser $user) {
      $options = $object->getOptions($user, AngieApplication::INTERFACE_DEFAULT);
      
      if($options instanceof NamedList && $options->count()) {
        if($object instanceof ApplicationObject) {
          $id = $object->getId();
        } elseif($object instanceof AngieModule) {
          $id = $object->getName();
        } else {
          $id = rand();
        } // if
        
        $this->add('object_options', lang('Options'), '#', array(
          'subitems' => $options->toArray(), 
        ));
      } // if
    } // onPageObjectSet
    
  }