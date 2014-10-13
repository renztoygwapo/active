<?php

  class NotebooksProjectExporter extends ProjectExporter {

  	/**
  	 * active module
  	 * 
  	 * @var string
  	 */
  	protected $active_module = NOTEBOOKS_MODULE;

    /**
     * Relative path where exported files will be stored
     * 
     * @var string
     */
    protected $relative_path = 'notebooks';

    /**
     * Export notebooks
     */
    public function export() {
      parent::export();
      if ($this->section == 'notebooks') {
  	    $notebooks = Notebooks::findByProject($this->project, STATE_ARCHIVED, $this->getObjectsVisibility());

//	    render todo_list index page     
        $this->renderTemplate('notebooks_index', $this->getDestinationPath('index.html'));
//	    render notebooks
        if (is_foreachable($notebooks)) {
          foreach ($notebooks as $notebook) {
            $this->smarty->assignByRef('notebook', $notebook);
            $this->renderTemplate('notebook', $this->getDestinationPath('notebook_' . $notebook->getId() . '.html'));
            $this->smarty->clearAssign('notebook');
            //render pages
            $pages = $this->getSubPagesFromParent('Notebook', $notebook->getId());
            if (is_foreachable($pages)) {
              foreach ($pages as $page) {
                $class_page = new NotebookPage();
                $class_page->loadFromRow($page);
                $this->smarty->assignByRef('page', $class_page);
                $this->renderTemplate('page', $this->getDestinationPath('page_' . $class_page->getId() . '.html'));
                $this->smarty->clearAssign('page');
                $revisions = NotebookPageVersions::findByNotebookPage($class_page);
                if (is_foreachable($revisions)) {
                  foreach ($revisions as $revision) {
                    $this->smarty->assignByRef('revision', $revision);
                    $this->renderTemplate('revision', $this->getDestinationPath('page_' . $class_page->getId() . '_' . $revision->getVersion() . '.html'));
                    $this->smarty->clearAssign('revision');
                  } //foreach
                } //if
              } //foreach
            } //if
          } // foreach
        } // if
      } // if

      return true;
    } // export
    
    /**
     * Get all pages for a parent
     *
     * @param string $parent_type
     * @param integer $parent_id
     * @param integer $level
     */
    function getSubPagesFromParent($parent_type, $parent_id, $level = -1) {
      $level++;
      $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "notebook_pages WHERE parent_type = ? AND parent_id = ? AND state >= ? ORDER BY ISNULL(position) ASC, position", $parent_type, $parent_id, STATE_ARCHIVED);
      if (is_null($result)) {
        return array();
      } //if
      $return = array();
      if ($result instanceof DBResult) {
      	foreach ($result as $page) {
      		$return[] = $page;
        	$return = array_merge($return,$this->getSubPagesFromParent('NotebookPage', $page['id'], $level));
      	} //foreach
      } //if
  	
  	return $return;
    } //getSubPagesFromParent

  }