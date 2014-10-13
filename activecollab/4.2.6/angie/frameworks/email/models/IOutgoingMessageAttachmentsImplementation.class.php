<?php

  /**
   * Outgoing email message attachments implementation
   * 
   * @package angie.frameworks.email
   * @subpackage models
   */
  class IOutgoingMessageAttachmentsImplementation extends IAttachmentsImplementation {
  	
  	/**
  	 * Construct outgoing message attachments interface implemetnation
  	 * 
  	 * @param OutgoingMessage $object
     * @throws InvalidInstanceError
  	 */
    function __construct(OutgoingMessage $object) {
  	  if($object instanceof OutgoingMessage) {
  	    parent::__construct($object);
  	  } else {
  	    throw new InvalidInstanceError('object', $object, 'OutgoingMessage');
  	  } // if
  	} // __construct
  	
  }