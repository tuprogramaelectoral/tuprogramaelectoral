Feature: Load of repository data
  In order to have the repository data into the system
  As a system administrator
  We need to read the data from the repository files and load it into the system

  Background:
    Given the repository files and content is:
      | path                                              | content                                                                                                                         |
      | field/administracion-publica/field.json           | {"name": "Administración Pública"}                                                                                              |
      | field/sanidad/field.json                          | {"name": "Sanidad"}                                                                                                             |
      | party/partido-ficticio/party.json                 | {"name": "Partido Ficticio", "acronym": "PF", "programmeUrl": "http://partido-ficticio.es"}                                     |
      | party/otro-partido/party.json                     | {"name": "Otro Partido", "acronym": "OP", "programmeUrl": "http://otro-partido.es"}                                             |
      | field/sanidad/policy/partido-ficticio/content.md  | ## sanidad universal y gratuita                                                                                                 |
      | field/sanidad/policy/partido-ficticio/policy.json | {"party": "partido-ficticio", "field": "sanidad", "sources": ["http://partido-ficticio.es/programa/sanidad (páginas 20 a 25)"]} |
      | field/sanidad/policy/otro-partido/content.md      | ## sanidad para todos                                                                                                           |
      | field/sanidad/policy/otro-partido/policy.json     | {"party": "otro-partido", "field": "sanidad", "sources": ["http://otro-partido.es/programa/sanidad (páginas 12 a 15)"]}         |
    And the content of the files are loaded into the system

  @backend
  Scenario: Fields have been properly loaded
    When I see the list of available "fields"
    Then the list of "fields" contains:
      | id                     | name                   |
      | administracion-publica | Administración Pública |
      | sanidad                | Sanidad                |

  @backend
  Scenario: Parties have been properly loaded
    When I see the list of available "parties"
    Then the list of "parties" contains:
      | id               | name             | acronym | programmeUrl               |
      | partido-ficticio | Partido Ficticio | PF      | http://partido-ficticio.es |
      | otro-partido     | Otro Partido     | OP      | http://otro-partido.es     |

  @backend
  Scenario: Policies linked to a field have been properly loaded
    When I see these policies linked to the field "sanidad"
      | id                       | content                         |
      | partido-ficticio_sanidad | ## sanidad universal y gratuita |
      | otro-partido_sanidad     | ## sanidad para todos           |
    Then the list of "policies" contains:
      | id                       | partyId          | sources                                                           | content                               |
      | partido-ficticio_sanidad | partido-ficticio | ["http://partido-ficticio.es/programa/sanidad (páginas 20 a 25)"] | <h2>sanidad universal y gratuita</h2> |
      | otro-partido_sanidad     | otro-partido     | ["http://otro-partido.es/programa/sanidad (páginas 12 a 15)"]     | <h2>sanidad para todos</h2>           |
