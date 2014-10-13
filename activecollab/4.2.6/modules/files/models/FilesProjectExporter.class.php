<?php

class FilesProjectExporter extends ProjectExporter {

  /**
   * active module
   *
   * @var string
   */
  protected $active_module = FILES_MODULE;

  /**
   * Relative path where exported files will be stored
   *
   * @var string
   */
  protected $relative_path = 'files';

  /**
   * Export notebooks
   */
  public function export() {
    parent::export();
    if ($this->section == 'files') {

      $files_count = ProjectAssets::countFilesByProject($this->project, null, STATE_ARCHIVED, $this->getObjectsVisibility());

      $per_query = 500;
      $loops = ceil($files_count / $per_query);

      // create single file page for every file in the project
      $current_iteration = 0;
      while ($current_iteration < $loops) {
        $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND module = 'files' AND state >= ? AND visibility >= ?  ORDER BY ISNULL(due_on), due_on LIMIT " . $current_iteration * $per_query . ", $per_query", $this->project->getId(), STATE_ARCHIVED, $this->getObjectsVisibility());
        if ($result instanceof DBResult) {
          foreach ($result as $row) {
            $project_asset = new $row['type']();
            $project_asset->loadFromRow($row);
            $this->smarty->assignByRef('project_asset', $project_asset);
            $this->renderTemplate('file', $this->getDestinationPath('file_' . $project_asset->getId() . '.html'));
            $this->smarty->clearAssign('project_asset');

            // Render Text Document Versions
            if ($project_asset instanceof TextDocument) {
              /**
               * @var TextDocument $project_asset
               */
              $text_document_versions = $project_asset->versions()->get();
              if (is_foreachable($text_document_versions)) {
                foreach ($text_document_versions as $text_document_version) {
                  /**
                   * @var TextDocumentVersion $text_document_version
                   */
                  $this->smarty->assignByRef('text_document_version', $text_document_version);
                  $this->renderTemplate('document_version', $this->getDestinationPath('file_' . $project_asset->getId() . '_document_version_' . $text_document_version->getVersionNum() . '.html'));
                  $this->smarty->clearAssign('revision');
                } //foreach
              } //if
            } //if

            unset($row);
          } //foreach
        } //if
        set_time_limit(30);
        $current_iteration++;
      } // while

      $categories = Categories::findBy($this->project, 'AssetCategory');
      $categories_for_helper = Categories::findBy($this->project, 'AssetCategory');
      $this->smarty->assignByRef('categories', $categories_for_helper);

      // render files index page
      $this->renderTemplate('files_index', $this->getDestinationPath('index.html'));

      // render categories pages
      if (is_foreachable($categories)) {
        foreach ($categories as $category) {
          $this->smarty->assignByRef('category', $category);
          $this->renderTemplate('files_index', $this->getDestinationPath('category_' . $category->getId() . '.html'));
          $this->smarty->clearAssign('category');
        } //foreach
      } //if
    } // if

    return true;
  } // export

}