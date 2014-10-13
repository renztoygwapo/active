  <script type="text/javascript">
    {if $error_message}
      window.parent.document.getElementById('{$form_id}').upload_failed({$row_index}, '{$error_message}');
    {else}
      window.parent.document.getElementById('{$form_id}').upload_completed({$attachment_id}, {$row_index});
    {/if}
  </script>