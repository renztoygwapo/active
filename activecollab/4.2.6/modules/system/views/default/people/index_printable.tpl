{if $archived}
  {title}Archived Companies{/title}
{else}
  {title}Active Companies{/title}
{/if}

<div id="companies">
{if is_foreachable($companies)}
  <table>
  {foreach from=$companies item=company}
    <tr class="{cycle values='odd,even'}">
      <td class="icon"><img src="{$company->avatar()->getUrl(ICompanyAvatarImplementation::SIZE_MEDIUM)}" alt="" /></td>
      <td class="name">{company_link company=$company}
      {if $company->isOwner()}
        <span class="details">({lang}Owner Company{/lang})</span>
      {/if}
      </td>
      <td>
        {if $company->getConfigValue('office_phone')}
          {lang}Phone Number{/lang}: {$company->getConfigValue('office_phone')}<br />
        {/if}
        {if $company->getConfigValue('office_fax')}
          {lang}Fax Number{/lang}: {$company->getConfigValue('office_fax')}<br />
        {/if}
        {if is_valid_url($company->getConfigValue('office_homepage'))}
          {lang}Homepage{/lang}: <a href="{$company->getConfigValue('office_homepage')}">{$company->getConfigValue('office_homepage')}</a><br />
        {/if}
        {if $company->getConfigValue('office_address')}
          {lang}Address{/lang}: {$company->getConfigValue('office_address')|clean|nl2br nofilter}
        {/if}
      </td>
    </tr>
  {/foreach}
  </table>
{else}
  <p class="empty_page">{lang}There are no companies defined in the database{/lang}</p>
{/if}

</div>