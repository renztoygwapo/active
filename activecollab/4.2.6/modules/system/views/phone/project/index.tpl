{title lang=false}{$active_project->getName()}{/title}
{add_bread_crumb}Overview{/add_bread_crumb}

{object object=$active_project user=$logged_user}
  <div id="project_progress">{project_progress project=$active_project}</div>
{/object}

{if $active_project->getState() == $smarty.const.STATE_VISIBLE}
	<div class="module_navigation">
		<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
			<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		  {foreach $wireframe->tabs as $tab}
		    {if $tab.text != '-'}
		    <li><a href="{$tab.url}"><img class="ui-li-icon" src="{$tab.icon}" alt=""/>{$tab.text}</a></li>
		    {/if}
		  {/foreach}
	  </ul>
	</div>
{/if}