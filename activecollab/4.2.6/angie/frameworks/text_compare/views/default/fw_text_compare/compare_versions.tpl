{if !$request->isInlineCall()}
<div id="page_compare">
	{form action="{Router::assemble('compare_versions')}" method=post}
	  <table id="versions_to_compare" cellspacing="0">
	    <tr>
	      <td class="select_versions">{lang}Compare{/lang} {select_version name=new versions=$versions version_label=$left_version_label id=left_version_select} {lang}with{/lang} {select_version name=old versions=$versions version_label=$right_version_label id=right_version_select}</td>
	      <td class="go">{button type=submit class=grey_button}Go{/button}</td>
	    </tr>
	  </table>
  {/form}
  
  <div id="compared_versions">
{/if}
    <table cellspacing="0">
      <tr>
        <th class="reference">{lang left_version_label=$left_version_label}Version #:left_version_label{/lang}</th>
        <th class="compared diff">{lang left_version_label=$left_version_label right_version_label=$right_version_label}Diff between version #:left_version_label and version #:right_version_label{/lang}</th>
      </tr>
      <tr>
        <td class="reference"><h2>{$left_version.name}</h2></td>
        <td class="compared diff"><h2>{$name_diff nofilter}</h2></td>
      </tr>
      <tr>
        <td class="reference"><div class="auto_overflow">{$left_version.body|html_to_text|nl2br nofilter}</div></td>
        <td class="compared diff"><div class="auto_overflow">{$body_diff|nl2br nofilter}</div></td>
      </tr>
    </table>
{if !$request->isInlineCall()}
  </div>
</div>
{/if}