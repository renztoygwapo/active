{form action=Router::assemble('invoicing_settings_change_description_formats')}
  {wrap_fields}
    {wrap field=invoice_item_formats}
      {pattern_field name="formats[description_format_grouped_by_task]" value=$formats_data.description_format_grouped_by_task variables='job_type,task_id,task_summary,project_name' default_format=$smarty.const.Invoices::DEFAULT_TASK_DESCRIPTION_FORMAT  label='When Records are Grouped by Task'}
    {/wrap}

    {wrap field=invoice_item_formats}
      {pattern_field name="formats[description_format_grouped_by_project]" value=$formats_data.description_format_grouped_by_project variables='name,client,category' default_format=$smarty.const.Invoices::DEFAULT_PROJECT_DESCRIPTION_FORMAT  label='When Records are Grouped by Project'}
    {/wrap}

    {wrap field=invoice_item_formats}
      {pattern_field name="formats[description_format_grouped_by_job_type]" value=$formats_data.description_format_grouped_by_job_type variables='job_type' default_format=$smarty.const.Invoices::DEFAULT_JOB_TYPE_DESCRIPTION_FORMAT  label='When Records are Grouped by Job Type'}
    {/wrap}

    {wrap field=invoice_item_formats}
      {pattern_field name="formats[description_format_separate_items]" value=$formats_data.description_format_separate_items variables='job_type_or_category,record_summary,record_date,parent_task_or_project,project_name' default_format=$smarty.const.Invoices::DEFAULT_INDIVIDUAL_DESCRIPTION_FORMAT label='When Records are Displayed as Individual Invoice Items'}
    {/wrap}

    {wrap field=record_summary_transoformations}
      {label}Transform Non-Empty Record Summary{/label}
      <select name="formats[first_record_summary_transformation]">
        <option value="">{lang}Don't Change{/lang}</option>
        <option value="{$smarty.const.Invoices::SUMMARY_PUT_IN_PARENTHESES}" {if $formats_data.first_record_summary_transformation == $smarty.const.Invoices::SUMMARY_PUT_IN_PARENTHESES}selected{/if}>{lang}Put in between '(' and ')'{/lang}</option>
        <option value="{$smarty.const.Invoices::SUMMARY_PREFIX_WITH_DASH}" {if $formats_data.first_record_summary_transformation == $smarty.const.Invoices::SUMMARY_PREFIX_WITH_DASH}selected{/if}>{lang}Prefix with ' - '{/lang}</option>
        <option value="{$smarty.const.Invoices::SUMMARY_SUFIX_WITH_DASH}" {if $formats_data.first_record_summary_transformation == $smarty.const.Invoices::SUMMARY_SUFIX_WITH_DASH}selected{/if}>{lang}Sufix with ' - '{/lang}</option>
        <option value="{$smarty.const.Invoices::SUMMARY_PREFIX_WITH_COLON}" {if $formats_data.first_record_summary_transformation == $smarty.const.Invoices::SUMMARY_PREFIX_WITH_COLON}selected{/if}>{lang}Prefix with ' :'{/lang}</option>
        <option value="{$smarty.const.Invoices::SUMMARY_SUFIX_WITH_COLON}" {if $formats_data.first_record_summary_transformation == $smarty.const.Invoices::SUMMARY_SUFIX_WITH_COLON}selected{/if}>{lang}Sufix with ': '{/lang}</option>
      </select>
      {lang}and then{/lang}
      <select name="formats[second_record_summary_transformation]">
        <option>{lang}Don't Change{/lang}</option>
        <option value="{$smarty.const.Invoices::SUMMARY_PUT_IN_PARENTHESES}" {if $formats_data.second_record_summary_transformation == $smarty.const.Invoices::SUMMARY_PUT_IN_PARENTHESES}selected{/if}>{lang}Put in between '(' and ')'{/lang}</option>
        <option value="{$smarty.const.Invoices::SUMMARY_PREFIX_WITH_DASH}" {if $formats_data.second_record_summary_transformation == $smarty.const.Invoices::SUMMARY_PREFIX_WITH_DASH}selected{/if}>{lang}Prefix with ' - '{/lang}</option>
        <option value="{$smarty.const.Invoices::SUMMARY_SUFIX_WITH_DASH}" {if $formats_data.second_record_summary_transformation == $smarty.const.Invoices::SUMMARY_SUFIX_WITH_DASH}selected{/if}>{lang}Sufix with ' - '{/lang}</option>
        <option value="{$smarty.const.Invoices::SUMMARY_PREFIX_WITH_COLON}" {if $formats_data.second_record_summary_transformation == $smarty.const.Invoices::SUMMARY_PREFIX_WITH_COLON}selected{/if}>{lang}Prefix with ' :'{/lang}</option>
        <option value="{$smarty.const.Invoices::SUMMARY_SUFIX_WITH_COLON}" {if $formats_data.second_record_summary_transformation == $smarty.const.Invoices::SUMMARY_SUFIX_WITH_COLON}selected{/if}>{lang}Sufix with ': '{/lang}</option>
      </select>
      <p class="aid">{lang}Record summary can be empty, so you should prepare format that works for both cases: when summary is available and when it is not present{/lang}</p>
    {/wrap}
  {/wrap_fields}

  {wrap_buttons}
    {submit}Save Changes{/submit}
  {/wrap_buttons}
{/form}