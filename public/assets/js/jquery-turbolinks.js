/*!
jQuery.Turbolinks ~ https://github.com/kossnocorp/jquery.turbolinks
jQuery plugin for drop-in fix binded events problem caused by Turbolinks
The MIT License
Copyright (c) 2012-2013 Sasha Koss & Rico Sta. Cruz
*/
(function()
{
  var $, $document;

  $ = window.jQuery || (typeof require === "function" ? require('jquery') : void 0);

  $document = $(document);

  $.turbo = {
    version: '2.0.1',
    isReady: false,
    use: function(load, fetch, change, restore)
    {
      return $document.off('.turbo')
      		 .on("" + load + ".turbo", this.onLoad)
      		 .on("" + fetch + ".turbo", this.onFetch)
      		 .on("" + change + ".turbo", this.onChange)
           .on("" + restore + ".turbo", this.onRestore);
    },
    addCallback: function(callback) {
      if ($.turbo.isReady)
      {
        return callback($);
      }
      else
      {
        return $document.on('turbo:ready', function()
        {
          return callback($);
        });
      }
    },
    onLoad: function()
    {
      $.turbo.isReady = true;
      return $document.trigger('turbo:ready');
    },
    onFetch: function()
    {
      NProgress.start();
      jQuery('body script').remove();
      return $.turbo.isReady = false;
    },
    onChange: function()
    {
      NProgress.done();
      return $.turbo.isReady = true;
    },
    onRestore: function()
    {
      NProgress.remove();
      return $.turbo.isReady = false;
    },
    register: function()
    {
      $(this.onLoad);
      return $.fn.ready = this.addCallback;
    }
  };

  $.turbo.register();

  $.turbo.use('page:load', 'page:fetch', 'page:change', 'page:restore');

  /* Enable Turbolinks form */
  $document.on("submit", "form[data-turboform]", function(e)
  {
    Turbolinks.visit(this.action+(this.action.indexOf('?') == -1 ? '?' : '&')+$(this).serialize());
    return false;
  });

}).call(this);