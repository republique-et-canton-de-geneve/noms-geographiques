@api @tnr @lieu

Feature: Lieu - CRUD

  Background:
    Given users:
      | name                      | roles               |
      | contributeur-BEHAT        | contributeur        |
      | admin_site-BEHAT          | admin_site          |

    Given "lieu_geneve" content:
      | title        | status |
      | LIEU-1-BEHAT | 1      |
      | LIEU-2-BEHAT | 0      |

  @create
  Scenario Outline: Test d'accès à la création d'un contenu "Lieu"
    Given I am logged in as "<User>"
    When I am on "node/add/lieu_geneve"
    Then the response status code should be <status>

    Examples:
      | User                  | status |
      | contributeur-BEHAT    | 200    |
      | admin_site-BEHAT      | 200    |

  @read
  Scenario Outline: Test d'accès à la lecture d'un contenu "Lieu"
    Given I am logged in as "<User>"
    When I am on a "lieu_geneve" node with the title "<contenu>"
    Then the response status code should be <status>

    Examples:
      | User                  | contenu       | status |
      | contributeur-BEHAT    | LIEU-1-BEHAT  | 200    |
      | contributeur-BEHAT    | LIEU-2-BEHAT  | 200    |
      | admin_site-BEHAT      | LIEU-1-BEHAT  | 200    |
      | admin_site-BEHAT      | LIEU-2-BEHAT  | 200    |

  @update
  Scenario Outline: Test d'accès à la modification d'un contenu "Lieu"
    Given I am logged in as "<User>"
    When I am editing a "lieu_geneve" node with the title "<contenu>"
    Then the response status code should be <status>

    Examples:
      | User                  | contenu       | status |
      | contributeur-BEHAT    | LIEU-1-BEHAT  | 200    |
      | contributeur-BEHAT    | LIEU-2-BEHAT  | 200    |
      | admin_site-BEHAT      | LIEU-1-BEHAT  | 200    |
      | admin_site-BEHAT      | LIEU-2-BEHAT  | 200    |

  @delete
  Scenario Outline: Test d'accès à la suppression d'un contenu "Lieu"
    Given I am logged in as "<User>"
    When I am on a "lieu_geneve" node with the title "<contenu>"
    And I <action> "Supprimer" in the ".nav.nav-tabs .nav-item:nth-child(3) a" element

    Examples:
      | User                  | contenu       | action          |
      | contributeur-BEHAT    | LIEU-1-BEHAT  | should not see  |
      | contributeur-BEHAT    | LIEU-2-BEHAT  | should not see  |
      | admin_site-BEHAT      | LIEU-1-BEHAT  | should see      |
      | admin_site-BEHAT      | LIEU-2-BEHAT  | should see      |
