<?php

  /**
   * XML encoder used by activeCollab API
   *
   * @package angie.library
   */
  final class XmlEncoder {
    
    /**
     * User instance that will be used to encode elements, used for describe() 
     * methods of ApplicationObject instances
     *
     * @var IUser
     */
    static private $user;
    
    /**
     * Detailed setting for describe() method of ApplicationObject instances, 
     * if present
     * 
     * These settings are used only for the first level of encoding
     *
     * @var boolean
     */
    static private $detailed = false;

    /**
     * Encode $data as XML
     *
     * @param array $value
     * @param string $as
     * @param IUser $user
     * @param boolean $detailed
     * @return string
     */
    static function encode($value, $as = 'items', $user = null, $detailed = false) {
      self::$user = $user instanceof IUser ? $user : Authentication::getLoggedUser();
      self::$detailed = $detailed;
      
      if($value instanceof Error && empty($as)) {
        $as = 'error';
      } // if
      
      return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" . self::encodeNode($value, $as);
    } // encode

    /**
     * Encode data node
     *
     * @param mixed $data
     * @param string $as
     * @return string
     */
    static protected function encodeNode($data, $as) {
      
      // Describable object that we need to encode
      if($data instanceof IDescribe) {
        return self::encodeNode(AngieApplication::describe()->objectForApi($data, self::$user, self::$detailed), $as);
        
      // Do the actual data encoding
      } else {
      
        $result = "<$as>";
  
        if(is_foreachable($data)) {
          $has_numeric = false;
          foreach($data as $k => $v) {
            if(is_numeric($k)) {
              $has_numeric = true;
              break;
            } // if
          } // if
  
          $singular = null;
          if($has_numeric) {
            $singular = Inflector::singularize($as);
          } // if
  
          foreach($data as $k => $v) {
            if(is_numeric($k)) {
              $k = $singular;
            } // if
            $result .= self::encodeNode($v, $k);
          } // if
        } else {
          if(is_int($data) || is_float($data)) {
            $result .= $data;
          } elseif(is_array($data)) {
            $result .= '';
          } elseif($data instanceof DateValue) {
            $result .= $data->toMySQL();
          } elseif(is_null($data)) {
            $result .= '';
          } elseif(is_bool($data)) {
            $result .= $data ? '1' : '0';
          } else {
            $result .= '<![CDATA[' . $data . "]]>";
          } // if
        } // if
  
        return $result . "</$as>\n";
      } // if
    } // encodeNode

  }