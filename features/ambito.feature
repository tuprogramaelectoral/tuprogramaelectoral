Feature: Ámbitos del programa electoral
  Con el fin de elegir los ámbitos en los que estoy interesado
  Como votante
  Necesito ver la lista de ámbitos disponibles

  @acceptance
  Scenario: La lista de ámbitos en la página principal contiene los existentes en el repositorio
    Cuando visito la página principal
    Entonces veo los ámbitos existentes en el repositorio
