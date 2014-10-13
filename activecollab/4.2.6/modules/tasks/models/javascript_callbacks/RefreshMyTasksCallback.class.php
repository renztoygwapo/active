<?php

  /**
   * Refresh my tasks menu item callback
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class RefreshMyTasksCallback extends JavaScriptCallback {

    /**
     * My Tasks wrapper ID
     *
     * @var string
     */
    private $id;

    /**
     * Construct handler instance
     *
     * @param string $id
     * @throws InvalidParamError
     */
    function __construct($id = 'my_tasks') {
      if(empty($id)) {
        throw new InvalidParamError('id', $id, 'ID is required');
      } // if

      $this->id = $id;
    } // __construct

    /**
     * Render callback
     *
     * @return string
     */
    function render() {
      return '(function() { $(this).click(function(e) { $("#' . $this->id . '").myTasks("refresh", true); e.stopPropagation(); e.preventDefault(); return false; }); })';
    } // render

  }