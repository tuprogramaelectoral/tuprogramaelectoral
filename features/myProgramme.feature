Feature: Construir mi programa electoral
  In order to build my programme
  as a voter
  I need to select my interests and select my preferred linked policy to each of them

  Background:
    Given the repository files and content is:
      | type           | path                                              | content                                                                                                                        |
      | fields         | field/administracion-publica/field.json           | {"name": "Administración Pública"}                                                                                             |
      | fields         | field/sanidad/field.json                          | {"name": "Sanidad"}                                                                                                            |
      | fields         | field/turismo/field.json                          | {"name": "Turismo"}                                                                                                            |
      | parties        | party/partido-ficticio/party.json                 | {"name": "Partido Ficticio", "acronym": "PF", "programmeUrl": "http://partido-ficticio.es"}                                    |
      | parties        | party/otro-partido/party.json                     | {"name": "Otro Partido", "acronym": "OP", "programmeUrl": "http://otro-partido.es"}                                            |
      | policy content | field/sanidad/policy/partido-ficticio/content.md  | ## sanidad universal y gratuita                                                                                                |
      | policies       | field/sanidad/policy/partido-ficticio/policy.json | {"party": "partido-ficticio", "field": "sanidad", "sources": ["http://partido-ficticio.es/programa/sanidad (páginas 8 a 10)"]} |
      | policy content | field/turismo/policy/otro-partido/content.md      | ## promover el turismo                                                                                                         |
      | policies       | field/turismo/policy/otro-partido/policy.json     | {"party": "otro-partido", "field": "turismo", "sources": ["http://otro-partido.es/programa/turismo (páginas 20 a 25)"]}        |
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
    Given that I select these interests:
      | sanidad |
      | turismo |
    And the next interest is "sanidad"
    When I see the list of policies linked to the field "sanidad"
    And I select the linked policy "partido-ficticio_sanidad"
    Then my programme contains these linked policies:
      | field   | policy                   |
      | sanidad | partido-ficticio_sanidad |
      | turismo |                          |
    And the next interest is "turismo"

  @backend
  Scenario: I select a non exiting policy
    Given that I select these interests:
      | sanidad |
      | turismo |
    And the next interest is "sanidad"
    When I see the list of policies linked to the field "sanidad"
    And I select the linked policy "non-existing-policy"
    Then the system shows an error

  @backend
  Scenario: I complete my programme setting it to public
    Given that I select these interests:
      | sanidad |
      | turismo |
    And I see the list of policies linked to the field "sanidad"
    And I select the linked policy "partido-ficticio_sanidad"
    And I see the list of policies linked to the field "turismo"
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
    Given that I select these interests:
      | sanidad |
    And I see the list of policies linked to the field "sanidad"
    And I select the linked policy "partido-ficticio_sanidad"
    And I set my programme as completed and privacy "public"
    When "48 hours" passes from my last programme modification
    Then my programme is still accessible

  @backend
  Scenario: I complete my programme setting it to private
    Given that I select these interests:
      | sanidad |
    And I see the list of policies linked to the field "sanidad"
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
    Given that I select these interests:
      | sanidad |
    And I see the list of policies linked to the field "sanidad"
    And I select the linked policy "partido-ficticio_sanidad"
    And I set my programme as completed and privacy "private"
    When "48 hours" passes from my last programme modification
    Then my programme is not accessible
