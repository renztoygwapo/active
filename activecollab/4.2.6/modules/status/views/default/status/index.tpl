{if !$request->isAsyncCall()}
  {title}Status Updates Archive{/title}
  {add_bread_crumb}Archive{/add_bread_crumb}
{/if}
{use_widget name="status_update" module="status"}

{assign var=dialog_id value=HTML::uniqueId('status_updates_dialog')}

<div id="{$dialog_id}" class="status_updates_dialog">

  <div class="table_wrapper context_popup_scrollable"><div class="table_wrapper_inner">
    <table class="status_updates" id="status_updates_table" cellspacing="0">
      <tbody class="first_level">

      </tbody>
    </table>
  </div></div>

  <div id="add_status_message"><div id="add_status_message_wrapper">
    <div class="author_avatar">
      <img src="{$logged_user->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)}" alt="" />
    </div>

    <div class="author_message">
      <textarea placeholder="{lang}Type your message and hit Enter to post it{/lang}" maxlength="255"></textarea>
      <p class="details status_counter"></p>
    </div>
  </div></div>

{if !$request->isAsyncCall() && $pagination}
  {pagination pager=$pagination}{$pagination_url}{/pagination}
{/if}

</div>

<script type="text/javascript">
  var status_update_dialog = $("#{$dialog_id}");

  // initialize status update dialog
  status_update_dialog.statusUpdate({
    'add_message_url' : {$add_status_message_url|json nofilter},
    'logged_user'     : {$logged_user|json nofilter},
    'status_updates'  : {$status_updates|json nofilter}
  });

  // do popup specific stuff
  var popup = status_update_dialog.parents('#context_popup:first');
  if (popup.length) {

    var trigger = popup.data('trigger');
    if (trigger.length) {
      // add popup buttons
      trigger.contextPopup('addButton', 'browse_archives', "{lang}Browse Archive{/lang}", '{assemble route=status_updates}', '{image_url name="icons/16x16/archive-mono.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}', false, true);
      //trigger.contextPopup('addButton', 'rss_subscribe', '{lang}Track Using RSS{/lang}', '{$rss_url}', '{image_url name="icons/16x16/rss.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}');
    } // if

    // set trigger badge to 0
    App.Wireframe.Statusbar.setItemBadge(trigger.attr('id'), 0);
  } // if

</script>