<div id="data_sources_import">
  <div class="data_source_form scrollable">
    {$active_data_source->renderImportForm()}
  </div>

  {wrap_buttons}
    {button class="close_btn" id="data_sources_importer_close_btn"}Close{/button}
  {/wrap_buttons}

</div>


<script type="text/javascript">
  $('#data_sources_import').each(function() {
    var wrapper = $(this);
    var close_btn = wrapper.find('button.close_btn');
    close_btn.click(function() {
      App.widgets.FlyoutDialog.front().close(true);
    });
  })
</script>