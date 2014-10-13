<div>
  <table class="common lang_table" cellspacing="0">
    <tr>
      <th>{lang}Dictionary Word/Sentence{/lang}</th>
      <th></th>
      <th class="input_column">{lang}Translated Word/Sentence{/lang}</th>
    </tr>

    {if is_foreachable($translate_data)}
      {foreach from=$translate_data item=row}
        <tr class='{cycle values="odd,even"}'>
          <td class="dictionary">{$row.phrase}</td>
          <td class="copy_arrow"><img src="{image_url name="icons/16x16/proceed.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="copy" /></td>
          <td class="input{if !$row.translation} new{/if}">
            {if strlen($row.phrase) < 70}
              {text_field name=$row.hash value=$row.translation saved_translation=$row.translation}
            {else}
              {textarea_field class=language_textarea name=$row.hash saved_translation=$row.translation}{$row.translation nofilter}{/textarea_field}
            {/if}
          </td>
          <td class="actions">
            <img src="{image_url name="layout/bits/indicator-pending.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="copy" class="indicator"/>
          </td>
        </tr>
      {/foreach}
    {/if}
  </table>
</div>