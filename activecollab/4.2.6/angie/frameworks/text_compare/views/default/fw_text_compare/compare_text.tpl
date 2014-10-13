<div id="page_compare">
  <div id="compared_versions">
    <table>
      <tr>
        <th class="reference">
        	{if $final_version_label == 'final'}
	          {lang}{$final_version_label|ucfirst}{/lang}
	        {else}
	          {lang version_label=$final_version_label}Version #:version_label{/lang}
	        {/if}
        <th class="compared diff">
        	{if $final_version_label == 'final'}
	          {lang compare_with_version_label=$compare_with_version_label}Diff between final version and version #:compare_with_version_label{/lang}
	        {elseif $compare_with_version_label == 'final'}
	          {lang final_version_label=$final_version_label}Diff between version #:final_version_label and final version{/lang}
	        {else}
	          {lang final_version_label=$final_version_label compare_with_version_label=$compare_with_version_label}Diff between version #:final_version_label and version #:compare_with_version_label{/lang}
	        {/if}
      </tr>
      <tr>
        <td class="reference"><h2>{$final_version.name}</h2></td>
        <td class="compared diff"><h2>{$name_diff}</h2></td>
      </tr>
      <tr>
        <td class="reference"><div class="auto_overflow">{$final_version.body|html_to_text|nl2br nofilter}</div></td>
        <td class="compared diff"><div class="auto_overflow">{$body_diff|nl2br nofilter}</div></td>
      </tr>
    </table>
  </div>
</div>