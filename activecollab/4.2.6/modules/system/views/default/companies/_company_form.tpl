<div class="content_stack_wrapper">
  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3>{lang}Company Name{/lang} *</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=name}
        {text_field name='company[name]' value=$company_data.name required=true}
      {/wrap}
    </div>
  </div>

  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3>{lang}Contact Details{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=office_address}
        {address_field name='company[office_address]' label="Address" value=$company_data.office_address}
      {/wrap}

      {wrap field=office_phone}
        {text_field name='company[office_phone]' value=$company_data.office_phone label="Phone Number"}
      {/wrap}

      {wrap field=office_fax}
        {text_field name='company[office_fax]' value=$company_data.office_fax label="Fax Number"}
      {/wrap}

      {wrap field=office_homepage}
        {url_field name='company[office_homepage]' value=$company_data.office_homepage label="Homepage"}
      {/wrap}
    </div>
  </div>

{if Companies::canSeeNotes($logged_user)}
  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3>{lang}Note{/lang}</h3>
      <p class="aid">{lang}Company note will be displayed only to people with proper permissions{/lang}</p>
    </div>
    <div class="content_stack_element_body">
      {wrap field=note}
        {company_note_field name='company[note]' value=$company_data.note maxlength=255}
      {/wrap}
    </div>
  </div>
{/if}
</div>