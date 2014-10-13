{title}Reminders{/title}

<div id="user_reminders">
    <table class="common" cellspacing="0">
      <tr>
        <th>{lang}Sent On{/lang}</th>
        <th>{lang}Sender{/lang}</th>
        <th>{lang}Related Item{/lang}</th>
        <th>{lang}Comment{/lang}</th>
        <th></th>
      </tr>
      {if is_foreachable($reminders)}
        {foreach $reminders as $reminder}
          <tr class="reminder">
            <td class="sent_on">{$reminder->getSentOn()|datetime}</td>
            <td class="sender">{user_link user=$reminder->getCreatedBy()}</td>
            <td class="object quick_view_item">{object_link object=$reminder->getParent() excerpt=50}</td>
            <td class="comment">{$reminder->getComment()|clean|nl2br nofilter}</td>
            <td class="actions">
              <a href="{$reminder->getDismissUrl(true)}" class="dismiss_reminder" title="{lang}Dismiss{/lang}"><img src="{image_url name="icons/12x12/complete.png" module=$smarty.const.REMINDERS_FRAMEWORK}" />{lang}Dismiss{/lang}</a>
            </td>
          </tr>
        {/foreach}
      {/if}
    </table>

    <p class="empty_page">{lang}There are no active reminders for this user{/lang}</p>
</div>

<script type="text/javascript">
  (function () {
    var wrapper = $('#user_reminders');
    var table = wrapper.find('table.common');
    var empty_page = wrapper.find('p.empty_page');
    var reminder_count = {$reminders|count};

    if (!reminder_count) {
      table.hide();
      empty_page.show();
      return true;
    } // if

    table.show();
    empty_page.hide();

    table.find('td.actions a').asyncLink({
      'type' : 'post',
      'success_message' : App.lang('Reminder dismissed'),
      'indicator_url' : App.Wireframe.Utils.indicatorUrl('small'),
      'success' : function (reminder) {
        $(this).parents('tr:first').remove();

        if (!table.find('tr.reminder').length) {
          table.hide();
          empty_page.show();
        } // if

        App.Wireframe.Events.trigger('reminder_deleted', [reminder]);
      }
    });

  }());
</script>