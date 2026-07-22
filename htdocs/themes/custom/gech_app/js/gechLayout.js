/**
 * @file
 * Provides JS for the global layout.
 *
 */

(function ($) {
    $(document).ready(function () {
        /**
         * Menu:
         * Open/close the main navigation and display the overlay.
         */
        $('#header .navbar-toggler').click(function () {
            // Opening the menu:
            if ($('#navbarHeader').hasClass('collapse')) {
                openMenu($(this));
            } else {
                // Closing the menu:
                closeMenu();
            }
        });

        // Close the menu by cliking on the overlay:
        $('.overlay').click(function () {
            closeMenu();
        });
    });

    function openMenu($clickedBtn) {
        $clickedBtn.addClass('collapsed');
        $('#navbarHeader').removeClass('collapse');
        $('.overlay').show();

        toggleLabels($('#header .navbar-toggler'));

        // GECH-1074: focus on search input:
        // if ($clickedBtn.hasClass('item-search')) {
        //   $('form.gech-mindbreeze-global-search input[type="text"]').focus();
        // }

        // GECH-1074: focus on first element of nav:
        // if ($clickedBtn.hasClass('item-menu')) {
        //     $(".list-group-item:first > a").focus();
        // }
    }

    function closeMenu() {
        $('.navbar-toggler').removeClass('collapsed');
        $('#navbarHeader').addClass('collapse');
        $('.overlay').hide();

        toggleLabels($('.navbar-toggler'));
    }

    function toggleLabels($elements) {
        $elements.each(function () {
            var textNewLabel = $(this).data('label');
            var textCurrentLabel = $(this).attr('aria-label');
            // Inverse labels:
            $(this).attr('aria-label', textNewLabel);
            $(this).data('label', textCurrentLabel);
        });
    }
}(jQuery));
