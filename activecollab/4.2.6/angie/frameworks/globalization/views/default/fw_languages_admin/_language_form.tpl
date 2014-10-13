{wrap field=name}
  {label for=languageName required=yes}Name{/label}
  {text_field name='language[name]' value=$language_data.name id=languageName required=true}
{/wrap}

{wrap field=type}
  {label for=languageLocale required=yes}Locale{/label}
  {select_locale name='language[locale]' value=$language_data.locale id=languageLocale required=true class="select_locale_slc"}
{/wrap}

{wrap field=decimal_separator}
  {select_decimal_separator name='language[decimal_separator]' value=$language_data.decimal_separator label="Decimal Separator"}
{/wrap}

{wrap field=thousands_separator}
  {select_thousands_separator name='language[thousands_separator]' value=$language_data.thousands_separator label="Thousands Separator"}
{/wrap}