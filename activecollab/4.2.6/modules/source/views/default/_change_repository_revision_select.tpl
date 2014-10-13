<div id="{$_change_repository_revision_select_id}" class="change_revision">
  <select>
    {foreach from=$_change_repository_revision_select_commits item=commit}
      <option value="{$commit->getRevisionNumber()}" {if $commit->getName() === $revision_number} selected="selected" {/if} >
        {substr($commit->getName(),0,8)} - {str_excerpt(stripslashes($commit->getMessageTitle()),100) nofilter}
      </option>
    {/foreach}
  </select>
  <button type="button"><span>{lang}Change revision{/lang}</span></button>
  <div id="change_revision_status"></div>
</div>
  
<script type="text/javascript">
	var change_revision_form = $('#{$_change_repository_revision_select_id nofilter}');
	var change_revision_button = change_revision_form.find('button');
	var change_revision_select = change_revision_form.find('select');

  {literal}
	var change_revision = function (revision_number, browse_url, check_revision_url) {
	  change_revision_form.block();
	  window.location = App.extendUrl(browse_url, {r : revision_number});
	};
  {/literal}

  change_revision_button.click(function() {
    change_revision(change_revision_select.val(), '{$_change_repository_revision_select_url nofilter}');
  });
  
</script>