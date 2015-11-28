# language: es
Característica: Ámbitos del programa electoral
  Con el fin de elegir los ámbitos en los que estoy interesado
  Como usuario de la aplicación
  Necesitamos mostrar la lista de ámbitos disponibles

  @aceptacion
  Escenario: La lista de ámbitos en la página principal contiene los existentes en el repositorio
    Dado que la aplicación está ejecutándose
    Cuando visito la página principal
    Entonces veo los ámbitos existentes en el repositorio
