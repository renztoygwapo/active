var Livechat = function() {

  var container = $('<div id="livechat_button_wrapper"><div class="livechat_button"><a class="chat" href="/contact.html"></a></div><div id="livechat_eye_catcher"><a class="chat" href="/contact.html"></a><a class="close" href="#">x</a></div></div>');

  var livechat_wrapper, button_text, eye_catcher, close_eye_catcher;

  var print_online_button = function() {
    button_text = 'Chat Now';
    close_eye_catcher = eye_catcher.find('a.close');

    eye_catcher.show();
    eye_catcher.find('div.livechat_content a.chat').text('Chat Now');

    eye_catcher.hover(function() {
      $(this).find('a.close').show();
    }, function() {
      $(this).find('a.close').hide();
    });

    close_eye_catcher.hover(function() {
      $(this).show();
    });

    close_eye_catcher.click(function() {
      eye_catcher.remove();
    });
  };

  var print_offine_button = function() {
    $('body').append(container);
    button_text = 'Leave a Message';
  };

  return {
    'init' : function () {
      $('body').append(container);
      livechat_wrapper = $('#livechat_button_wrapper');
      eye_catcher = livechat_wrapper.find('#livechat_eye_catcher');

      if (typeof LC_Status !== 'undefined' && LC_Status !== 'offline') {
        print_online_button();
      } else {
        print_offine_button()
      } // if

      livechat_wrapper.find('div.livechat_button a.chat').text(button_text);

      livechat_wrapper.find('a.chat').click(function() {
        window.open('https://secure.livechatinc.com/licence/1038879/open_chat.cgi?lang=en&groups=0','Chat_1038879','width=700,height=520,resizable=yes,scrollbars=no');
        eye_catcher.hide();
        return false;
      });
    } // init
  }
}();