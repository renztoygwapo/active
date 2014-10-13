<h2>{lang}Versions{/lang}</h2>
<table class="file_versions common">
  <tr class="file_version latest {cycle values='odd,even'}">
	        <td class="version"><a href="{$_file_versions_file->getDownloadUrl(true)}" target="_blank">{lang}Latest Version{/lang}</a></td>
	        <td class="size">{$_file_versions_file->getSize()|filesize}</td>
	        <td class="details">
            {$_file_versions_file->getLastVersionOn()|ago nofilter} {lang}by{/lang} {user_link user=$_file_versions_file->getLastVersionBy()}
	        </td>
	        <td class="options">
	          <span class="latest_file_version">{lang}Latest{/lang}</span>
	        </td>
	      </tr>
  {if is_foreachable($_file_versions)}
	      {foreach from=$_file_versions item=_file_version name=file_revisions}
	      <tr class="file_version {cycle values='odd,even'}">
	        <td class="version"><a href="{$_file_version->getViewUrl()}" target="_blank">{lang}Version #{/lang}{$_file_version->getVersionNum()}</a></td>
	        <td class="size">{$_file_version->getSize()|filesize}</td>
	        <td class="details">{$_file_version->getCreatedOn()|ago nofilter} {lang}by{/lang} {user_link user=$_file_version->getCreatedBy()}</td>
	        <td class="options">
	        </td>
	      </tr>
	      {/foreach}
	    {/if}
</table>