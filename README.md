# Tu Programa Electoral

[![Build Status](https://travis-ci.org/tuprogramaelectoral/tuprogramaelectoral.svg)](https://travis-ci.org/tuprogramaelectoral/tuprogramaelectoral)

Web that presents policies from the electoral programme of the principal Spanish parties, for the 2015 general election, classified by scope.
Users select the policy linked to each relevant scope that they feel more related with, without knowing the party they belong to.
At the end of the process a graph with the user party affinity is shown and the list of selected policies, along the name of the party who wrote them.

This is a Spanish web application based in the idea behind https://voteforpolicies.org.uk/

**Si quieres colaborar con su desarrollo, diseño y dotación de contenido pásate por la [wiki](https://github.com/tuprogramaelectoral/tuprogramaelectoral/wiki)**

## Required applications

 * docker (https://docs.docker.com/installation/)

## Load of aliases

For convenience a series of aliases have been created inside the folder `artifacts`, you can load them automatically running the next command:

```shell
cat <<EOF >> ~/.bashrc
if [ -f $PWD/artifacts/aliases ]; then
    export TPE_PATH=$PWD
    source $PWD/artifacts/aliases
fi
EOF
source ~/.bashrc
```

## Load of development hosts

```shell
sudo -s 'cat <<EOF >> /etc/hosts
127.0.0.1   tuprogramaelectoral.dev
127.0.0.1   api.tuprogramaelectoral.dev
EOF'
```

## Install dependencies

Those commands just need to be executed once, they will download the PHP, Node and Bower dependencies
and every time a new dependency is added/changed

```shell
tpe-compose build
tpe-compose run backend install-app
tpe-compose run frontend install-app
```

## Start the development environment

```shell
tpe-compose build
tpe-compose up -d
```
