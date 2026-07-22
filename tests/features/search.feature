@api @searchDate

Feature: Recherche

  Background:
    Given users:
      | name           | roles          |
      | administrateur | Administrateur |

    Given "lieu_geneve" content:
      | title        | field_nom_complet | status | moderation_state |
      | BEHAT-LIEU-1 | BEHAT-LIEU-1      | 1      | published        |
      | BEHAT-LIEU-2 | BEHAT-LIEU-2      | 1      | published        |
      | BEHAT-LIEU-3 | BEHAT-LIEU-3      | 1      | published        |

  @search_api @acces
  Scenario Outline: Test d'accès à la page de recherche
    Given I am logged in as "<User>"
    When I am on "/search"
    Then the response status code should be <status>

    Examples:
      | User           | status |
      | administrateur | 200    |

  @search_api @noResult
  Scenario Outline: Test d'affichage d'une recherche ne retournant aucun résultats
    Given I am logged in as "<User>"
    And I am on "/search"
    Then the response status code should be <status>
    Then I fill in "keyword" with "BEHAT-LIEU-4"
    And I press "Rechercher"
    Then I should see the text "Aucun résultat ne correspond à votre recherche."

    Examples:
      | User           | status |
      | administrateur | 200    |

  @search_api @allResult
  Scenario Outline: Test d'affichage d'une recherche retournant tous les lieux de Genève
    Given I am logged in as "<User>"
    And I am on "/search"
    Then the response status code should be <status>
    Then I fill in "keyword" with "BEHAT-LIEU-"
    And I press "Rechercher"
    Then I should see "BEHAT-LIEU-1"
    Then I should see "BEHAT-LIEU-2"
    Then I should see "BEHAT-LIEU-3"

    Examples:
      | User           | status |
      | administrateur | 200    |

  @search_api @oneResult
  Scenario Outline: Test d'affichage d'une recherche retournant un résultat spécifique
    Given I am logged in as "<User>"
    And I am on "/search"
    Then the response status code should be <status>
    Then I fill in "keyword" with "BEHAT-LIEU-1"
    And I press "Rechercher"
    Then I should see "BEHAT-LIEU-1"

    Examples:
      | User           | status |
      | administrateur | 200    |

