{wrap field=name}
  {text_field name='company[name]' value=$company_data.name label='Name' id=company_form_name required=true}
{/wrap}

{wrap field=office_address}
  {editor_field name='company[office_address]' label='Address' id=company_form_address}{$company_data.office_address nofilter}{/editor_field}
{/wrap}

{wrap field=office_phone}
  {text_field name='company[office_phone]' value=$company_data.office_phone label='Phone Number' id=company_form_phone_number}
{/wrap}

{wrap field=office_fax}
  {text_field name='company[office_fax]' value=$company_data.office_fax label='Fax Number' id=company_form_fax_number}
{/wrap}

{wrap field=office_homepage}
  {text_field name='company[office_homepage]' value=$company_data.office_homepage label='Homepage' id=company_form_homepage}
{/wrap}

{if Companies::canSeeNotes($logged_user)}
	{wrap field=note}
		{company_note_field name='company[note]' value=$company_data.note label='Note' id=company_form_note maxlength=255}
    <span class="details block">{lang}Company note will be displayed only to people with proper permissions{/lang}</span>
  {/wrap}
{/if}