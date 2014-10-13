<?php

  /**
   * Mailer adapter that uses native Swift mailer transport
   * 
   * @package angie.frameworks.email
   * @subpackage models
   */
  class NativeSwiftMailerAdapter extends SwiftMailerAdapter {
  	
  	/**
  	 * Return mailer name
  	 * 
  	 * @return string
  	 */
  	function getName() {
  		return lang('Native PHP mail()');
  	} // getName
  	
  	/**
  	 * Return mailer transport
  	 * 
  	 * @return Swift_SmtpTransport
  	 */
  	function getTransport() {
  	  return new Swift_MailTransport(AngieApplication::mailer()->getNativeMailerOptions());
  	} // getTransport
  	
  }