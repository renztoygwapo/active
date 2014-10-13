<?php


// Build on top of framework controller
AngieApplication::useController('preview', PREVIEW_FRAMEWORK_INJECT_INTO);

/**
 * Framework level attachments preview controller implementation
 *
 * @package angie.frameworks.attachments
 * @subpackage controller
 */
class FwAttachmentsPreviewController extends PreviewController {

  /**
   * Active object parent
   *
   * @var IAttachments
   */
  var $active_object_parent;

  /**
   * Initialize controller
   */
  function __before() {
    parent::__before();

    $this->active_object_parent = $this->active_object->getParent();
    if (!($this->active_object_parent instanceof ApplicationObject)) {
      $this->response->notFound();
    } // if

    if ($this->active_object_parent->isNew()) {
      $this->response->notFound();
    } // if
  } // __before

  /**
   * Preview file content
   */
  function preview_content() {
    if (!$this->active_object->canView($this->logged_user)) {
      $this->response->forbidden();
    } // if

    $this->response->assign(array(
      'active_object' => $this->active_object,
      'active_object_parent' => $this->active_object_parent
    ));

    // if this is not a quick view call
    if (!$this->request->isQuickViewCall()) {
      $preview_width = $this->request->get('preview_width', 800);
      $preview_height = $this->request->get('preview_height', 600);

      $this->response->assign(array(
        'preview_width' => $preview_width,
        'preview_height' => $preview_height
      ));
    } else {
      // quick view preview
      $items = array();
      $attachments = $this->active_object_parent->attachments()->get($this->logged_user);
      if (is_foreachable($attachments)) {
        foreach ($attachments as $attachment) {
          $items[] = array(
            'id'            => $attachment->getId(),
            'name'          => $attachment->getName(),
            'size'          => format_file_size($attachment->getSize()),
            'thumbnail_url' => $attachment->preview()->getThumbnailUrl(),
            'preview_url'   => $attachment->preview()->getPreviewUrl(),
            'download_url'  => $attachment->download()->getDownloadUrl(true),
            'type'          => $attachment->getMimeType()
          );
        } // foreach
      } // if

      $this->response->assign(array(
        'items' => $items,
        'current_item' => $this->active_object->getId()
      ));
    } // if

  } // preview_content

}