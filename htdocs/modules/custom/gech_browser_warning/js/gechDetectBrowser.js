/**
 * @file
 * Detect browser.
 *
 * Inspire by:
 * https://www.drupal.org/project/browser_detect
 *
 * Others solutions, see:
 * https://www.npmjs.com/package/detect-browser
 * https://www.npmtrends.com/browser-detect-vs-detect-browser-vs-browser-detection
 */
(function (Drupal, $) {
  'use strict';

  Drupal.behaviors.browser_detect = {
    displayDisclaimer: function () {
      var message = '<!-- avertissement -->' +
          '<div class="alert alert-warning messages messages--warning" role="alert">' +
          '<span class="fa-stack">' +
          '<i class="fa fa-exclamation-circle fa-2x"></i>' +
          "</span>Pour une expérience optimale sur ce site, nous vous recommandons l'usage d'un navigateur récent." +
          '<span class="close"><a href="#" title="Close"><img src="/core/misc/icons/e32700/error.svg" style="float: right;" /></a>' +
          '</div>' +
          '<!-- /avertissement -->';

      $('main .container').first().prepend(message);
      $('.adminimal .layout-container .page-content').prepend(message);

      // Handle close event
      $('.alert .close a').click(function () {
        document.cookie = "browserWarning=Done;path=/";
        $('.alert.alert-warning').hide();
      });
    },
    check: function () {

      var userBrowser = (navigator.userAgent).toLowerCase(),
          browsers = [],
          matches,
          cookie = document.cookie;
      // console.log(cookie, 'cookie');
      // console.log(userBrowser, 'userBrowser');

      // Check if Warning already displays
      if (cookie.indexOf('browserWarning=Done') <= -1) {

        // Browsers check
        browsers.chrome = (userBrowser.indexOf('chrome') > -1 || userBrowser.indexOf('crios') > -1) ? 1 : -1;
        browsers.firefox = userBrowser.indexOf('firefox');
        browsers.msedge = userBrowser.indexOf('edg');
        browsers.safari = userBrowser.indexOf('safari');
        var isIE = /*@cc_on!@*/false || !!document.documentMode;

        // Check if the message should be displayed
        if (isIE) {
          console.log('Browser KO: Internet Explorer');
          Drupal.behaviors.browser_detect.displayDisclaimer();
        }
        else if (browsers.firefox > -1) {
          // Check firefox version
          matches = userBrowser.match(/firefox\/([0-9]*)/);
          if (matches[1] <= 61) {
            console.log('Browser KO: Firefox version <= 61');
            Drupal.behaviors.browser_detect.displayDisclaimer();
          }
          else {
            console.log('Browser OK: Firefox > 61');
          }
        }
        else if ((browsers.chrome <= -1) && (browsers.msedge <= -1)
            && (browsers.safari <= -1)) {

          // Not Chrome or Edge or Safari... but possibly mobile browser.
          console.log('Browser KO: Not Chrome or Edge or Safari');
          //Drupal.behaviors.browser_detect.displayDisclaimer();
        }
        else {
          // Chrome or Edge or Safari
          console.log('Browser OK: Chrome or Edge or Safari');
        }
      }
      else {
        console.log('Browser warning already closed');
      }
    },

    attach: function (context) {
      Drupal.behaviors.browser_detect.check();
    }
  };

})(Drupal, jQuery);
