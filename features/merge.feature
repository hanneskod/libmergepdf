Feature: Mergeing pdfs
  In order to use lib
  As a user
  I need to be able to merge pdfs

  Scenario: I merge two pdfs using the fpdi driver
    Given the "Fpdi2Driver" driver
    And a pdf
    And a pdf
    When I merge
    Then a pdf is generated

  Scenario: I merge selected pages using the fpdi driver
    Given the "Fpdi2Driver" driver
    And a pdf with pages "1"
    And a pdf with pages "1-2"
    When I merge
    Then a pdf with "3" pages is generated

  Scenario: I merge two pdfs using the tcpdi driver
    Given the "TcpdiDriver" driver
    And a pdf
    And a pdf
    When I merge
    Then a pdf is generated

  Scenario: I merge selected pages using the tcpdi driver
    Given the "TcpdiDriver" driver
    And a pdf with pages "1"
    And a pdf with pages "1-2"
    When I merge
    Then a pdf with "3" pages is generated

  Scenario: I merge pdfs of versions later than 1.4
    Given the "TcpdiDriver" driver
    And a pdf of version "1.4" with pages "1"
    And a pdf of version "1.5" with pages "1"
    And a pdf of version "1.6" with pages "1"
    And a pdf of version "1.7" with pages "1"
    When I merge
    Then a pdf with "4" pages is generated
