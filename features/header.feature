Feature: Handling pdf headers
  In order to fix issue 32
  As a user
  I need to be able to handle headers

  Scenario: I merge a pdf with header using the fpdi driver
    Given the "Fpdi2Driver" driver
    And a pdf with a header including text HEADER
    When I merge
    Then a pdf including text "HEADER" is generated

  Scenario: I merge a blank pdf using the fpdi driver
    Given the "Fpdi2Driver" driver
    And a blank pdf
    When I merge
    Then a pdf not including text "1 / 1" is generated

  Scenario: I merge a pdf with header using the tcpdi driver
    Given the "TcpdiDriver" driver
    And a pdf with a header including text HEADER
    When I merge
    Then a pdf including text "HEADER" is generated

  Scenario: I merge a blank pdf using the tcpdi driver
    Given the "TcpdiDriver" driver
    And a blank pdf
    When I merge
    Then a pdf not including text "1 / 1" is generated
