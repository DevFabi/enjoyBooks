@javascript @loginAsAdmin @admin
Feature:
  As a admin
  I want to naviguate in website

  Scenario: I want to display list of author
    Given I am on "/author"
    And I should see "Author list"

  Scenario: I want to display list of books
    Given I am on "/books"
    And I should see "Books"

  Scenario: I want to try to access admin
    Given I am on "/admin/author"
    And I should see "Author handler"
