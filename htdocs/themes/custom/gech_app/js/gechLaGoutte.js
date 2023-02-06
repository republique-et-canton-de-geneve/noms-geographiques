/**
 * @file
 * Provides JS for the e-démarches (la goutte).
 *
 * No use of Drupal behaviors because of the complexity of the functions'call.
 *
 */


(function ($) {
    $(document).ready(function () {

        // Does not use drupalSettings in order to work in templates:
        var allJsonData = JSON.parse($('[data-drupal-selector="drupal-settings-json"]').html());
        // var whoAmISettings = drupalSettings.gech_utils.e_demarches;
        // Replaced by:
        var whoAmISettings = allJsonData.gech_utils.e_demarches;

        var whoAmIButtonSelector = $('.whoami');
        var loggedButtonsSelector = $('.whoami-logged');
        var signInButtonsSelector = $('.whoami-signin');
        var emailSelector = $('.whoami-email');
        var nameSelector = $('.whoami-name');

        // Initialize popoup.
        const bsPopup = new bootstrap.Collapse($('#edemarches-popup'), {
            toggle: false,
        });

        if (whoAmISettings.sso === 1) {
            whoAmIButtonSelector.click(function () {
                // Closing the main navigation:
                if (!$('#navbarHeader').hasClass('collapse')) {
                    $('.navbar-toggler').removeClass('collapsed');
                    $('#navbarHeader').addClass('collapse');
                    $('.overlay').hide();
                    toggleLabels($('.navbar-toggler'));
                }

                // Show overlay:
                $('.overlay').toggle();

                // Toggle aria-label:
                toggleLabels(whoAmIButtonSelector);

                // Toggle popup.
                bsPopup.toggle();
                // Toggle the button for the arrow.
                whoAmIButtonSelector.toggleClass('collapsed');
            });


            if (typeof getCookie('whoami') === 'undefined') {
                var date = new Date();
                var cache_cookie = parseInt(whoAmISettings.cookie);
                date.setTime(date.getTime() + (cache_cookie * 1000));
                var expires = "; expires=" + date.toGMTString();
                $.ajax({
                    type: 'GET',
                    url: whoAmISettings.endpoint,
                    xhrFields: {
                        withCredentials: true
                    },
                    setCookies: document.cookie,
                    crossDomain: true,
                    dataType: 'json',
                    success: function (data) {
                        if (whoAmISettings.debug === 1) {
                            console.log(data, 'response');
                        }
                        if (data['code'] === 200) {
                            document.cookie = "whoami=" + data['properties']['isLogged'] + expires + ";SameSite=Strict; path=/;Secure";
                            sessionStorage.setItem('mail', data['properties']['mail']);
                            sessionStorage.setItem('fullname', data['properties']['fullname']);
                        }
                        whoAmIStatus();
                    },
                    timeout: 2000,
                }).fail(function (data) {
                    if (whoAmISettings.debug === 1) {
                        console.error(data, 'E-demarches: Request failed!');
                    }
                    document.cookie = "whoami=0;" + expires + "; SameSite=Strict; path=/;Secure";
                    whoAmIStatus();
                });
            } else {
                whoAmIStatus();
            }

            $('body').on('click', function (e) {
                // Hide popover if user clicks everywhere except on
                // popover - but its hidden skip-whoami button - & its buttons.
                if (($('#edemarches-popup').hasClass('show'))
                    && ((!($(e.target).hasClass('whoami')) && ($(e.target).parents('.popover').length === 0))
                        || $(e.target).hasClass('skip-whoami'))) {

                    // Change labels:
                    toggleLabels(whoAmIButtonSelector);

                    // Toggle popup.
                    bsPopup.toggle()
                    // Toggle the button for the arrow.
                    whoAmIButtonSelector.toggleClass('collapsed');

                    // Hide overlay except when the user click on menu
                    if (!$(e.target).hasClass('navbar-toggler')) {
                        $('.overlay').hide();
                    }
                }
            });
        }

        /**
         * Get the cookie according to its name.
         *
         * @param name
         *  the cookie's name.
         *
         * @returns {string}
         *  The cookie's content.
         */
        function getCookie(name) {
            var value = "; " + document.cookie;
            var parts = value.split("; " + name + "=");
            if (parts.length === 2) {
                return parts.pop().split(";").shift();
            }
        }

        /**
         * Toggle buttons labels.
         *
         * @param $elements
         *  the buttons to update.
         */
        function toggleLabels($elements) {
            $elements.each(function () {
                var textNewLabel = $(this).data('label');
                var textCurrentLabel = $(this).attr('aria-label');
                // Inverse labels:
                $(this).attr('aria-label', textNewLabel);
                $(this).data('label', textCurrentLabel);
            });
        }

        /**
         * Display buttons according to user status (logged or not)
         */
        function whoAmIStatus() {
            if (getCookie('whoami') === 1 || getCookie('whoami') === 'true') {

                // User logged.
                whoAmIButtonSelector.addClass('active');
                loggedButtonsSelector.show();
                signInButtonsSelector.hide();

                emailSelector.html(sessionStorage.getItem('mail'));
                nameSelector.html(sessionStorage.getItem('fullname'));

                emailSelector.show();
                nameSelector.show();
            } else {
                // User logout.
                loggedButtonsSelector.hide();
                signInButtonsSelector.show();
                emailSelector.hide();
                nameSelector.hide();
                whoAmIButtonSelector.removeClass('active');
                sessionStorage.removeItem('mail');
                sessionStorage.removeItem('fullname');
            }
        }
    });
}(jQuery));
