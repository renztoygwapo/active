<div id="aggregated_tasks_report">
  {$rendered_report_result nofilter}
  
  {if $report->getData()}
    <table class="common" cellspacing="0">
      <tr>
        <th class="name">{lang}Group{/lang}</th>
        <th class="value">{lang}Tasks{/lang}</th>
      </tr>
    
    {foreach $report->getData() as $serie}
      <tr>
      	<td class="name">{$serie->getOption('label')}</td>
        <td class="value">{$serie->getPoint(0)->getY()}</td>
      </tr>
    {/foreach}
    </table>
  {/if}
</div>