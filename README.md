# Tu Programa Electoral

[![Build Status](https://travis-ci.org/tuprogramaelectoral/tuprogramaelectoral.svg)](https://travis-ci.org/tuprogramaelectoral/tuprogramaelectoral)

Web que muestra los programas electorales de cada partido clasificados por ámbitos de actuación, los usuarios eligen las políticas de cada programa que consideran más afines a su ideología y al final del proceso muestra que porcentaje de afinidad tiene el usuario con cada partido y su selección de políticas.

Esta es una versión española desde cero de la web https://voteforpolicies.org.uk/

## Aplicaciones necesarias

 * docker (https://docs.docker.com/installation/)

## Cargar aliases

Por conveniencia se han creado una serie de alias en el directorio `artifacts`, puedes hacer que se carguen automáticamente ejecutando lo siguiente

```shell
cat <<EOF >> ~/.bashrc
if [ -f $PWD/artifacts/aliases ]; then
    export TPE_PATH=$PWD
    source $PWD/artifacts/aliases
fi
EOF
source ~/.bashrc
```

## Montar el entorno de desarrollo

```shell
tpe-compose up -d
```

## Para usuarios de OSX

Si quieres evitar problemas con los permisos de escritura con los volúmenes de docker necesitarás instalar unison

```shell
brew install unison
brew install fswatch
```

Y para montar el entorno de desarrollo

```shell
tpe-compose-osx up -d codebase
tpe-unison
tpe-compose-osx up -d
tpe-unison
```

Después de ese proceso sólo es necesario ejecutar lo siguiente para levantar el sistema y sincronizar los cambios

```shell
tpe-compose-osx up -d
tpe-unison-fsmonitor
```
