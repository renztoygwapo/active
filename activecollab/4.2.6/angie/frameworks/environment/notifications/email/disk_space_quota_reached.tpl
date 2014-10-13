{lang language=$language}Disk Quota Full{/lang}
================================================================================
{notification_wrapper title='Disk Quota Full' recipient=$recipient sender=$sender}
<p>{lang link_style=$style.link language=$language disk_space_used=$disk_space_used disk_space_limit=$disk_space_limit}System is using all of <strong>:disk_space_limit</strong> available space{/lang}.</p>
<p>{lang url=$disk_space_admin_url link_style=$style.link language=$language}Please visit <a href=":url" style=":link_style">Disk Space Control Center</a> to resolve this issue{/lang}.</p>
{/notification_wrapper}