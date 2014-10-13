<?php

  /**
   * HTML Purifier for Angie implementation
   * 
   * @package angie.vendors.htmlpurifier
   */
  final class HtmlPurifierForAngie {
    
    /**
     * Purifier instance
     *
     * @var HTMLPurifier::
     */
    static private $purifier;
    
    /**
     * Default configuration instance
     *
     * @var HTMLPurifier_Config
     */
    static private $default_config;
  
    /**
     * Do code purification
     * 
     * @param string $html
     * @return string
     */
    static function purify($html) {
      if(defined('PURIFY_HTML') && PURIFY_HTML) {
        if(empty(self::$purifier) || empty(self::$default_config)) {
          self::$purifier = new HTMLPurifier();
          self::$default_config = HTMLPurifier_Config::createDefault();

          // Enable likification
          self::$default_config->set('AutoFormat.Linkify', true);
          self::$default_config->set('AutoFormat.PurifierLinkify', true);

  				// whitelisted attributes needed for editor
          $whitelisted_tags = HTML::getWhitelistedTagsForPurifier();

          if (is_foreachable($whitelisted_tags)) {
            $formatted_whitelisted_tags = array();
            foreach ($whitelisted_tags as $whitelisted_tag => $whitelisted_tag_attributes) {
              $formatted_whitelisted_tags[] = $whitelisted_tag;
              if (is_foreachable($whitelisted_tag_attributes)) {
                foreach ($whitelisted_tag_attributes as $whitelisted_tag_attribute) {
                  $formatted_whitelisted_tags[] = $whitelisted_tag . '[' . $whitelisted_tag_attribute . ']';
                } // foreach
              } // if
            }	// foreach
            self::$default_config->set('HTML.Allowed', implode(',', $formatted_whitelisted_tags));
          } // if

          if (is_foreachable($whitelisted_tags)) {
            $definition = self::$default_config->getHTMLDefinition(true);
            foreach ($whitelisted_tags as $whitelisted_tag => $whitelisted_tag_attributes) {
              if (is_foreachable($whitelisted_tag_attributes)) {
                foreach ($whitelisted_tag_attributes as $whitelisted_tag_attribute) {
                  $definition->addAttribute($whitelisted_tag, $whitelisted_tag_attribute, 'Text');
                } // foreach
              } // if
            }	// foreach
          } // if
        } // if

        return self::$purifier->purify($html, self::$default_config);
      } else {
        return $html;
      } // if
    } // purify
    
  }