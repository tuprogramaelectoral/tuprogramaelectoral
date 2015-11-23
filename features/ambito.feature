# language: es
Característica: Ámbitos del programa electoral
  Con el fin de elegir los ámbitos en los que estoy interesado
  Como usuario de la aplicación
  Necesitamos mostrar la lista de ámbitos disponibles

  @dominio
  Escenario: La lista de ámbitos está vacia
    Dado que no existen "ámbitos" en el sistema
    Cuando veo la lista de "ámbitos" disponibles
    Entonces la lista de "ámbitos" debería estar vacía

  @dominio @backend
  Escenario: La lista de ámbitos tiene elementos
    Dado que existen los siguientes "ámbitos":
      | {"nombre": "Empleo"}                 |
      | {"nombre": "Administración Pública"} |
    Cuando veo la lista de "ámbitos" disponibles
    Entonces la lista de "ámbitos" debería contener
      | id                     | nombre                 |
      | empleo                 | Empleo                 |
      | administracion-publica | Administración Pública |

  @aceptacion
  Escenario: La lista de ámbitos en la página principal contiene los existentes en el repositorio
    Dado que la aplicación está ejecutándose
    Cuando visito la página principal
    Entonces veo los ámbitos existentes en el repositorio
