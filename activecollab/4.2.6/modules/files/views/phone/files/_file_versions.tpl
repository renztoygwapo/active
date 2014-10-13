<div class="file_versions" id="{$_file_versions_id}">
	<div class="content_section_container">
	  <!-- File Versions List -->
	  <table class="file_versions common" cellspacing="0">
	    <thead>
	      <tr>
	        <th>{lang}Version{/lang}</th>
	        <th>{lang}Size{/lang}</th>
	        <th>{lang}Details{/lang}</th>
	        <th></th>
	      </tr>
	    </thead>
	    <tbody>
	      <tr class="file_version latest {cycle values='odd,even'}">
	        <td class="version"><a href="{$_file_versions_file->getDownloadUrl()}" target="_blank">{lang}Latest Version{/lang}</a></td>
	        <td class="size">{$_file_versions_file->getSize()|filesize}</td>
	        <td class="details">
	        {if $_file_versions_file->getVersionNum() == 1}
	          {$_file_versions_file->getCreatedOn()|ago nofilter} {lang}by{/lang} {user_link user=$_file_versions_file->getCreatedBy()}
	        {else}
	          {$_file_versions_file->getLastVersionOn()|ago nofilter} {lang}by{/lang} {user_link user=$_file_versions_file->getUpdatedBy()}
	        {/if}
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
	          {if $_file_version->canDelete($_file_versions_user)}<a href="{$_file_version->getDeleteUrl()}" title="{lang}Delete Permanently{/lang}" class="remove_version"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" /></a>{/if}
	        </td>
	      </tr>
	      {/foreach}
	    {/if}
	    </tbody>
	  </table>
	</div>
</div>