# Dockerfiles pour PHP et NODE
## Introduction
Anciennement, nous utilisions un script (init.sh ou new_ini.sh) qui configurait
php, composer, node et plus spécifiquement yarn avec le repository de l'état 
ainsi que l'environnement état. Il installait égualement composer et yarn.
Il fallait absolument lancer le script depuis un des deux conteneurs php ou node
pour pouvoir soit, utiliser composeur, sortir par les proxies ou encore utiliser
yarn.

Aujourd'hui, nous avons déplacé les actions de ce script dans un Dockerfiles,
- docker/dockerfile/node/Dockerfile

