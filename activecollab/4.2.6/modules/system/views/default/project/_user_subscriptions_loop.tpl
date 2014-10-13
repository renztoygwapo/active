{foreach from=$subscriptions item=subscription}
  <tr class="subscriptions_row">
    <td class="name"><span class="subscription"><span class="object_type object_type_{$subscription.type_short}">{lang}{$subscription.type}{/lang}</span> <a href="{$subscription.object_link}" class="quick_view_item">{$subscription.name|excerpt:100}</a></span></td>
    <td class="author"><span class="details block">{action_on_by user=Users::findById($subscription.created_by_id) datetime=DateValue::makeFromString($subscription.created_on)}</span></td>
    <td class="checkbox">{checkbox name="unsubscribe" class=unsubscribe_checkbox value=$subscription.subscription_id}</td>
  </tr>
{/foreach}