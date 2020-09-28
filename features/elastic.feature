@elastic
Feature:
  I want to test some searchs

  Scenario: I want to search books by author or book name
    Given I am on "/searchBooksBis"
    When I fill "Text" with "HarryPotter"
    And I press "submit"
    And I should see "Harry Potter et le Prisonnier d'Azkaban"