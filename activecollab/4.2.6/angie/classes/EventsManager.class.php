<?php

  /**
   * Events manager
   * 
   * @package angie.library.events
   */
  final class EventsManager {
    
    /**
     * Current module
     *
     * @var AngieFramework
     */
    static private $current_module;
  
    /**
     * Array of event definitions
     *
     * @var array
     */
    static private $events = array();
    
    /**
     * Initialize events manager
     * 
     * @param AngieFramework[] $frameworks
     * @param AngieModule[] $modules
     */
    static function init(&$frameworks, &$modules) {
      foreach($frameworks as $framework) {
        self::$current_module = $framework;
        $framework->defineHandlers();
      } // foreach
      
      foreach($modules as $module) {
        self::$current_module = $module;
        $module->defineHandlers();
      } // foreach
      
      self::$current_module = null;
    } // init
    
    /**
     * Subscribe $callback function to $event
     *
     * @param string $event
     * @param string $callback
     * @param string $module
     * @throws InvalidParamError
     */
    public static function listen($event, $callback, $module = null) {
      if($module === null) {
        if(self::$current_module instanceof AngieFramework) {
          $module = self::$current_module->getName();
        } else {
          throw new InvalidParamError('module', $module, "module parameter value is required");
        } // if
      } // if
      
      if(is_array($event)) {
        foreach($event as $single_event) {
          self::listen($single_event, $callback, $module);
        } // foreach
      } else {
        $callback_function_name = $module . '_handle_' . $callback;
        
        $handler_file = AngieApplication::getEventHandlerPath($callback, $module);
        
        $handler = array(
          $callback_function_name, 
          $handler_file,
        );
        
        if(isset(self::$events[$event])) {
          $already_subscribed = false;
          foreach(self::$events[$event] as $subscribed_handler_data) {
            if($subscribed_handler_data[0] == $callback_function_name) {
              $already_subscribed = true;
            } // if
          }  // foreach
          
          if(!$already_subscribed) {
            self::$events[$event][] = $handler;
          } // if
        } else {
          self::$events[$event] = array($handler);
        } // if
      } // if
    } // listen
    
    /**
     * Trigger specific event with a given parameters
     * 
     * $result is start value of result. It determines how data returned from 
     * callback functions will be handled. If $result is:
     * 
     * - array - values will be added as new elements
     * - integer or float - values will be added to the $result
     * - string - values will be appended to current value
     * - null - values returned from callback functions are ignored
     * 
     * If callback function returns FALSE executen is stopped and result made to 
     * that point is retuned
     * 
     * WARNING: $result is not passed by reference
     *
     * @param string $event
     * @param array $params
     * @param mixed $result
     * @return mixed
     */
    public static function trigger($event, $params = null, $result = null) {
      if($params === null) {
        $params = array(); // empty list of params
      } // if
      
      if(class_exists('Logger')) {
        Logger::log("Event '$event' triggered", Logger::INFO, 'events');
      } // if
      
      if(isset(self::$events[$event])) {
        if(is_foreachable(self::$events[$event])) {
          foreach(self::$events[$event] as $handler) {
            
            // Extract callback function name and expected location
            list($callback, $location) = $handler;
            
            // If handler function is not defined include file
            if(!function_exists($callback)) {
              require_once $location;
            } // if
            
            // Go baby go...
            $callback_result = call_user_func_array($callback, $params);            
            Logger::log("Callback '$callback' called for '$event'. Execution result: " . var_export($callback_result, true), Logger::INFO, 'events');
            
            if($callback_result === false) {
              return $result; // break here if we get FALSE
            } // if
            
            if(is_array($result)) {
              $result[] = $callback_result;
            } elseif(is_string($result)) {
              $result .= $callback_result;
            } elseif(is_int($result) || is_float($result)) {
              $result += $callback_result;
            } // if
          } // foreach
        } // if
        return $result;
      } // if
    } // trigger
  
  }