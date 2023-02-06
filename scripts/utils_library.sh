#!/usr/bin/env bash
#    .--.
#   |o_o |
#   |:_/ |
#  //   \ \
# (|     | )
#/'\_   _/`\
#\___)=(___/
#+-------------------------------------------+
#| Script de librairies utiles aux scripts   |
#| de déploiements, doit être appelée dans   |
#| le script qui l'utilise.                  |
#| @author DBX <didier.beux@etat.ge.ch>      |
#| @since  jeu. 11 août 2022 14:41:26        |
#+-------------------------------------------+
# Url de base de Gitlab à l'état.
export git_url="git.devops.etat-ge.ch/gitlab/DEVELOPPEUR-PHP/10677-ngeo-d9.git"
# Url de base de nexus à l'état.
export nexus_url="***REMOVED***/repositories/project_release"


function print_message(){
  type_message="$1"
  message="$2"
  echo "${type_message} - ${message}"
}

function print_messages(){
  type_message="$1"
  messages="$2"
  echo "${type_message} -----------------"
  echo -en "${messages}\n"
  echo "FIN ${type_message} -------------"
}