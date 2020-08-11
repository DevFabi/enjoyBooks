@javascript @register
Feature:
  As a anonyme
  I want to create an account

  Scenario: I want to register
    Given I am on "/register"
    Then I should be on "/register"
    When I fill "Email" with "tutuToto@gmail.com"
    And I fill "First name" with "Tutu"
    And I fill "Last name" with "Tutu"
    And I fill "Password" with "Tutu"
    And I fill "Password confirm" with "Tutu"
    And I press "submit"
    And I should be on "/login"
