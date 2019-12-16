Feature: Merging pdfs

  @all
  Scenario: I merge pdfs
    Given a pdf
    And a pdf
    When I merge
    Then a pdf is generated

  @all
  Scenario: I merge selected pages
    Given a pdf with pages "1"
    And a pdf with pages "1-2"
    When I merge
    Then a pdf with "3" pages is generated
