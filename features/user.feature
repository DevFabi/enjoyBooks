@javascript @user
Feature:
  As a user
  I want to naviguate in website

  Scenario: I want to login
    Given I am on "/login"
    When I fill "Email" with "tutuToto@gmail.com"
    And I fill "Password" with "Tutu"
    And I press "submit"
    And I should be on "/home"

  @loginAsUser
  Scenario: I want to display list of author
    Given I am on "/author"
    And I should see "Author list"

  @loginAsUser
  Scenario: I want to display list of books
    Given I am on "/books"
    And I should see "Books"

  @loginAsUser
  Scenario: I want to try to access admin
    Given I am on "/admin/author"
    Then the response status code should be 403
    