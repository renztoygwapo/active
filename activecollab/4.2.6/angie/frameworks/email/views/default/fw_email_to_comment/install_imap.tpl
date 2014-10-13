<div id="install_imap">
  <p>{lang}Required Extension Status{/lang}:
  {if $imap_installed}
    <span class="ok">{lang}Installed{/lang}</span>
  {else}
    <span class="nok">{lang}Not Yet Installed{/lang}</span>
  {/if}
  </p>

  <div class="empty_slate">
    <h3>{lang}Installation Instructions{/lang}</h3>
    <p>{lang}IMAP PHP extension is required for activeCollab to be able to connect to POP3 and IMAP mailboxes and import messages{/lang}.</p>
    <p>{lang instructions_url="http://www.php.net/manual/en/imap.setup.php"}Different operating systems have differnet installation instructions, so please consult <a href=":instructions_url">main setup article</a> in PHP documentation, as well as resources available for the operating system that you are using. Example queries{/lang}:</p>
    <ul>
      <li><a href="https://www.google.com/search?q=windows+php+imap+install" target="_blank">Windows</a></li>
      <li><a href="https://www.google.com/search?q=mountain+lion+php+imap+install" target="_blank">Mac OS X, Mountain Lion</a></li>
      <li><a href="https://www.google.com/search?q=debian+php+imap+install" target="_blank">Debian Linux</a></li>
      <li><a href="https://www.google.com/search?q=centos+php+imap+install" target="_blank">CentOS Linux</a></li>
    </ul>
    <p>{lang}Please consult your system administrator for assistance with IMAP extension installation{/lang}.</p>
  </div>
</div>