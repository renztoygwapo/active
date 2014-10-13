<tr class="item {cycle values='odd,even'}" id="items_row_{$iteration}">
  <td class="num"><span>#{counter name=invoice_items}</span><img src="{image_url name="layout/bits/handle-move.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" class="move_handle" /></td>
  <td class="description"><input type="text" name="invoice[items][{$iteration}][description]" value="{$recurring_profile_item->getDescription()}" /></td>
  <td class="unit_cost"><input type="text" name="invoice[items][{$iteration}][unit_cost]" class="short" value="{$recurring_profile_item->getUnitCost()|money}" /></td>
  <td class="quantity"><input type="text" name="invoice[items][{$iteration}][quantity]" class="short" value="{$recurring_profile_item->getQuantity()|money}" /></td>
  <td class="tax_rate"><input type="hidden" name="invoice[items][{$iteration}][tax_rate_id]" value="{$recurring_profile_item->getTaxRateId()}" /></td>
  <td class="subtotal" style="display: none"><input type="hidden" name="invoice[items][{$iteration}][subtotal]" value="{$recurring_profile_item->getSubtotal()|money}" /></td>
  <td class="total"><input type="text" name="invoice[items][{$iteration}][total]" value="{$recurring_profile_item->getTotal()|money}" /></td>
  <td class="options"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" class="button_remove" /></td>
</tr>