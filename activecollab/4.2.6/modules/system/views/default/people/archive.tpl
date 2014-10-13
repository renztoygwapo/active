{title}Archived Companies{/title}
{add_bread_crumb}Archived Companies{/add_bread_crumb}

<div id="companies">
{if is_foreachable($companies)}
  {if $pagination->getLastPage() > 1}
    <div class="pagination_container top">{pagination pager=$pagination}{assemble route=people_archive page='-PAGE-'}{/pagination}</div>
  {/if}
  
    <table>
    {foreach from=$companies item=company}
      <tr class="{cycle values='odd,even'}">
        <td class="icon"><img src="{$company->avatar()->getUrl(ICompanyAvatarImplementation::SIZE_SMALL)}" alt="" /></td>
        <td class="name">{company_link company=$company}
        {if $company->isOwner()}
          <span class="details">({lang}Owner Company{/lang})</span>
        {/if}
        </td>
      </tr>
    {/foreach}
    </table>
    
  {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
    <p class="next_page"><a href="{assemble route=people_archive page=$pagination->getNextPage()}">{lang}Next Page{/lang}</a></p>
  {/if}
{else}
    <p class="empty_page"><span class="inner">{lang}There are no companies in the archive{/lang}</span></p>
{/if}
</div>