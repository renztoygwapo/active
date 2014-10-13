{if $_notebook_page}
<tr class="{cycle values='odd,even'}">
  <td class="star">{favorite_object object=$_notebook_page user=$logged_user}</td>
{if $_notebook_page->getVersion() == 1}
  <td class="name"><span class="indent">{$_indent nofilter}</span>{object_link object=$_notebook_page}</td>
  <td class="version details">v1</td>
  <td class="age details">{$_notebook_page->getCreatedOn()|ago nofilter} {lang}by{/lang} {user_link user=$_notebook_page->getCreatedBy() short=yes}</td>
{else}
  <td class="name"><span class="indent">{$_indent nofilter}</span>{object_link object=$_notebook_page}</td>
  <td class="version details">v{$_notebook_page->getVersion()}</td>
  <td class="age details">{$_notebook_page->getUpdatedOn()|ago nofilter} {lang}by{/lang} {user_link user=$_notebook_page->getUpdatedBy() short=yes}</td>
{/if}
</tr>
{/if}