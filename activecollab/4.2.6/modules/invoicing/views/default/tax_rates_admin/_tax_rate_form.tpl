<div class="tax_rate_form">
  {wrap field=name}
    {label for=tax_rateName required=yes}Name{/label}
    {text_field name="tax_rate[name]" value=$tax_rate_data.name id=tax_rateName class=required}
  {/wrap}
  
  {wrap field=percentage}
    {label for=tax_ratePercentage}Tax Percentage {/label}
    {decimal_field name="tax_rate[percentage]" value=$tax_rate_data.percentage id=tax_ratePercentage class=short min="0" max="99.999" step="0.001" disabled=$active_tax_rate->isUsed()} %
  {/wrap}
</div>