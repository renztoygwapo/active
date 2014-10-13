<?php

  /**
   * JSON class
   * 
   * @package angie.library.json
   */
  final class JSON {
    
    /**
     * Encode value to JSON
     * 
     * @param mixed $value
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return string
     */
    static function encode($value, $user = null, $detailed = false, $for_interface = false) {
      if($user === null) {
        $user = Authentication::getLoggedUser();
      } // if
      
      // Describe
      if($value instanceof IDescribe) {
        if($for_interface === AngieApplication::INTERFACE_API && !JSON_API_COMPATIBILITY_RESPONSE) {
          return JSON::encode(AngieApplication::describe()->objectForApi($value, $user, $detailed, $for_interface));
        } else {
          return JSON::encode(AngieApplication::describe()->object($value, $user, $detailed, $for_interface));
        } // if

      // IJSON
      } elseif($value instanceof IJSON) {
        return $value->toJSON($user, $detailed, $for_interface);
        
      // Named list
      } elseif($value instanceof NamedList) {
        $result = array();
        
        foreach($value as $k => $v) {
          $result[] = '"' . $k . '":' . JSON::encode($v, $user, $detailed, $for_interface);
        } // foreach
        
        return '{' . implode(',', $result) . '}';
        
      // Array (assoc or numeric)
      } elseif(is_array($value) || $value instanceof ArrayAccess) {
        if(count($value) == 0) {
          return '[]';
        } else {
          $is_assoc = false;
          
          $key_should_be = 0;
          foreach(array_keys($value) as $key) {
            if($key !== $key_should_be) {
              $is_assoc = true;
              break;
            } // if
            
            $key_should_be++;
          } // foreach
          
          $result = array();
          
          foreach($value as $k => $v) {
            if($is_assoc) {
              $result[] = (is_string($k) ? JSON::encode($k) : '"' . $k . '"') . ':' . JSON::encode($v, $user, $detailed, $for_interface);
            } else {
              $result[] = JSON::encode($v, $user, $detailed, $for_interface);
            } // if
          } // foreach
          
          return $is_assoc ? '{' . implode(',', $result) . '}' : '[' . implode(',', $result) . ']';
        } // if
        
      // Scalar value
      } else {
        return json_encode($value);
      } // if
    } // encode

    /**
     * Encode value to JSON, but encode it so it is compatible with XML response
     *
     * @param mixed $value
     * @param IUser $user
     * @param boolean $detailed
     * @return string
     */
    static function encodeForApi($value, $user = null, $detailed = false) {
      if($user === null) {
        $user = Authentication::getLoggedUser();
      } // if

      if($value instanceof IDescribe || is_object($value) && method_exists($value, 'describeForApi')) {
        return JSON::encode(AngieApplication::describe()->objectForApi($value, $user, $detailed));
      } else {
        return JSON::encode($value, $user, $detailed, AngieApplication::INTERFACE_API);
      } // if
    } // encodeForApi
    
    /**
     * Decodes JSON value
     * 
     * @param string $value
     * @return mixed
     * @throws JSONDecodeError
     */
    static function decode($value) {
      if($value) {
        $result = json_decode($value, true);
        
        if($result === null) {
          if(strtolower($value) == 'null') {
            return $result;
          } else {
            throw new JSONDecodeError($value, json_last_error());
          } // if
        } else {
          return $result;
        } // if
      } else {
        return null;
      } // if
      
      return $value ? json_decode($value, true) : null;
    } // decode

    /**
     * Convert given value to map
     *
     * @param mixed $value
     * @param User $user
     * @param bool $detailed
     * @param bool $for_interface
     * @return string
     */
    static function map($value, $user = null, $detailed = false, $for_interface = false) {
      return JSON::encode(JSON::valueToMap($value), $user, $detailed, $for_interface);
    } // map

    /**
     * Convert provided value to map
     *
     * @param mixed $value
     * @return array
     * @throws InvalidParamError
     */
    static function valueToMap($value) {
      if(empty($value) || is_foreachable($value)) {
        $elements = array();

        if($value) {
          foreach($value as $k => $v) {
            $elements[] = array('__k' => $k, '__v' => $v);
          } // foreach
        } // if

        return $elements;
      } else {
        throw new InvalidParamError('value', $value, 'Only foreachable or empty values can be converted to maps');
      } // if
    } // valueToMap
    
  }