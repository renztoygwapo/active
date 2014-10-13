<?php

  /**
   * Framework level homescreen widgets manager implementation
   * 
   * @package angie.frameworks.homescreens
   * @subpackage models
   */
  abstract class FwHomescreenWidgets extends BaseHomescreenWidgets {

    /**
     * Craete a home screen widget instance
     *
     * @param string $widget_class
     * @param integer $column
     * @param array|null $settings
     * @return HomescreenWidget
     */
    static function create($widget_class, $column, $settings = null) {
      $widget = new $widget_class();

      $widget->setColumnId($column);
      if($settings && is_foreachable($settings)) {
        $widget->setAdditionalProperties($settings);
      } // if

      return $widget;
    } // create
  
    /**
     * Return widgets by home screen tab
     * 
     * @param HomescreenTab $homescreen_tab
     * @return HomescreenWidget[]
     */
    static function findByHomescreenTab(HomescreenTab $homescreen_tab) {
      $type_names = Homescreens::getWidgetTypeNames(Authentication::getLoggedUser());

      if($type_names && is_foreachable($type_names)) {
        return HomescreenWidgets::find(array(
          'conditions' => array('homescreen_tab_id = ? AND type IN (?)', $homescreen_tab->getId(), $type_names),
          'order' => 'position',
        ));
      } else {
        return null;
      } // if
    } // findByHomescreenTab
    
    /**
     * Return widgets by home screen tab
     * 
     * @param HomescreenTab $homescreen_tab
     * @return DBResult
     */
    static function countByHomescreenTab(HomescreenTab $homescreen_tab) {
      return HomescreenWidgets::count(array('homescreen_tab_id = ?', $homescreen_tab->getId()));
    } // countByHomescreenTab
    
    /**
     * Return next widget position
     * 
     * @param HomescreenTab $homescreen_tab
     * @param integer $column_id
     * @return integer
     */
    static function getNextPosition(HomescreenTab $homescreen_tab, $column_id) {
      return ((integer) DB::executeFirstCell('SELECT MAX(position) FROM ' . TABLE_PREFIX . 'homescreen_widgets WHERE homescreen_tab_id = ? AND column_id = ?', $homescreen_tab->getId(), $column_id)) + 1;
    } // getNextPosition
    
    /**
     * Remove widgets by parent home screen tab
     * 
     * @param HomescreenTab $homescreen_tab
     * @return boolean
     */
    static function deleteByHomescreenTab(HomescreenTab $homescreen_tab) {
      return DB::execute('DELETE FROM ' . TABLE_PREFIX . 'homescreen_widgets WHERE homescreen_tab_id = ?', $homescreen_tab->getId());
    } // deleteByHomescreenTab

    /**
     * Delete widgets by module
     *
     * @param AngieModule $module
     */
    static function deleteByModule(AngieModule $module) {
      $widgets = array();

      $d = dir($module->getPath() . '/models/homescreen_widgets');
      if($d) {
        while(($entry = $d->read()) !== false) {
          $class_name = str_ends_with($entry, '.class.php') ? str_replace('.class.php', '', $entry) : null;

          if($class_name) {
            $widgets[] = $class_name;
          } // if
        } // if

        $d->close();
      } // if

      if (count($widgets)) {
        DB::execute("DELETE FROM " . TABLE_PREFIX . "homescreen_widgets WHERE `type` IN (?)", $widgets);
      } // if
    } // findByModule
    
  }