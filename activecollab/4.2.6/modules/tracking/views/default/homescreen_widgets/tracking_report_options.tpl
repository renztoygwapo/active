<div id="homescreen_widget_{$widget->getId()}" class="tracked_time_options_wrapper">
  {wrap field=tracking_report_caption}
    {text_field name='homescreen_widget[caption]' value=$widget_data.caption label='Caption'}
    <p class="aid">{lang value=$widget->getName()}Leave blank to use ":value"{/lang}</p>
  {/wrap}

  {wrap field=tracking_report_user_filter class=tracking_report_user_filter}
    {select name='homescreen_widget[user_filter]' label='Show For'}
      <option value="anybody" {if $widget_data.user_filter == 'anybody'}selected="selected"{/if}>{lang}Everyone{/lang}</option>
      <option value="logged_user" {if $widget_data.user_filter == 'logged_user'}selected="selected"{/if}>{lang}Logged in User{/lang}</option>
    {/select}

    {if $widget_data.user_filter == 'logged_user'}
      <p class="aid">{lang}System will display detailed results for the logged in user, grouped by date{/lang}</p>
    {else}
      <p class="aid">{lang}Results will be summarized by user and grouped by date{/lang}</p>
    {/if}
  {/wrap}

  {wrap field=tracking_report_billable_status_filter}
    {select name='homescreen_widget[billable_status_filter]' label='Status'}
      <option value="all" {if $widget_data.billable_status_filter == 'all'}selected="selected"{/if}>{lang}Any{/lang}</option>
      <option value="not_billable" {if $widget_data.billable_status_filter == 'not_billable'}selected="selected"{/if}>{lang}Non-Billable{/lang}</option>
      <option value="billable" {if $widget_data.billable_status_filter == 'billable'}selected="selected"{/if}>{lang}Billable{/lang}</option>
      <option value="pending_payment" {if $widget_data.billable_status_filter == 'pending_payment'}selected="selected"{/if}>{lang}Pending Payment{/lang}</option>
      <option value="billable_not_paid" {if $widget_data.billable_status_filter == 'billable_not_paid'}selected="selected"{/if}>{lang}Not Yet Paid (Billable or Pending Payment){/lang}</option>
      <option value="billable_paid" {if $widget_data.billable_status_filter == 'billable_paid'}selected="selected"{/if}>{lang}Already Paid{/lang}</option>
    {/select}
  {/wrap}

  {wrap field=tracking_report_days_filter}
    {select name='homescreen_widget[days_filter]' label='Date Range'}
      <option value="7" {if $widget_data.days_filter == 7}selected="selected"{/if}>{lang num=7}Last :num Days{/lang}</option>
      <option value="15" {if $widget_data.days_filter == 15}selected="selected"{/if}>{lang num=15}Last :num Days{/lang}</option>
      <option value="30" {if $widget_data.days_filter == 30}selected="selected"{/if}>{lang num=30}Last :num Days{/lang}</option>
    {/select}
  {/wrap}
</div>

<script type="text/javascript">
  $('#homescreen_widget_{$widget->getId()}').each(function() {
    var wrapper = $(this);

    wrapper.find('div.tracking_report_user_filter select').change(function() {
      if($(this).val() == 'logged_user') {
        wrapper.find('div.tracking_report_user_filter p.aid').text(App.lang('Results will be grouped by date')).highlightFade();
      } else {
        wrapper.find('div.tracking_report_user_filter p.aid').text(App.lang('Results will be summarized by user and grouped by date')).highlightFade();
      } // if
    });
  });
</script>