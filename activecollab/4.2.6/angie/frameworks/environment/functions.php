<?php

  /**
   * Return object link
   * 
   * @param ApplicationObject $object
   * @param integer $excerpt
   * @param array $additional
   * @param boolean $quick_view
   * @return string
   */
  function object_link($object, $excerpt = null, $additional = null, $quick_view = false) {
    if($object instanceof ApplicationObject) {

      if ($quick_view) {
        if (isset($additional['class'])) {
          $additional['class'] .= ' quick_view_item';
        } else {
          $additional['class'] = 'quick_view_item';
        } // if
      } // if

      $formatted_additional = '';
      if (is_foreachable($additional)) {
        foreach ($additional as $additional_key => $additional_value) {
          $formatted_additional[] = $additional_key . '="' . $additional_value . '"';
        } // foreach
        $formatted_additional = implode(' ', $formatted_additional);
      } // if


      $link = '<a href="' . $object->getViewUrl() . '" ' . $formatted_additional . '>' . clean(
        ($excerpt ? str_excerpt($object->getName(), $excerpt) : $object->getName())
      ) . '</a>';
      
      if($object instanceof IComplete && $object->complete()->isCompleted()) {
        return '<del class="completed">' . $link . '</del>';
      } else {
        return $link;
      } // if
    } else {
      return '';
    } // if
  } // object_link

  /**
   * Cleanup quick jump/add cache for $user if specified, if not for everyone
   *
   * @param User $user
   */
  function clean_menu_projects_and_quick_add_cache($user = null) {
    if($user instanceof User) {
      AngieApplication::cache()->removeByObject($user, 'quick_add');
      AngieApplication::cache()->removeByObject($user, 'quick_jump');
    } else {
      AngieApplication::cache()->removeByModel('users');
    } // if
  } // clean_menu_projects_and_quick_add_cache
  
  // ------------------------------------------------------------
  //  Map app stuff with files / resolve paths
  // ------------------------------------------------------------
  
  /**
   * Return path of specific template
   *
   * @param string $view
   * @param string $controller_name
   * @param string $module_name
   * @param string $interface
   * @return string
   */
  function get_view_path($view, $controller_name = null, $module_name = DEFAULT_MODULE, $interface = null) {
    return AngieApplication::getViewPath($view, $controller_name, $module_name, $interface);
  } // get_view_path

  /**
   * Get the file icon for the specified file
   *
   * @param string $filename
   * @param string $size
   * @return string
   */
  function get_file_icon_url($filename, $size) {
    if (!$size) {
      $size = '16x16';
    } // if

    $extension = strtolower(get_file_extension($filename));
    $path = ENVIRONMENT_FRAMEWORK_PATH . "/assets/default/images/file-types/{$size}/{$extension}.png";

    return is_file($path) ?
      AngieApplication::getImageUrl("file-types/{$size}/$extension.png", ENVIRONMENT_FRAMEWORK) :
      AngieApplication::getImageUrl("file-types/{$size}/default.png", ENVIRONMENT_FRAMEWORK);
  } // get_file_icon

  // ---------------------------------------------------
  //  Used by on_extra_stats event
  // ---------------------------------------------------

  /**
   * Return counts by state as string
   *
   * @param mixed $data
   * @return string
   */
  function counts_by_state_as_string($data) {
    $result = array(
      STATE_DELETED => 0,
      STATE_TRASHED => 0,
      STATE_ARCHIVED => 0,
      STATE_VISIBLE => 0,
    );

    if($data && is_foreachable($data)) {
      foreach($data as $row) {
        if(array_key_exists('state', $row) && array_key_exists('records_count', $row)) {
          $result[(integer) $row['state']] = (integer) $row['records_count'];
        } // if
      } // foreach
    } // if

    return implode(',', $result);
  } // counts_by_state