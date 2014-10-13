{title lang=false}{$active_homescreen_tab->getName()}{/title}
{add_bread_crumb lang=false}{$active_homescreen_tab->getName()}{/add_bread_crumb}
{use_widget name='homescreen' module=$smarty.const.HOMESCREENS_FRAMEWORK}

<div id="homescreen_tab">{$active_homescreen_tab->render($logged_user) nofilter}</div>