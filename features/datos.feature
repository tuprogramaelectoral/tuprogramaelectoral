# language: es
Característica: Carga de datos iniciales
  Con el fin de disponer de los datos iniciales para ejecutar la aplicación
  Como desarrollador de la aplicación
  Necesitamos leer los datos y cargarlos en el sistema

  Antecedentes:
    Dado que los ficheros y su contenido son los siguientes:
      | tipo               | path                                                   | contenido                                                                                                                                                                    |
      | ámbitos            | ambito/administracion-publica/ambito.json              | {"nombre": "Administración Pública"}                                                                                                                                         |
      | ámbitos            | ambito/sanidad/ambito.json                             | {"nombre": "Sanidad"}                                                                                                                                                        |
      | partidos           | partido/partido-ficticio/partido.json                  | {"nombre": "Partido Ficticio", "siglas": "PF", "programa": "http://partido-ficticio.es"}                                                                                     |
      | contenido política | ambito/sanidad/politica/partido-ficticio/contenido.md  | ## sanidad universal y gratuita                                                                                                                                              |
      | políticas          | ambito/sanidad/politica/partido-ficticio/politica.json | {"partido": "partido-ficticio", "ambito": "sanidad", "fuentes": ["http://partido-ficticio.es/programa/sanidad apartado sobre sanidad en el programa electoral del partido"]} |
    Y cargo los ficheros en el sistema

  @backend
  Escenario: Los ámbitos están en el sistema y son válidos
    Cuando veo la lista de "ámbitos" disponibles
    Entonces la lista de "ámbitos" debería contener:
      | id                     | nombre                 |
      | administracion-publica | Administración Pública |
      | sanidad                | Sanidad                |

  @backend
  Escenario: Los partidos están en el sistema y son válidos
    Cuando veo la lista de "partidos" disponibles
    Entonces la lista de "partidos" debería contener:
      | id               | nombre           | siglas | programa                   |
      | partido-ficticio | Partido Ficticio | PF     | http://partido-ficticio.es |

  @backend
  Escenario: Los politicas relacionadas con un ámbito están en el sistema y son válidos
    Cuando veo la lista de políticas del ámbito "sanidad"
    Entonces la lista de "políticas" debería contener:
      | id                       | partidoId        | ambitoId | fuentes                                                                                                     | contenido                       |
      | partido-ficticio-sanidad | partido-ficticio | sanidad  | ["http://partido-ficticio.es/programa/sanidad apartado sobre sanidad en el programa electoral del partido"] | ## sanidad universal y gratuita |
