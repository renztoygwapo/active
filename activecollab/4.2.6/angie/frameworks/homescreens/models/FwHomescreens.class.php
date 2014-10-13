<?php

  /**
   * Framework level homescreens manager implementation
   * 
   * @package angie.frameworks.homescreens
   * @subpackage models
   */
  abstract class FwHomescreens {

    /**
     * Tab types cache
     *
     * @var array
     */
    static private $tab_types = array();

    /**
     * Return tab types for a given user
     *
     * @param IUser $user
     * @return HomescreenTab[]
     */
    static function getTabTypes(IUser $user) {
      $user_email = $user->getEmail();

      if(!isset(self::$tab_types[$user_email])) {
        self::$tab_types[$user_email] = array(
          new SplitHomescreenTab(),
          new LeftHomescreenTab(),
          new RightHomescreenTab(),
          new CenterHomescreenTab(),
        );

        EventsManager::trigger('on_homescreen_tab_types', array(&self::$tab_types[$user_email], &$user));
      } // if

      return self::$tab_types[$user_email];
    } // getTabTypes

    /**
     * Return names of tab types that are available to $user
     *
     * @param IUser $user
     * @return array
     */
    static function getTabTypeNames(IUser $user) {
      $type_names = array();

      foreach(self::getTabTypes($user) as $type) {
        $type_names[] = get_class($type);
      } // foreach

      return $type_names;
    } // getTabTypeNames

    /**
     * Widget types cache
     *
     * @var array
     */
    static private $widget_types = array();

    /**
     * Return widget types that are available to the given user
     *
     * @param IUser $user
     * @return HomeScreenWidget[]
     */
    static function getWidgetTypes(IUser $user) {
      $user_email = $user->getEmail();

      if(!isset(self::$widget_types[$user_email])) {
        self::$widget_types[$user_email] = array();
        EventsManager::trigger('on_homescreen_widget_types', array(&self::$widget_types[$user_email], &$user));
      } // if

      return self::$widget_types[$user_email];
    } // getWidgetTypes

    /**
     * Return names of widget types that are available to $user
     *
     * @param IUser $user
     * @return array
     */
    static function getWidgetTypeNames(IUser $user) {
      $type_names = array();

      foreach(Homescreens::getWidgetTypes($user) as $type) {
        $type_names[] = get_class($type);
      } // foreach

      return $type_names;
    } // getWidgetTypeNames
  
  }