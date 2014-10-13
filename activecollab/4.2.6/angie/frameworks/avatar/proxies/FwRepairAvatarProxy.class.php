<?php

/**
 * Repair avatar proxy
 *
 * @package angie.frameworks.avatars
 * @subpackage proxies
 */
class FwRepairAvatarProxy extends ProxyRequestHandler {

  /**
   * Id of object we want to find avatar for
   *
   * @var integer
   */
  protected $object_id;

  /**
   * Size needed
   *
   * @var int
   */
  protected $size;

  /**
   * Folder where avatars are stored
   *
   * @var string
   */
  protected $folder;

  /**
   * Available sizes
   *
   * @var array
   */
  protected $available_sizes;

  /**
   * extension
   *
   * @var string
   */
  protected $extension = 'png';

  /**
   * Construct proxy request handler
   *
   * @param array $params
   */
  function __construct($params = null) {
    $this->object_id = isset($params['object_id']) && $params['object_id'] ? trim($params['object_id']) : null;
    $this->size = isset($params['size']) && $params['size'] ? $params['size'] : null;
    $this->folder = isset($params['folder']) && $params['folder'] ? $params['folder'] : null;
    $this->available_sizes = isset($params['available_sizes']) && $params['available_sizes'] ? $params['available_sizes'] : array();
    rsort($this->available_sizes);

    if (!$this->object_id || !$this->size || !$this->folder || !$this->available_sizes) {
      $this->badRequest();
    } // if
  } // __construct

  /**
   * Get path to avatar
   *
   * @param mixed $id
   * @param int $size
   * @param string $extension
   * @return string
   */
  function getPath($id, $size, $extension = 'png') {
    if ($size) {
      $size_part = $size . 'x' . $size;
    } else {
      $size_part = 'original';
    } // if

    return ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/' . $this->folder . '/' . $id . '.' . $size_part . '.' . $extension;
  } // getPath

  /**
   * Forward thumbnail
   */
  function execute() {
    require_once ANGIE_PATH . '/functions/general.php';
    require_once ANGIE_PATH . '/functions/errors.php';
    require_once ANGIE_PATH . '/functions/web.php';

    $desired_path = $this->getPath($this->object_id, $this->size, 'png');
    if (is_file($desired_path)) {
      $avatar_path = $desired_path;
    } else {
      $supported_extensions = array('png', 'jpg', 'gif');
      $avatar_path = false;
      if (is_array($this->available_sizes) && count($this->available_sizes)) {
        foreach ($supported_extensions as $extension) {
          $possible_image = $this->getPath($this->object_id, false, $extension);
          if (is_file($possible_image)) {
            $source_image = $possible_image;
            break;
          } // if
          foreach ($this->available_sizes as $size) {
            $possible_image = $this->getPath($this->object_id, $size, $extension);
            if (is_file($possible_image)) {
              $source_image = $possible_image;
              break;
            } // if
          } // foreach
        } // foreach
      } // if

      if ($source_image && is_file($source_image)) {
        $resource = open_image($source_image);

        foreach ($this->available_sizes as $size) {
          $current_path = $this->getPath($this->object_id, $size, 'png');
          if (!is_file($current_path)) {
            scale_image($resource, $current_path, $size, $size, IMAGETYPE_PNG, 100, true);
          } // if
        } // foreach

        $full_size = $current_path = $this->getPath($this->object_id, false, 'png');
        if (!is_file($full_size)) {
          $max_size = max($this->available_sizes);
          scale_image($resource, $current_path, $max_size, $max_size, IMAGETYPE_PNG, 100, true);
        } // if
      } // if

      $avatar_path = $desired_path;
    } // if

    if (!$avatar_path) {
      $avatar_path = $this->getPath('default', $this->size, 'png') ;
    } // if

    if (is_file($avatar_path)) {
      download_file($avatar_path, 'image/' . $this->extension, 'avatar.' . $this->extension, false, true);
    } else {
      $this->notFound();
    } // if
  } // execute

} // FwRepairAvatarProxy