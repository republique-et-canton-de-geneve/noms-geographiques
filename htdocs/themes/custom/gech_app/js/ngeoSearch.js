/**
 * @file
 * Provides JS for the search feature.
 *
 */

(function ($) {

  const urlParams = new URLSearchParams(window.location.search);
  const param_sort_by = urlParams.get('sort_by');
  const param_sort_order = urlParams.get('sort_order');
  const param_voie_disparue= urlParams.get('voie_disparue');

  const $sortFilters = $('#edit-sort-filters');
  const $voieDisparueSwitch = $('#voie-disparue-switch');
  const $searchForm = $('#views-exposed-form-search-page-recherche');

  const $homeSearchCols = $('.home-search-cols > div');

  $(document).ready(function () {
    /**
     * Override dropdown settings.
     *
     * Make the facet dropdown not closing by clicking not exactly on its label.
     * Close only if clicked outside.
     */
    const dropdownElementList = document.querySelectorAll('.js-facets-checkbox-links .dropdown-toggle')
    const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl, {
      'autoClose': 'outside',
    }))


    /**
     * Make the 2 search cols on home the same height.
     */
    if ($homeSearchCols.length && $(window).width() >= 990) {
      var highestCardHeight = 0;
      // Select and loop the concerned cols.
      $homeSearchCols.each(function () {
        // Get the biggest height:
        if ($(this).height() > highestCardHeight) {
          highestCardHeight = $(this).height();
        }
      });
      // Set the biggest height to each element:
      $homeSearchCols.children('.card').height(highestCardHeight);
    }


    /**
     * Voies disparues in search results.
     *
     * Set a CSS class to the parent in order to style children elements.
     */
    if ($('.voie-disparue').length) {
      $('.voie-disparue').each(function() {
        if ($(this).text().length > 0) {
          $(this).parents('.views-row').addClass('voie-disparue--wrapper');
        }
      })
    }


    /**
     * Get the sort parameters from the url.
     *
     * The exposed sort select from View is hidden, and we use it
     * to send sort parameter via our custom select.
     * So we need to update it according to the URL sort parameters.
     */
    // Check parameters and set the value of the selected sort.
    if ($sortFilters.length) {

      // If no sort-by present in URL.
      if (param_sort_by === null) {
        $sortFilters.val("pertinence_DESC")
      }

      // Standard cases:
      if (param_sort_by === "title") {

        // If no sort-order present in URL.
        if (param_sort_order === null) {
          $sortFilters.val("title_ASC")
        }

        if (param_sort_order === "ASC") {
          $sortFilters.val("title_ASC")
        }
        else if (param_sort_order === "DESC") {
          $sortFilters.val("title_DESC")
        }
      }
      else if (param_sort_by === "search_api_relevance") {
        // Pertinence.
        $sortFilters.val("pertinence_DESC")
      }
    }


    /**
     * Get the voie-disparue parameter from the url.
     *
     * The exposed voie-disparue select from View is hidden, and we use it
     * to send voie-disparue parameter via our custom switch.
     * So we need to update it according to the URL sort parameters.
     *
     * We don't offer the choice of "only voie disparue".
     */
    if ($voieDisparueSwitch.length) {
      if (param_voie_disparue === "All") {
        $voieDisparueSwitch.prop("checked", true);
      }

      if (param_voie_disparue === "0") {
        $voieDisparueSwitch.prop("checked", false);
      }
    }


  })

  /**
   * Override exposed view voie-disparue element.
   *
   * We don't show the default exposed voie-disparue select. We hide it and we show
   * instead our custom switch. The default one is useful to send parameters to the view form.
   */
  $voieDisparueSwitch.change(function () {
    if ($(this).prop('checked')) {
      $('#edit-voie-disparue--2').val('All');
    }
    else {
      $('#edit-voie-disparue--2').val('0');
    }
    $searchForm.submit();
  });


  /**
   * Override exposed view sort element.
   *
   * We don't show the default exposed sort filter. We hide it and we show
   * instead our custom one. The default one is useful to send parameters to the view.
   */
  $sortFilters.change(function () {
    var selectedSort = $(this).children("option:selected").val();
    var sortOrder = $("#edit-sort-order--2");
    var sortBy = $("#edit-sort-by--2");

    // When the exposed sort value changes, we set the corresponding value to
    // the hidden sort to make it work.
    switch (selectedSort) {
      case "title_ASC":
        sortBy.val("title")
        sortOrder.val("ASC");
        break;

      case "title_DESC":
        sortBy.val("title")
        sortOrder.val("DESC");
        break;

      case "pertinence_DESC":
        sortBy.val("search_api_relevance")
        sortOrder.val("DESC");
        break;
    }

    $searchForm.submit();
  })

}(jQuery));
