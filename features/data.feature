Feature: Load of repository data
  In order to have the repository data into the system
  As a system administrator
  We need to read the data from the repository files and load it into the system

  Background:
    Given the repository files and content is:
      | path                                                | content                                                                                                                         |
      | 1/election.json                                     | {"edition": "1", "date": "1977-06-15"}                                                                                          |
      | 1/scope/administracion-publica/scope.json           | {"name": "Administración Pública"}                                                                                              |
      | 1/scope/sanidad/scope.json                          | {"name": "Sanidad"}                                                                                                             |
      | 1/party/partido-ficticio/party.json                 | {"name": "Partido Ficticio", "acronym": "PF", "programmeUrl": "http://partido-ficticio.es"}                                     |
      | 1/party/otro-partido/party.json                     | {"name": "Otro Partido", "acronym": "OP", "programmeUrl": "http://otro-partido.es"}                                             |
      | 1/scope/sanidad/policy/partido-ficticio/content.md  | ## sanidad universal y gratuita                                                                                                 |
      | 1/scope/sanidad/policy/partido-ficticio/policy.json | {"party": "partido-ficticio", "scope": "sanidad", "sources": ["http://partido-ficticio.es/programa/sanidad (páginas 20 a 25)"]} |
      | 1/scope/sanidad/policy/otro-partido/content.md      | ## sanidad para todos                                                                                                           |
      | 1/scope/sanidad/policy/otro-partido/policy.json     | {"party": "otro-partido", "scope": "sanidad", "sources": ["http://otro-partido.es/programa/sanidad (páginas 12 a 15)"]}         |
    And the content of the files are loaded into the system

  @backend
  Scenario: Scopes have been properly loaded
    When I see the list of available "scopes" for the "1" election edition
    Then the list of "scopes" contains:
      | scope                  | name                   |
      | administracion-publica | Administración Pública |
      | sanidad                | Sanidad                |

  @backend
  Scenario: Parties have been properly loaded
    When I see the list of available "parties" for the "1" election edition
    Then the list of "parties" contains:
      | party            | name             | acronym | programmeUrl               |
      | partido-ficticio | Partido Ficticio | PF      | http://partido-ficticio.es |
      | otro-partido     | Otro Partido     | OP      | http://otro-partido.es     |

  @backend
  Scenario: Policies linked to a scope have been properly loaded
    When I see these policies linked to the scope "sanidad" for the "1" election edition
      | party            | content                         |
      | partido-ficticio | ## sanidad universal y gratuita |
      | otro-partido     | ## sanidad para todos           |
    Then the list of "policies" contains:
      | scope   | party            | sources                                                           | content                               |
      | sanidad | partido-ficticio | ["http://partido-ficticio.es/programa/sanidad (páginas 20 a 25)"] | <h2>sanidad universal y gratuita</h2> |
      | sanidad | otro-partido     | ["http://otro-partido.es/programa/sanidad (páginas 12 a 15)"]     | <h2>sanidad para todos</h2>           |
