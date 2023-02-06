/**
 * @file
 *
 * Provides a popup to a specific content.
 *
 * - For images: those from the illustration field of "lieu de genève".
 */
(function ($, Drupal) {
  Drupal.behaviors.imagepopup = {
    attach: function (context, settings) {

      // Translations.
      $.extend(true, $.magnificPopup.defaults, {
        tClose: 'Fermer (Esc)',
        tLoading: 'Chargement en cours...',
        gallery: {
          tPrev: 'Précédent',
          tNext: 'Suivant',
          tCounter: '%curr%/%total%'
        },
        image: {
          tError: '<a href="%url%">L\'image</a> n\'a pas pu être chargée.' // Error message when image could not be loaded
        },
        ajax: {
          tError: '<a href="%url%">L\'image</a> n\'a pas pu être chargée.' // Error message when ajax request failed
        }
      });

      $(document).ready(function() {
        $('.magnific-popup--item').each(function() {
          $(this).magnificPopup({
            type: 'image',
            image: {
              titleSrc: function(item) {
                // Add caption on image bottom.
                return item.el.find('img').attr('title');
              }
            },
            delegate: 'a',
            gallery: {
              enabled: true
            },
            zoom: {
              enabled: true,
              duration: 300,
              opener: function(element) {
                return element.find('img');
              }
            }
          });
        });
      });
    }
  };
})(jQuery, Drupal);
