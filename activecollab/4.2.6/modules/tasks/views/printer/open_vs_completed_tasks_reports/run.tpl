{title lang=false}Open vs Completed Tasks{/title}

<div id="print_container">
{if $result}
  <table class="common" cellspacing="0">
    <tr>
      <th>{lang}Week{/lang}</th>
      <th class="center">{lang}Total Tasks{/lang}</th>
      <th class="center">{lang}Open Tasks{/lang}</th>
      <th class="center">{lang}Completed Tasks{/lang}</th>
    </tr>
  {foreach $result as $week_data}
    <tr>
      <td>{lang year=$week_data.year week=$week_data.week}:year, Week :week{/lang}</td>
      <td class="center">{$week_data.total_tasks}</td>
      <td class="center">{$week_data.total_tasks - $week_data.completed_tasks}</td>
      <td class="center">{$week_data.completed_tasks}</td>
    </tr>
  {/foreach}
  </table>
{else}
  <p>{lang}Filter returned an empty result{/lang}</p>
{/if}
</div>