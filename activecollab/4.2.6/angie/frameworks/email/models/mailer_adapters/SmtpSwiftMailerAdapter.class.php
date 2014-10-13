<?php

  /**
   * Mailer adapter that uses Swift mailer's SMTP connection
   * 
   * @package angie.frameworks.email
   * @subpackage models
   */
  class SmtpSwiftMailerAdapter extends SwiftMailerAdapter {
    
    /**
     * Cached connection name
     *
     * @var string
     */
    private $name = false;
  	
  	/**
  	 * Return mailer name
  	 * 
  	 * @return string
  	 */
  	function getName() {
  	  if($this->name = false) {
  	    list($host, $port, $authenticate, $username, $password, $security) = AngieApplication::mailer()->getSmtpConnectionParameters();
  	    
  	    $this->name = lang('SMTP (:host::port)', array(
  	      'host' => $host, 
  	      'port' => $port
  	    ));
  	  } // if
  	  
  		return $this->name;  		
  	} // getName
  	
  	/**
  	 * Return mailer transport
  	 * 
  	 * @return Swift_SmtpTransport
  	 */
  	function getTransport() {
  	  list($host, $port, $authenticate, $username, $password, $security) = AngieApplication::mailer()->getSmtpConnectionParameters();
  	  
  	  if($security != 'ssl' && $security != 'tls') {
  	    $security = null;
  	  } // if
  	  
  	  $smtp = Swift_SmtpTransport::newInstance($host, $port, $security);
      if($authenticate) {
        $smtp
          ->setUsername($username)
          ->setPassword($password);
      } // if
      
      return $smtp;
  	} // getTransport
  	
  }