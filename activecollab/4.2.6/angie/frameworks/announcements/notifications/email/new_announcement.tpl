{lang language=$language subject=$subject}Announcement: :subject{/lang}
================================================================================
{notification_wrapper title='Announcement' recipient=$recipient sender=$sender}
  {$body|nl2br|clickable nofilter}
{/notification_wrapper}