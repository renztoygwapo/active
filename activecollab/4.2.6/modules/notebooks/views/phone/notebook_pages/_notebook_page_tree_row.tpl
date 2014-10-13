{if $_notebook_page}
<li>
	{if $_notebook_page->getVersion() == 1}
		<a href="{$_notebook_page->getViewUrl()}">{$_indent nofilter} {$_notebook_page->getName()}<p class="ui-li-aside ui-li-desc">{lang version=$_notebook_page->getVersion()}v1{/lang}</p></a>
	{else}
		<a href="{$_notebook_page->getViewUrl()}">{$_indent nofilter} {$_notebook_page->getName()}<p class="ui-li-aside ui-li-desc">{lang version=$_notebook_page->getVersion()}v:version{/lang}</p></a>
	{/if}
</li>
{/if}