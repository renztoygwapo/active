{title lang=false}{lang name=$active_invoice->getName()}:name Time and Expenses{/lang}{/title}
{add_bread_crumb}Time & Expenses{/add_bread_crumb}
{use_widget name="form" module="environment"}

<div id="invoice_time">
  {form method="post" action=$active_invoice->getReleaseUrl() class="release_form"}
  {if is_foreachable($time_records) || is_foreachable($expenses)}
    <div id="timerecords">
      <table class="common" cellspacing="0">
        <thead>
          <tr>
            <th class="item">{lang}Item{/lang}</th>
            <th class="date left">{lang}Date{/lang}</th>
            <th class="user">{lang}User{/lang}</th>
            <th class="hours">{lang}Value{/lang}</th>
            <th class="description">{lang}Description{/lang}</th>
            {if $items_can_be_released}
            <th class="action">{checkbox class="check_all_items" name="all_chx"}</th>
            {/if}
          </tr>
        </thead>
        <tbody>
      {foreach from=$time_records item=time_record}
        <tr>
          <td class="type">{$time_record->getVerboseType()}</td>
          <td class="date left">{$time_record->getRecordDate()|date:0}</td>
          <td class="user">{user_link user=$time_record->getUser()}</td>
          <td class="hours">{$time_record->getName(true)}</td>
          <td class="description">
          {if $time_record->getParent() instanceof ProjectObject}
            {object_link object=$time_record->getParent()}
            {if $time_record->getSummary()}
              &mdash; {$time_record->getSummary()}
            {/if}
          {else}
            {$time_record->getSummary()}
          {/if}
          </td>
          {if $items_can_be_released}
          <td class="action">{checkbox name="release_times[]" value=$time_record->getId() class="action_chx"}</td>
          {/if}
        </tr>
      {/foreach}
      {foreach from=$expenses item=expense}
        <tr>
          <td class="type">{$expense->getVerboseType()}</td>
          <td class="date left">{$expense->getRecordDate()|date:0}</td>
          <td class="user">{user_link user=$expense->getUser()}</td>
          <td class="hours">{$expense->getName(true, true)}</td>
          <td class="description">
          {if $expense->getParent() instanceof ProjectObject}
            {object_link object=$expense->getParent()}
            {if $expense->getSummary()}
              &mdash; {$expense->getSummary()}
            {/if}
          {else}
            {$expense->getSummary()}
          {/if}
          </td>
          {if $items_can_be_released}
          <td class="action">{checkbox name="release_expenses[]" value=$expense->getId() class="action_chx"}</td>
          {/if}
        </tr>
      {/foreach}
        </tbody>
      {if $items_can_be_released}
        <tfoot>
          <tr>
            <td class="right" id="release_invoice_time_records" colspan="{if $items_can_be_released}6{else}5{/if}">{button confirm="Are you sure that you want to remove relation between this invoice and time records listed above? Note that time records will NOT be deleted!" type="submit"}Release{/button}</td>
          </tr>
        </tfoot>
      {/if}
      </table>
    </div>

    {empty_slate name=time module=invoicing}
  {else}
    <p class="empty_page"><span class="inner">{lang}There is no time attached to this invoice{/lang}</span></p>
  {/if}
  {/form}
</div>

<script type="text/javascript">
  $('#invoice_time').each(function() {
    var wrapper = $(this);

    var check_all_items = wrapper.find('.check_all_items').change(function(){
      var action_chx = wrapper.find('.action_chx');

      if($(this).attr('checked')) {
        action_chx.prop('checked', true);
      } else {
        action_chx.prop('checked', false);
      } // if
    });

    var form = wrapper.find('form.release_form');

    form.submit(function() {
      if(form.find('input[type=checkbox]:checked').length > 0) {
        form.ajaxSubmit({
          'url' : App.extendUrl(form.attr('action'), {
            'async' : 1
          }),
          'type' : 'post',
          'success' : function(response) {
            App.Wireframe.Flash.success(App.lang('Related invoice items have been released'));
            App.Wireframe.Content.setFromUrl(response['urls']['view']);
          },
          'error' : function(response) {
            App.Wireframe.Flash.error(App.lang('An error occurred while trying to release related invoice items'));
          }
        });
      } else {
        App.Wireframe.Flash.error('Please choose items that you want to release');
      } // if

      return false;
    });
  });
</script>