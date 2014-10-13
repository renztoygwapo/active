<?php

  /**
   * Swift for Angie definition
   * 
   * @package angie.vendors.swift
   */
  final class SwiftMailerForAngie {
    
    /**
     * TRUE if SwiftMailer is included
     *
     * @var boolean
     */
    static private $swift_mailer_included = false;
    
    /**
     * Include SwiftMailer library, set up proper auto-loaders etc
     */
    static function includeSwiftMailer() {
      if(self::$swift_mailer_included === false) {
        define('SWIFT_REQUIRED_LOADED', true);

        // Load Swift utility class
        require SWIFT_MAILER_FOR_ANGIE_PATH . '/swiftmailer/classes/Swift.php';
        
        // Prepend auto-loader
        AngieApplication::registerAutoloader(array('Swift', 'autoload'));
        
        // Load the init script to set up dependency injection
        require SWIFT_MAILER_FOR_ANGIE_PATH . '/swiftmailer/swift_init.php';
        
        self::$swift_mailer_included = true;
      } // if
    } // includeSwiftMailer
    
    /**
     * Test connection
     * 
     * @param string $host
     * @param string $port
     * @param boolean $authenticate
     * @param string $username
     * @param string $password
     * @param string $security
     * @throws Swift_Transport_TransportException
     */
    static function testSmtpConnection($host, $port, $authenticate = false, $username = null, $password = null, $security = null) {
      self::includeSwiftMailer();

      $smtp = Swift_SmtpTransport::newInstance($host, $port, $security);
      if($authenticate) {
        $smtp->setUsername($username)->setPassword($password);
      } // if
      
      $mailer = Swift_Mailer::newInstance($smtp);
      $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin(new SwiftMailerForLogger()));
      
      $smtp->start();
      
      // Try to send out a message
      if(defined('TEST_SMTP_BY_SENDING_EMAIL_TO') && is_valid_email(TEST_SMTP_BY_SENDING_EMAIL_TO)) {
        $message = Swift_Message::newInstance('Testing Connection', 'Test SMTP connection by sending an actual email', 'text/plain', 'utf-8')->setEncoder(Swift_Encoding::get8BitEncoding());
        $message->setFrom(ADMIN_EMAIL);
        $message->setTo(TEST_SMTP_BY_SENDING_EMAIL_TO);
        
        $mailer->send($message);
      } // if
    } // testSmtpConnection
    
  }