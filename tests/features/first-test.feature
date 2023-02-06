@api
Feature: Test account menu links
  In order to login,
  As an contributor,
  I can not see the toolbar menu containing the item Gérer,
  As an admin-site,
  I can see the toolbar menu containing the item Gérer.


#  Scenario: Make sure that logged in users see the account menu
#    Given I am logged in as a user with the "authenticated" role
#    And I am on "/"
#    Then I should see the link "My account" in the "body" region
#    And I should see the link "Log in"

#  Scenario: Make sure that anonymous users see the account menu
#    Given I am not logged in
#    And I am on "/"
#    Then I should see the link "Log in" in the "header" region

  Scenario: Make sure that logged in contributeur can't see the toolbar menu
    When I visit 'user/login'
    And I fill in "Contri Buteur (DI)" for "name"
    And I fill in "contrib" for "pass"
    And I press the "Se connecter" button
    Then I should not see the link "Gérer"

    Scenario: Make sure that logged in admin-site can see the toolbar menu
    When I visit 'user/login'
    And I fill in "Admin Site (DI)" for "name"
    And I fill in "site" for "pass"
    And I press the "Se connecter" button
    Then I should not see the link "Gérer"
