<?php

  /**
   * Labels interface definition
   *
   * @package angie.frameworks.labels
   */
  interface ILabel {
    
    /**
     * Return instance of label helper
     *
     * @return ILabelImplementation
     */
    public function label();
    
  }