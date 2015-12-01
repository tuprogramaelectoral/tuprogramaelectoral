# language: es
Característica: Construir mi programa electoral
  Con el fin de construir mi programa electoral
  Como votante
  Necesito seleccionar mis intereses y elegir mi politica preferida por cada uno

  Antecedentes:
    Dado que los ficheros y su contenido son los siguientes:
      | tipo               | path                                                   | contenido                                                                                                                                                                    |
      | ámbitos            | ambito/administracion-publica/ambito.json              | {"nombre": "Administración Pública"}                                                                                                                                         |
      | ámbitos            | ambito/sanidad/ambito.json                             | {"nombre": "Sanidad"}                                                                                                                                                        |
      | ámbitos            | ambito/turismo/ambito.json                             | {"nombre": "Turismo"}                                                                                                                                                        |
      | partidos           | partido/partido-ficticio/partido.json                  | {"nombre": "Partido Ficticio", "siglas": "PF", "programa": "http://partido-ficticio.es"}                                                                                     |
      | contenido política | ambito/sanidad/politica/partido-ficticio/contenido.md  | ## sanidad universal y gratuita                                                                                                                                              |
      | políticas          | ambito/sanidad/politica/partido-ficticio/politica.json | {"partido": "partido-ficticio", "ambito": "sanidad", "fuentes": ["http://partido-ficticio.es/programa/sanidad apartado sobre sanidad en el programa electoral del partido"]} |
    Y cargo los ficheros en el sistema

  @backend
  Escenario: Selecciono mis intereses
    Cuando veo la lista de "ámbitos" disponibles
    Y selecciono los siguientes intereses:
      | sanidad                |
      | administracion-publica |
    Entonces mi programa debería contener los siguientes intereses:
      | sanidad                |
      | administracion-publica |
    Y el próximo interés es "administracion-publica"

  @backend
  Escenario: Selecciono un interés no existente
    Cuando veo la lista de "ámbitos" disponibles
    Y selecciono los siguientes intereses:
      | no-existe |
    Entonces el sistema debería mostrar un error

  @backend
  Escenario: Elijo la política más afín a mi ideología
    Dado que selecciono los siguientes intereses:
      | sanidad |
      | turismo |
    Y el próximo interés es "sanidad"
    Cuando veo la lista de políticas del ámbito "sanidad"
    Y selecciono la política "partido-ficticio-sanidad"
    Entonces mi programa debería contener las siguientes políticas:
      | ámbito  | política                 |
      | sanidad | partido-ficticio-sanidad |
      | turismo |                          |
    Y el próximo interés es "turismo"

  @backend
  Escenario: Elijo una política que no existe
    Dado que selecciono los siguientes intereses:
      | sanidad |
      | turismo |
    Y el próximo interés es "sanidad"
    Cuando veo la lista de políticas del ámbito "sanidad"
    Y selecciono la política "politica-no-existente"
    Entonces el sistema debería mostrar un error
