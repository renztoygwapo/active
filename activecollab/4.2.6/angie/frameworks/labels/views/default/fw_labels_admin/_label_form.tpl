{wrap field=name}
  {text_field name="label[name]" value=$label_data.name maxlength=10 label="Name" required=true}
{/wrap}

{wrap field=name}
  {color_field name="label[fg_color]" value=$label_data.fg_color label="Text Color"}
{/wrap}

{wrap field=name}
  {color_field name="label[bg_color]" value=$label_data.bg_color label="Background Color"}
{/wrap}