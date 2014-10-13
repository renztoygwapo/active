{wrap field=name}
  {text_field name='currency[name]' value=$currency_data.name label="Name" required=true}
{/wrap}

{wrap field=code}
  {text_field name='currency[code]' value=$currency_data.code label="Code" required=true}
{/wrap}

{wrap field=decimal_spaces}
  {select_number_of_decimal_spaces name='currency[decimal_spaces]' value=$currency_data.decimal_spaces label="Number of Decimal Spaces"}
{/wrap}

{wrap field=decimal_rounding}
  {select_decimal_rounding name='currency[decimal_rounding]' value=$currency_data.decimal_rounding label="Decimal Rounding"}
{/wrap}