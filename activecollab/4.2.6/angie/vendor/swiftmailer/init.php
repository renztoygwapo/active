<?php

  /**
   * SwiftMailer initialization file
   * 
   * @package angie.vendor.swiftmailer
   */

  // Location of swift mailer library
  define('SWIFT_MAILER_FOR_ANGIE_PATH', ANGIE_PATH . '/vendor/swiftmailer');
  
  AngieApplication::setForAutoload(array(
    'SwiftMailerForAngie' => SWIFT_MAILER_FOR_ANGIE_PATH . '/SwiftMailerForAngie.class.php', 
    'SwiftMailerForLogger' => SWIFT_MAILER_FOR_ANGIE_PATH . '/SwiftMailerForLogger.class.php', 
  ));