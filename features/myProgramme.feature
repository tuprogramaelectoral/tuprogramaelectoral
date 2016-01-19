Feature: Build my programme by selecting my preferred policies
  In order to build my programme
  as a voter
  I need to select my interests and select my preferred linked policy to each of them

  Background:
    Given the repository files and content is:
      | path                                              | content                                                                                                                         |
      | field/administracion-publica/field.json           | {"name": "Administración Pública"}                                                                                              |
      | field/sanidad/field.json                          | {"name": "Sanidad"}                                                                                                             |
      | field/turismo/field.json                          | {"name": "Turismo"}                                                                                                             |
      | party/partido-ficticio/party.json                 | {"name": "Partido Ficticio", "acronym": "PF", "programmeUrl": "http://partido-ficticio.es"}                                     |
      | party/otro-partido/party.json                     | {"name": "Otro Partido", "acronym": "OP", "programmeUrl": "http://otro-partido.es"}                                             |
      | field/sanidad/policy/partido-ficticio/content.md  | ## sanidad universal y gratuita                                                                                                 |
      | field/sanidad/policy/partido-ficticio/policy.json | {"party": "partido-ficticio", "field": "sanidad", "sources": ["http://partido-ficticio.es/programa/sanidad (páginas 8 a 10)"]}  |
      | field/sanidad/policy/otro-partido/content.md      | ## sanidad para todos                                                                                                           |
      | field/sanidad/policy/otro-partido/policy.json     | {"party": "otro-partido", "field": "sanidad", "sources": ["http://otro-partido.es/programa/sanidad (páginas 8 a 10)"]}          |
      | field/turismo/policy/otro-partido/content.md      | ## promover el turismo                                                                                                          |
      | field/turismo/policy/otro-partido/policy.json     | {"party": "otro-partido", "field": "turismo", "sources": ["http://otro-partido.es/programa/turismo (páginas 20 a 25)"]}         |
      | field/turismo/policy/partido-ficticio/content.md  | ## fomentar el turismo                                                                                                          |
      | field/turismo/policy/partido-ficticio/policy.json | {"party": "partido-ficticio", "field": "turismo", "sources": ["http://partido-ficticio.es/programa/turismo (páginas 20 a 25)"]} |
    And the content of the files are loaded into the system

  @backend
  Scenario: I successfully select my interests
    When I see the list of available "fields"
    And I select these interests:
      | sanidad                |
      | administracion-publica |
    Then my programme contains the interests:
      | sanidad                |
      | administracion-publica |
    And the next interest is "administracion-publica"

  @backend
  Scenario: shows an error if I select a non existing interest
    When I see the list of available "fields"
    And I select these interests:
      | non-existing |
    Then the system shows an error

  @backend
  Scenario: I successfully select my preferred policy linked to a field
    Given I see the list of available "fields"
    And I select these interests:
      | sanidad |
      | turismo |
    And the next interest is "sanidad"
    When I see these policies linked to the field "sanidad"
      | id                       | content                         |
      | partido-ficticio_sanidad | ## sanidad universal y gratuita |
      | otro-partido_sanidad     | ## sanidad para todos           |
    And I select the linked policy "partido-ficticio_sanidad"
    Then my programme contains these linked policies:
      | field   | policy                   |
      | sanidad | partido-ficticio_sanidad |
      | turismo |                          |
    And the next interest is "turismo"

  @backend
  Scenario: I select a non exiting policy
    Given I see the list of available "fields"
    And I select these interests:
      | sanidad |
      | turismo |
    And the next interest is "sanidad"
    When I see these policies linked to the field "sanidad"
      | id                       | content                         |
      | partido-ficticio_sanidad | ## sanidad universal y gratuita |
      | otro-partido_sanidad     | ## sanidad para todos           |
    And I select the linked policy "non-existing-policy"
    Then the system shows an error

  @backend @frontend
  Scenario: I complete my programme setting it to public
    Given I see the list of available "fields"
    And I select these interests:
      | sanidad |
      | turismo |
    And I see these policies linked to the field "sanidad"
      | id                       | content                         |
      | partido-ficticio_sanidad | ## sanidad universal y gratuita |
      | otro-partido_sanidad     | ## sanidad para todos           |
    And I select the linked policy "partido-ficticio_sanidad"
    And I see these policies linked to the field "turismo"
      | id                       | content                |
      | partido-ficticio_turismo | ## fomentar el turismo |
      | otro-partido_turismo     | ## promover el turismo |
    And I select the linked policy "otro-partido_turismo"
    And there is no next interest
    When I set my programme as completed and privacy "public"
    Then my programme contains these linked policies:
      | field   | policy                   |
      | sanidad | partido-ficticio_sanidad |
      | turismo | otro-partido_turismo     |
    And my programme is completed
    And my programme privacy is "public"
    And my programme party affinity is:
      | party            | affinity |
      | partido-ficticio | 1        |
      | otro-partido     | 1        |

  @backend
  Scenario: after 48h my public completed programme is accessible
    Given I see the list of available "fields"
    And I select these interests:
      | sanidad |
    And I see these policies linked to the field "sanidad"
      | id                       | content                         |
      | partido-ficticio_sanidad | ## sanidad universal y gratuita |
      | otro-partido_sanidad     | ## sanidad para todos           |
    And I select the linked policy "partido-ficticio_sanidad"
    And I set my programme as completed and privacy "public"
    When "48 hours" passes from my last programme modification
    Then my programme is still accessible

  @backend @frontend
  Scenario: I complete my programme setting it to private
    Given I see the list of available "fields"
    And I select these interests:
      | sanidad |
    And I see these policies linked to the field "sanidad"
      | id                       | content                         |
      | partido-ficticio_sanidad | ## sanidad universal y gratuita |
      | otro-partido_sanidad     | ## sanidad para todos           |
    And I select the linked policy "partido-ficticio_sanidad"
    And there is no next interest
    When I set my programme as completed and privacy "private"
    Then my programme contains these linked policies:
      | field   | policy                   |
      | sanidad | partido-ficticio_sanidad |
    And my programme is completed
    And my programme privacy is "private"
    And my programme party affinity is:
      | party            | affinity |
      | partido-ficticio | 1        |

  @backend
  Scenario: after 48h my private completed programme is not accessible
    Given I see the list of available "fields"
    And I select these interests:
      | sanidad |
    And I see these policies linked to the field "sanidad"
      | id                       | content                         |
      | partido-ficticio_sanidad | ## sanidad universal y gratuita |
      | otro-partido_sanidad     | ## sanidad para todos           |
    And I select the linked policy "partido-ficticio_sanidad"
    And I set my programme as completed and privacy "private"
    When "48 hours" passes from my last programme modification
    Then my programme is not accessible

  @backend @frontend
  Scenario: my completed and private programme is deleted
    Given I see the list of available "fields"
    And I select these interests:
      | sanidad |
    And I see these policies linked to the field "sanidad"
      | id                       | content                         |
      | partido-ficticio_sanidad | ## sanidad universal y gratuita |
      | otro-partido_sanidad     | ## sanidad para todos           |
    And I select the linked policy "partido-ficticio_sanidad"
    And there is no next interest
    And I set my programme as completed and privacy "private"
    When I delete my programme
    Then my programme is not accessible
