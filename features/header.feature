Feature: Handling pdf headers (see issue 32)

  @all
  Scenario: I merge a pdf with a header
    Given a pdf with a header including text HEADER
    When I merge
    Then a pdf including text "HEADER" is generated

  @all
  Scenario: I merge a blank pdf
    Given a blank pdf
    When I merge
    Then a pdf not including text "1 / 1" is generated
