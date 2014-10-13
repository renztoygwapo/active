/**
 * Render progress bar
 */
jQuery.fn.progress = function(settings) {
  var settings = jQuery.extend({
    'max_value' : null,
    'value' : null,
    'icon' : null,
    'href'  : null,
    'label' : null,
    'class' : ''
  }, settings);
  
  return this.each(function() {
    var progress_container = $(this);

    /**
     * Return progress bar class
     */
    var get_class = function() {
      var _class;
      if(settings['max_value']) {
        _class += ' with_progressbar';
      } else {
        _class += ' without_progressbar';
      } //if

      if(settings['icon']) {
        _class += ' with_icon ';
      } //if
      return _class;
    } //get_class

    /**
     * Return style
     */
    var get_style = function() {
      if(settings['icon']) {
        return 'background-image: url(' + settings['icon'] + ');';
      } //if
      return '';
    } //get_style

    /**
     * Render widget
     */
    var render = function() {
      var progress;
      if(settings['href']) {
        progress = $('<a href="' + settings['href'] + '" class="advanced_progressbar ' + get_class() + '" style="' + get_style() + '"></a>');
      } else {
        progress = $('<span class="advanced_progressbar ' + get_class() + '" style="' + get_style() + '"></span>');
      } //if

      var label = $('<span class="advanced_progressbar_label">' + settings['label'] + '</span>').appendTo(progress);

      if(settings['max_value']) {
        var percentage = settings['value']/settings['max_value'] * 100;
        percentage = percentage > 100 ? 100 : percentage;
        var progress_bar = $('<span class="advanced_progressbar_progressbar"><span class="advanced_progressbar_progressbar_inner" style="width: ' + percentage + '%"></span></span>').appendTo(progress);
      } //if

      progress_container.append(progress);
    } //render

    render();

  });
};