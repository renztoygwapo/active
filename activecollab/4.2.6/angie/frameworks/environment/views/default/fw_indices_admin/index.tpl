{title}Indexes Administration{/title}
{add_bread_crumb}Indexes{/add_bread_crumb}

<div id="indices_admin" class="wireframe_content_wrapper settings_panel">
  <div class="settings_panel_header">
    <table class="settings_panel_header_cell_wrapper">
      <tr>
        <td class="settings_panel_header_cell">
          <h2>{lang}Indexes{/lang}</h2>
		      <div class="properties">
		        <div class="property">
		          <div class="label">{lang}About{/lang}</div>
		          <div class="data">{lang}Use this tool to see the size and rebuild all data indexes{/lang}</div>
		        </div>
		      </div>
          
          <ul class="settings_panel_header_cell_actions">
            <li>{link href=Router::assemble('indices_admin_rebuild') mode=flyout title="Rebuilding Indexes" class=link_button_alternative}Rebuild All{/link}</li>
          </ul>
        </td>
      </tr>
    </table>
  </div>
  
  <div class="settings_panel_body">
  	<table class="common" cellspacing="0">
    	<thead>
      	<tr>
      		<th></th>
      		<th>{lang}Index{/lang}</th>
      		<th class="size center">{lang}Size{/lang}</th>
      		<th></th>
      	</tr>
    	</thead>
    	<tbody>
      {foreach $all_indices as $index}
      	<tr>
      		<td class="icon"><img src="{$index.icon}"></td>
      		<td class="name">
      			<span class="index_name">{$index.name}</span>
    			{if $index.description}
    				<span class="index_description">{$index.description}</span>
    			{/if}
      		</td>
      		<td class="size center">{$index.size|filesize}</td>
      		<td class="options right">
      		{if $index.rebuild_url}
      			{button href=$index.rebuild_url mode=flyout title="Rebuilding Index"}Rebuild{/button}
      		{/if}
      		</td>
      	</tr>
      {/foreach}
    	</tbody>
    </table>
  </div>
</div>