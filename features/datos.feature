# language: es
Característica: Carga de datos iniciales
  Con el fin de disponer de los datos iniciales para ejecutar la aplicación
  Como desarrollador de la aplicación
  Necesitamos leer los datos y cargarlos en el sistema

  @dominio @backend
  Escenario: Los datos están en el sistema de ficheros y son válidos
    Dado que los ficheros y su contenido son los siguientes:
      | tipo     | path                                      | contenido                                                                                |
      | ámbitos  | ambito/administracion-publica/ambito.json | {"nombre": "Administración Pública"}                                                     |
      | ámbitos  | ambito/agricultura/ambito.json            | {"nombre": "Agricultura"}                                                                |
      | partidos | partido/partido-ficticio/partido.json     | {"nombre": "Partido Ficticio", "siglas": "PF", "programa": "http://partido-ficticio.es"} |
    Cuando cargo los ficheros en el sistema
    Entonces el sistema contiene los siguientes "ámbitos"
      | id                     | nombre                 |
      | administracion-publica | Administración Pública |
      | agricultura            | Agricultura            |
    Y el sistema contiene los siguientes "partidos"
      | id               | nombre           | siglas | programa                   |
      | partido-ficticio | Partido Ficticio | PF     | http://partido-ficticio.es |

