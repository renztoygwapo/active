    <tr class="item {cycle values='odd,even'}" id="items_row_{$iteration}" row_number="{$iteration}">
      <td class="num"><span>#{counter name=invoice_items}</span><img src="{image_url name="layout/bits/handle-move.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" class="move_handle" /></td>
      <td class="description"><input type="text" name="invoice[items][{$iteration}][description]" value="{$invoice_item.description}" /></td>
      <td class="unit_cost"><input type="text" name="invoice[items][{$iteration}][unit_cost]" class="short number_input" value="{$invoice_item.unit_cost|money:$active_invoice->getCurrency()}" /></td>
      <td class="quantity"><input type="text" name="invoice[items][{$iteration}][quantity]" class="short number_input" value="{$invoice_item.quantity|money:$active_invoice->getCurrency()}" /></td>
      <td class="tax_rate"><input type="hidden" name="invoice[items][{$iteration}][tax_rate_id]" value="{$invoice_item.tax_rate_id}" /></td>
      <td class="subtotal" style="display: none"><input type="hidden" name="invoice[items][{$iteration}][subtotal]" value="{$invoice_item.subtotal|money:$active_invoice->getCurrency()}" /></td>
      <td class="total"><input type="text" name="invoice[items][{$iteration}][total]" value="{$invoice_item.total|money:$active_invoice->getCurrency()}" class="number_input" /></td>
      <td class="options">
        <img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" class="button_remove" />
        {if is_foreachable($invoice_item.time_record_ids)}
          {foreach from=$invoice_item.time_record_ids item=time_record}
            <input type="hidden" name="invoice[items][{$iteration}][time_record_ids][]" value="{$time_record}" />
          {/foreach}
        {/if}
      </td>
    </tr>