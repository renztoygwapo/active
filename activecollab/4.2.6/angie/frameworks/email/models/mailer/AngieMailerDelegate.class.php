<?php

  /**
   * Framework level application mailer implementation
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  class AngieMailerDelegate extends AngieDelegate {
    
    // Send constants
    const SEND_INSTANTNLY = 'instantly';
    const SEND_IN_BACKGROUD = 'in_background';
    const SEND_HOURLY = 'hourly';
    const SEND_DAILY = 'daily';
    
    /**
     * Mailing adapter
     * 
     * @var ApplicationMailerAdapter
     */
    private $adapter;
    
    /**
     * Return mailer adapter
     * 
     * @return ApplicationMailerAdapter
     * @throws InvalidParamError
     */
    function getAdapter() {
      if(!($this->adapter instanceof ApplicationMailerAdapter)) {
        switch(AngieApplication::mailer()->getConnectionType()) {
          case MAILING_DISABLED:
            AngieApplication::mailer()->setAdapter(new DisabledMailerAdapter());
            break;
          case MAILING_SILENT:
            AngieApplication::mailer()->setAdapter(new SilentMailerAdapter());
            break;
          case MAILING_NATIVE: 
             AngieApplication::mailer()->setAdapter(new NativeSwiftMailerAdapter());
            break;
          case MAILING_SMTP:
            AngieApplication::mailer()->setAdapter(new SmtpSwiftMailerAdapter());
            break;
          default:
            throw new InvalidParamError('mailing', AngieApplication::mailer()->getConnectionType(), 'Invalid mailer type');
        } // if
      } // if
      
      return $this->adapter;
    } // getAdapter
    
    /**
     * Set mailer adapter
     * 
     * @param ApplicationMailerAdapter $adapter
     */
    function setAdapter(ApplicationMailerAdapter $adapter) {
      $this->adapter = $adapter;
    } // setAdapter
    
    /**
     * Try to establish SMTP connection with given parameters
     * 
     * @param string $host
     * @param string $port
     * @param string $message
     * @param boolean $authenticate
     * @param string $username
     * @param string $password
     * @param string $security
     * @return boolean
     */
    function testSmtp($host, $port, &$message, $authenticate = false, $username = null, $password = null, $security = null) {
      try {
        SwiftMailerForAngie::testSmtpConnection($host, $port, $authenticate, $username, $password, $security);
        $message = lang('Connection has been established successfully');
        
        return true;
      } catch(Exception $e) {
        $message = $e->getMessage();
        return false;
      } // try
    } // testSmtp
    
    /**
     * Mailer decorator
     * 
     * @var OutgoingMessageDecorator
     */
    private $decorator;
    
    /**
     * Return mailer decorator
     * 
     * @return OutgoingMessageDecorator
     */
    function getDecorator() {
      return $this->decorator;
    } // getDecorator
    
    /**
     * Set mailer decorator
     * 
     * @param OutgoingMessageDecorator $decorator
     */
    function setDecorator(OutgoingMessageDecorator $decorator) {
      $this->decorator = $decorator;
    } // setDecorator
    
    /**
     * Default sender instance
     * 
     * @var AnonymousUser
     */
    private $default_sender;
    
    /**
     * Return default sender
     * 
     * @return AnonymousUser
     */
    function getDefaultSender() {
      if(!($this->default_sender instanceof IUser)) {
        list($from_email, $from_name) = AngieApplication::mailer()->getFromEmailAndName();
        
        if($from_email) {
          AngieApplication::mailer()->setDefaultSender(new AnonymousUser($from_name, $from_email));
        } else {
          AngieApplication::mailer()->setDefaultSender(new AnonymousUser($from_name, ADMIN_EMAIL));
        } // if
      } // if
      
      return $this->default_sender;
    } // getDefaultSender
    
    /**
     * Set default from user
     * 
     * @param AnonymousUser $sender
     */
    function setDefaultSender(AnonymousUser $sender) {
      $this->default_sender = $sender;
    } // setDefaultSender
    
    /**
     * Cached default mailing method
     * 
     * @var string
     */
    private $default_mailing_method = false;
    
    /**
     * Return default mailing method
     * 
     * @return string
     */
    function getDefaultMailingMethod() {
      if($this->default_mailing_method === false) {
        $this->default_mailing_method = ConfigOptions::getValue('mailing_method');
        
        if(!$this->isValidMailingMethod($this->default_mailing_method)) {
          $this->default_mailing_method = self::SEND_INSTANTNLY;
        } // if
      } // if
      
      return $this->default_mailing_method;
    } // getDefaultMailingMethod
    
    /**
     * Set default mailing method
     * 
     * @param string $method
     * @throws InvalidParamError
     */
    function setDefaultMailingMethod($method) {
      if($this->isValidMailingMethod($method)) {
        ConfigOptions::setValue('mailing_method', $method);
        $this->default_mailing_method = $method;
      } else {
        throw new InvalidParamError('method', $method, '$method value is not a valid mailing method name');
      } // if
    } // setDefaultMailingMethod
    
    /**
     * Return true if $method is a valid mailing method
     * 
     * @param string $method
     * @return boolean
     */
    function isValidMailingMethod($method) {
      return in_array($method, array(self::SEND_INSTANTNLY, self::SEND_IN_BACKGROUD, self::SEND_HOURLY, self::SEND_DAILY));
    } // isValidMailingMethod
    
    // ---------------------------------------------------
    //  Connect / disconnect / send
    // ---------------------------------------------------
    
    /**
     * Connect mailer adapter
     */
    function connect() {
      if($this->adapter instanceof ApplicationMailerAdapter) {
        $this->adapter->connect();
      } // if
    } // connect
    
    /**
     * Disconnect mailer adapter
     */
    function disconnect() {
      if($this->adapter instanceof ApplicationMailerAdapter) {
        $this->adapter->disconnect();
      } // if
    } // disconnect
    
    /**
     * Returns true if we have proper mailer adapter set and it is connected
     * 
     * @return boolean
     */
    function isConnected() {
      return $this->adapter instanceof ApplicationMailerAdapter ? $this->adapter->isConnected() : false;
    } // isConnected
    
    // ---------------------------------------------------
    //  Send Message
    // ---------------------------------------------------
    
    /**
     * Send a message to one or more recipients
     * 
     * Supported additional parameters:
     * 
     * - context - Context in which notification is sent
     * - decorate - Whether email should be decorated or not. This parameter is 
     *   taken into account only if message is sent instantly. Default is TRUE
     * 
     * @param mixed $recipients
     * @param string $subject
     * @param string $body
     * @param array $additional
     * @param string $method
     * @return mixed
     * @throws InvalidParamError
     */
    function send($recipients, $subject = '', $body = '', $additional = null, $method = AngieMailerDelegate::SEND_INSTANTNLY) {
      if($recipients instanceof IUser) {
        return $this->sendToRecipient($recipients, $subject, $body, $additional, $method);
      } elseif(is_array($recipients)) {
        $sent = array();
        
        foreach($recipients as $recipient) {
          $sent[] = $this->sendToRecipient($recipient, $subject, $body, $additional, $method);
        } // foreach
        
        return $sent;
      } else {
        throw new InvalidParamError('recipients', $recipients, 'Recipient should be an IUser instance or a list of users');
      } // if
    } // send

    /**
     * Send message to a single recipient
     *
     * @param IUser $recipient
     * @param string $subject
     * @param string $body
     * @param null $additional
     * @param string $method
     * @return bool|OutgoingMessage
     */
    private function sendToRecipient(IUser $recipient, $subject = '', $body = '', $additional = null, $method = AngieMailerDelegate::SEND_INSTANTNLY) {
      if($recipient instanceof User && !$recipient->isActive()) {
        return false; // we cannot send email to inactive user
      } // if

      $outgoing_message = new OutgoingMessage();
      
      $context = $additional && isset($additional['context']) && $additional['context'] ? $additional['context'] : null;

      if($context instanceof ApplicationObject) {
        $outgoing_message->setParent($context);
      } // if

      $decorator = array_var($additional, 'decorator');
      
      $outgoing_message->setSender(AngieApplication::mailer()->getDefaultSender());
      $outgoing_message->setRecipient($recipient);
      $outgoing_message->setSubject($subject);
      $outgoing_message->setDecorator($decorator);
      $outgoing_message->setBody($body);
      $outgoing_message->setCode(array_var($additional, 'subscription_code', null));
      
      if(is_array($additional)) {
        if($context) {
          if($context instanceof IComments) {
            $outgoing_message->setContextId($context->comments()->getCommentRoutingCode());
          } elseif(is_string($context)) {
            $outgoing_message->setContextId($context);
          } // if
        } // if
        
        if(isset($additional['sender']) && $additional['sender']) {
          $outgoing_message->setSender($additional['sender']);
        } // if
        
        if(isset($additional['attachments']) && is_foreachable($additional['attachments'])) {
          foreach($additional['attachments'] as $attachment_path => $attachment_name) {
            if(is_numeric($attachment_path)) {
              $outgoing_message->attachments()->attachFile($attachment_name, basename($attachment_name), 'application/octet-stream'); // Use file name
            } else {
              $outgoing_message->attachments()->attachFile($attachment_path, $attachment_name, 'application/octet-stream');
            } // if
          } // foreach
        } // if
      } // if
      
      if($method === null) {
        $method = $recipient instanceof User && $recipient->isLoaded() ? ConfigOptions::getValueFor('mailing_method', $recipient) : ConfigOptions::getValue('mailing_method');
      } // if

      $outgoing_message->setMailingMethod($method);
      $outgoing_message->save();
      
      if($method == AngieMailerDelegate::SEND_INSTANTNLY) {
        $outgoing_message->send(($additional && isset($additional['decorate']) ? (boolean) $additional['decorate'] : true));
      } // if
      
      return $outgoing_message;
    } // sendToRecipient
    
    /**
     * Send multiple messages as digest
     * 
     * @param array $messages
     * @return array
     */
    function sendDigest($messages) {
      if(is_foreachable($messages)) {
        $by_recipient = array();
        foreach($messages as $message) {
          $email = $message->getRecipient()->getEmail();
          
          if(!isset($by_recipient[$email])) {
            $by_recipient[$email] = array();
          } // if
          
          $by_recipient[$email][] = $message;
        } // foreach
        
        foreach($by_recipient as $recipient_messages) {
          $this->getAdapter()->sendDigest($recipient_messages);
        } // foreach
      } // if
    } // sendDigest
    
    // ---------------------------------------------------
    //  Configuration Options
    // ---------------------------------------------------
    
    /**
     * Returns true if connection settings are locked
     * 
     * @return boolean
     */
    function isConnectionConfigurationLocked() {
      return defined('MAILING_CONNECTION_CONFIGURATION_LOCKED') && MAILING_CONNECTION_CONFIGURATION_LOCKED;
    } // isConnectionConfigurationLocked
    
    /**
     * Return true if mailing message configuration locked
     */
    function isMessageConfigurationLocked() {
      return defined('MAILING_MESSAGE_CONFIGURATION_LOCKED') && MAILING_MESSAGE_CONFIGURATION_LOCKED;
    } // isMessageConfigurationLocked
    
    /**
     * Cached connetion type value
     *
     * @var string
     */
    private $connection_type = false;
    
    /**
     * Return connection type
     * 
     * @return string
     */
    function getConnectionType() {
      if($this->connection_type === false) {
        if(AngieApplication::mailer()->isConnectionConfigurationLocked()) {
          $this->connection_type = defined('MAILING_CONNECTION_TYPE') && MAILING_CONNECTION_TYPE ? MAILING_CONNECTION_TYPE : 'silent';
        } else {
          $this->connection_type = ConfigOptions::getValue('mailing');
        } // if
      } // if
      
      return $this->connection_type;
    } // getConnectionType
    
    /**
     * Return SMTP connection parameters
     * 
     * @return array
     */
    function getSmtpConnectionParameters() {
      if(AngieApplication::mailer()->isConnectionConfigurationLocked()) {
        return array(
          defined('MAILING_CONNECTION_SMTP_HOST') ? MAILING_CONNECTION_SMTP_HOST : '', 
          defined('MAILING_CONNECTION_SMTP_PORT') ? MAILING_CONNECTION_SMTP_PORT : '', 
          defined('MAILING_CONNECTION_SMTP_AUTHENTICATE') ? MAILING_CONNECTION_SMTP_AUTHENTICATE : false, 
          defined('MAILING_CONNECTION_SMTP_USERNAME') ? MAILING_CONNECTION_SMTP_USERNAME : '', 
          defined('MAILING_CONNECTION_SMTP_PASSWORD') ? MAILING_CONNECTION_SMTP_PASSWORD: '', 
          defined('MAILING_CONNECTION_SMTP_SECURITY') && MAILING_CONNECTION_SMTP_SECURITY ? MAILING_CONNECTION_SMTP_SECURITY : 'off', 
        );
      } else {
        $options = ConfigOptions::getValue(array(
          'mailing_smtp_host', 
          'mailing_smtp_port', 
          'mailing_smtp_authenticate',
          'mailing_smtp_username', 
          'mailing_smtp_password', 
          'mailing_smtp_security',
        ));
        
        return array(
          $options['mailing_smtp_host'], 
          $options['mailing_smtp_port'], 
          $options['mailing_smtp_authenticate'], 
          $options['mailing_smtp_username'], 
          $options['mailing_smtp_password'], 
          $options['mailing_smtp_security']
        );
      } // if
    } // getSmtpConnectionParameters
    
    /**
     * Return native mailer options
     * 
     * @return string
     */
    function getNativeMailerOptions() {
      if(AngieApplication::mailer()->isConnectionConfigurationLocked()) {
        return defined('MAILING_CONNECTION_NATIVE_OPTIONS') ? MAILING_CONNECTION_NATIVE_OPTIONS : '-oi -f %s';
      } else {
        return ConfigOptions::getValue('mailing_native_options');
      } // if
    } // getNativeMailerOptions
    
    /**
     * Return email address and name that are used to set From email parameters
     * 
     * @return array
     */
    function getFromEmailAndName() {
      if(AngieApplication::mailer()->isMessageConfigurationLocked()) {
        return array(
          defined('MAILING_MESSAGE_FROM_EMAIL') ? MAILING_MESSAGE_FROM_EMAIL : '', 
          defined('MAILING_MESSAGE_FROM_NAME') ? MAILING_MESSAGE_FROM_NAME : ConfigOptions::getValue('identity_name'),
        );
      } else {
        return array(ConfigOptions::getValue('notifications_from_email'), ConfigOptions::getValue('notifications_from_name'));
      } // if
    } // getFromEmailAndName
    
    /**
     * Cached force message from
     *
     * @var boolean
     */
    private $force_message_from = null;
    
    /**
     * Returns true if message from is forced
     * 
     * @return boolean
     */
    function getForceMessageFrom() {
      if($this->force_message_from === null) {
        if(AngieApplication::mailer()->isMessageConfigurationLocked()) {
          $this->force_message_from = defined('MAILING_MESSAGE_FROM_FORCE') && MAILING_MESSAGE_FROM_FORCE;
        } else {
          $this->force_message_from = (boolean) ConfigOptions::getValue('notifications_from_force');
        } // if
      } // if
      
      return $this->force_message_from;
    } // getForceMessageFrom
    
    /**
     * Cached mark messages as bulk value
     *
     * @var boolean
     */
    private $mark_messages_as_bulk = null;
    
    /**
     * Returns true if messages should be marked as bulk mail
     *
     * @return boolean
     */
    function getMarkAsBulk() {
      if($this->mark_messages_as_bulk === null) {
        if(AngieApplication::mailer()->isMessageConfigurationLocked()) {
          $this->mark_messages_as_bulk = defined('MAILING_MESSAGE_MARK_AS_BULK') && MAILING_MESSAGE_MARK_AS_BULK;
        } else {
          $this->mark_messages_as_bulk = (boolean) ConfigOptions::getValue('mailing_mark_as_bulk');
        } // if
      } // if
      
      return $this->mark_messages_as_bulk;
    } // getMarkAsBulk
    
    /**
     * Set mark as bulk value
     * 
     * @param boolean $value
     */
    function setMarkAsBulk($value) {
      ConfigOptions::setValue('mailing_mark_as_bulk', $value);
      $this->mark_messages_as_bulk = $value;
    } // setMarkAsBulk 
    
  }