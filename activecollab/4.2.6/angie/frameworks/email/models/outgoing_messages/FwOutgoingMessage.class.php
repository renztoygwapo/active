<?php

  /**
   * Outgoing message instance
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  abstract class FwOutgoingMessage extends BaseOutgoingMessage implements IRoutingContext, IReadOnly, IAttachments {
  	
  	/**
  	 * Send this message
  	 * 
  	 * @param boolean $decorate
  	 */
  	function send($decorate = true) {
  		AngieApplication::mailer()->getAdapter()->send($this, $decorate);
  	} // send
  	
  	/**
  	 * Return outgoing message name
  	 * 
  	 * @return string
  	 */
  	function getName() {
  		return $this->getSubject();
  	} // getName

    private $decorator = false;

    /**
     * @return FwOutgoingMessageDecorator|OutgoingMessageDecorator
     */
    function getDecorator() {
      $decorator_class = parent::getDecorator();
      if ($decorator_class) {
        if ($this->decorator === false) {
          $this->decorator = new $decorator_class();
        } // if

        return $this->decorator;
      } else {
        return AngieApplication::mailer()->getDecorator();
      } // if
    } // getDecorator

    /**
     * @param FwOutgoingMessageDecorator|string|null $decorator
     * @return string
     */
    function setDecorator($decorator) {
      if ($decorator instanceof FwOutgoingMessageDecorator) {
        $decorator = get_class($decorator);
      } else {
        if (is_string($decorator)) {
          // ok
        } else {
          $decorator = get_class(AngieApplication::mailer()->getDecorator());
        } // if
      } // if

      return parent::setDecorator($decorator);
    } // setDecorator
  	
  	/**
  	 * Cached sender instance
  	 * 
  	 * @var IUser
  	 */
  	private $sender = false;
  	
  	/**
  	 * Return message sender instance
  	 * 
  	 * @return IUser
  	 */
  	function getSender() {
  		if($this->sender === false) {
  			if($this->getSenderId()) {
  				$this->sender = Users::findById($this->getSenderId());
  			} // if
  			
  			if(!($this->sender instanceof IUser)) {
  				$this->sender = new AnonymousUser($this->getSenderName(), $this->getSenderEmail());
  			} // if
  		} // if
  		
  		return $this->sender;
  	} // getSender
  	
  	/**
  	 * Set message sender
  	 * 
  	 * @param IUser $sender
  	 * @return IUser
  	 */
  	function setSender(IUser $sender) {
  		if($sender instanceof IUser) {
  			$this->setSenderId($sender->getId());
  			$this->setSenderName($sender->getName());
  			$this->setSenderEmail($sender->getEmail());
  		} else {
  			$this->setSenderId(null);
  			$this->setSenderName(null);
  			$this->setSenderEmail(null);
  		} // if
  		
  		$this->sender = $sender;
  		
  		return $this->sender;
  	} // setSender
  	
  	/**
  	 * Cached recipient instance
  	 * 
  	 * @var IUser
  	 */
  	private $recipient = false;
  	
  	/**
  	 * Return message recipient instance
  	 * 
  	 * @return IUser
  	 */
  	function getRecipient() {
  		if($this->recipient === false) {
  			if($this->getRecipientId()) {
  				$this->recipient = Users::findById($this->getRecipientId());
  			} else {
  				$this->recipient = Users::findByEmail($this->getRecipientEmail());
  			} // if
  			
  			if(!($this->recipient instanceof IUser)) {
  				$this->recipient = new AnonymousUser($this->getRecipientName(), $this->getRecipientEmail());
  			} // if
  		} // if
  		
  		return $this->recipient;
  	} // getRecipient
  	
  	/**
  	 * Set message recipient
  	 * 
  	 * @param IUser $recipient
  	 * @return IUser
  	 */
  	function setRecipient(IUser $recipient) {
  		if($recipient instanceof IUser) {
  			$this->setRecipientId($recipient->getId());
  			$this->setRecipientName($recipient->getName());
  			$this->setRecipientEmail($recipient->getEmail());
  		} else {
  			$this->setRecipientId(null);
  			$this->setRecipientName(null);
  			$this->setRecipientEmail(null);
  		} // if
  		
  		$this->recipient = $recipient;
  		
  		return $this->recipient;
  	} // setRecipient

    /**
     * Set message subject and make sure that it is not too long
     *
     * @param string $value
     * @return string
     */
    function setSubject($value) {
      if(strlen_utf($value) > 255) {
        $value = substr_utf($value, 0, 255);
      } // if

      return parent::setSubject($value);
    } // setSubject
  	
  	/**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
    	$result = parent::describe($user, $detailed, $for_interface);
    	
    	$result['sender'] = $this->getSender()->describe($user, false, $for_interface);
    	$result['recipient'] = $this->getRecipient()->describe($user, false, $for_interface);
    	$result['subject'] = $this->getSubject();
    	$result['body'] = $this->getBody();
    	$result['context_id'] = $this->getContextId();
    	$result['mailing_method'] = $this->getMailingMethod();
    	$result['send_retries'] = $this->getSendRetries();
    	$result['last_send_error'] = $this->getLastSendError();
    	$result['urls']['send'] = $this->getSendUrl();
    	
    	return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     * @throws NotImplementedError
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
  	/**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
    	return 'outgoing_messages_admin_message';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
    	return array('message_id' => $this->getId());
    } // getRoutingContextParams
    
    /**
     * Cached attachments interface implementation
     * 
     * @var IOutgoingMessageAttachmentsImplementation
     */
    private $attachments = false;
    
    /**
     * Return attachments interface implementation
     * 
     * @return IOutgoingMessageAttachmentsImplementation
     */
    function &attachments() {
    	if($this->attachments === false) {
    		$this->attachments = new IOutgoingMessageAttachmentsImplementation($this);
    	} // if
    	
    	return $this->attachments;
    } // attachments
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return send message URL
     * 
     * @return string
     */
    function getSendUrl() {
      return Router::assemble('outgoing_messages_admin_message_send', array('message_id' => $this->getId()));
    } // getSendUrl

    /**
     * Return raw body URL
     *
     * @return string
     */
    function getRawBodyUrl() {
      return Router::assemble('outgoing_messages_admin_message_raw_body', array('message_id' => $this->getId()));
    } // getRawBodyUrl

    /**
     * Return url for public unsubscribe
     *
     * @return string
     */
    function getUnsubscribeUrl() {
      if ($this->getCode()) {
        return Router::assemble('public_notifications_unsubscribe', array('code' => $this->getCode()));
      } // if

      return false;
    } // getUnsubscribeUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------

    /**
     * Return object domain
     *
     * @return string
     */
    function getObjectContextDomain() {
      return 'outgoing-messages';
    } // getContextDomain

    /**
     * Return object path
     *
     * @return string
     */
    function getObjectContextPath() {
      return 'outgoing-messages/' . $this->getId();
    } // getContextPath
    
    /**
     * Validate before save
     * 
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
    	if($this->validatePresenceOf('sender_email')) {
    		if(!is_valid_email($this->getSenderEmail())) {
    			$errors->addError(lang('Sender email is not valid email address'), 'sender_email');
    		} // if
    	} else {
    		$errors->addError(lang('Sender email is required'), 'sender_email');
    	} // if
    	
    	if($this->validatePresenceOf('recipient_email')) {
    	  if(!is_valid_email($this->getRecipientEmail())) {
    	  	$errors->addError(lang('Recipient email is not valid email address'), 'recipient_email');
    	  } // if
    	} else {
    	  $errors->addError(lang('Recipient email is required'), 'recipient_email');
    	} // if
    } // validate
    
  }