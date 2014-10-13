<?php

  /**
   * Notebook page specific comment implementation
   *
   * @package activeCollab.modules.notebooks
   * @subpackage model
   */
  class NotebookPageComment extends ProjectObjectComment {
  	
    /**
     * Return project to which this comment belongs to
     *
     * @return Project
     */
    function getProject() {
      if($this->getParent() instanceof NotebookPage) {
        if($this->getParent()->getNotebook() instanceof Notebook) {
          return $this->getParent()->getNotebook()->getProject();
        } else {
          throw new InvalidInstanceError('notebook', $this->getParent()->getNotebook(), 'Notebook');
        } // if
      } else {
        throw new InvalidInstanceError('parent', $this->getParent(), 'NotebookPage');
      } // if
    } // getProject
    
  }