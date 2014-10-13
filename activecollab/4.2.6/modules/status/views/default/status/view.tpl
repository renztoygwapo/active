{title}Status Update{/title}
{add_bread_crumb}View{/add_bread_crumb}
{use_widget name="status_update" module="status"}

{assign var=dialog_id value=HTML::uniqueId('status_updates_dialog')}
<div id="{$dialog_id}" class="status_updates_dialog">

  <div class="table_wrapper context_popup_scrollable"><div class="table_wrapper_inner">
    <table class="status_updates" id="status_updates_table" cellspacing="0">
      <tbody class="first_level">
        {include file=get_view_path('_status_row', 'status', 'status')}
      </tbody>
    </table>
  </div></div>
</div>

<script type="text/javascript">
  var status_update_dialog = $("#{$dialog_id}");

  // initialize status update dialog
  status_update_dialog.statusUpdate();
</script>