@javascript @loginAsAdmin @admin
Feature:
  As a admin
  I want to naviguate in website

  Scenario: I want to display list of author
    Given I am on "/author"
    And I should see "Liste des auteurs"

  Scenario: I want to display list of books
    Given I am on "/books"
    And I should see "Livres"

  Scenario: I want to try to access admin
    Given I am on "/admin/author"
    And I should see "Author handler"
