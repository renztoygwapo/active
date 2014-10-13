  <div class="item_form">
    {wrap field=description}
      {label for=itemDescription required=yes}Item Description{/label}
      {textarea_field name='item[description]' id=itemDescription class='required validate_minlength 3' required=true}{$item_data.description}{/textarea_field}
    {/wrap}

    {wrap field=unit_cost}
      {label for=itemUnitPrice required=yes}Unit Price{/label}
      {text_field name='item[unit_cost]' value=$item_data.unit_cost id=itemUnitPrice class='required validate_number' required=true}
    {/wrap}

    {wrap field=quantity}
      {label for=itemQuantity required=yes}Item Quantity{/label}
      {number_field name='item[quantity]' value=$item_data.quantity id=itemQuantity class='required validate_number' required=true}
    {/wrap}

    {wrap field=first_tax_rate_id}
      {label for=firstItemTaxRateId}First Tax{/label}
      {select_tax_rate name='item[first_tax_rate_id]' value=$item_data.first_tax_rate_id id=firstItemTaxRateId optional=true}
    {/wrap}

    {if Invoices::isSecondTaxEnabled()}
      {wrap field=second_tax_rate_id}
        {label for=secondItemTaxRateId}Second Tax{/label}
        {select_tax_rate name='item[second_tax_rate_id]' value=$item_data.second_tax_rate_id id=secondItemTaxRateId optional=true}
      {/wrap}
    {/if}
  </div>
