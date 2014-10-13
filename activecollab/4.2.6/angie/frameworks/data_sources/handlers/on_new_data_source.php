<?php

  /**
   * On new Data Source
   *
   * @param $sources
   */
  function data_sources_handle_on_new_data_source(&$sources) {
    $sources[] = new Basecamp();

  } //data_sources_handle_on_new_source