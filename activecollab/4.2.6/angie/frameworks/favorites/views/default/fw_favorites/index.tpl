{if is_foreachable($favorites)}
  <div class="user_favorites">
	  <table class="common" cellspacing="0">
	  {foreach from=$favorites item=favorite}
	    <tr>
        <td class="favorite_toggler">{favorite_object object=$favorite user=$logged_user}</td>
	      <td class="favorite_link">{object_link object=$favorite excerpt=50}</td>
	      <td class="favorite_type"><a href="{$favorite->getViewUrl()}"><span class="object_type object_type_{$favorite->getVerboseType(true)}">{$favorite->getVerboseType()}</span></a></td>
	    </tr>
	  {/foreach}
	  </table>
  </div>
{else}
  <p class="empty_page">{lang}There are no favorites for this user{/lang}</p>
{/if}
<script type="text/javascript">
	var favorites_wrapper = $('div#user_favorites');
	$(favorites_wrapper + '.favorite_link a,' + favorites_wrapper + '.favorite_type a').click(function(){
		favorites_wrapper.remove();
	});
</script>