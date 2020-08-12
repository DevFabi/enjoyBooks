@javascript @loginAsAdmin @admin_create
Feature:
  As a admin
  I want to create book, author and user

  Scenario: I want to add new book
    Given I am on "/admin/book"
    And I should see "Book handler"
    When I press "Create book"
    And I fill "book_publishedDate" with "10/08/2020"
    And I fill "book_description" with "blablabla"
    And I fill "book_title" with "book title"
    And I fill "book_image" with "tutu.jpg"
    And I fill "book_volumeId" with "1234aBCD"
    And I fill "book_authors" with "7"
    And I press "submit"
    Then I should be on "/admin/book"

  Scenario: I want to add new author
    Given I am on "/admin/author"
    And I should see "Author handler"
    When I press "Create"
    And I fill "author_name" with "new author name"
    And I press "submit"
    Then I should be on "/admin/author"

  Scenario: I want to add new user
    Given I am on "/admin/users"
    And I should see "Users handler"
    When I press "Create"
    And I fill "user_email" with "toto@gmail.com"
    And I fill "user_firstName" with "Toto"
    And I fill "user_lastName" with "Titi"
    And I fill "user_password" with "titutoti"
    And I fill "user_passwordConfirm" with "titutoti"
    And I press "submit"
    Then I should be on "/admin/users"
