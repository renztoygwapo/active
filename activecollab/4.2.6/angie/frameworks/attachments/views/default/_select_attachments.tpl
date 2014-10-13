{use_widget name="select_attachments" module=$smarty.const.FILE_UPLOADER_FRAMEWORK}

<div class="select_attachments" id="{$_select_object_attachments_id nofilter}">
  <table class="select_attachments_list" cellspacing="0"></table>

  <div class="upload_button" id="{$_select_object_attachments_id nofilter}_attach_file_button_wrapper">
    <a href="#" id="{$_select_object_attachments_id nofilter}_attach_file_button" class="link_button"><span class="inner"><span class="icon button_add">{lang}Attach Files{/lang}</span></span></a>
  </div>

  <p class="select_object_attachments_max_size details">{max_file_size_warning}</p>
</div>

<script type="text/javascript">
  (function () {
    $('#{$_select_object_attachments_id}').selectAttachments({$_select_object_attachments_uploader_options|json nofilter});
  }());
</script>