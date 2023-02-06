#!/usr/bin/env bash
#    .--.
#   |o_o |
#   |:_/ |
#  //   \ \
# (|     | )
#/'\_   _/`\
#\___)=(___/
#+-------------------------------------------+
#| Script qui tag dans git la branch selon   |
#| la version passée en paramètre.           |
#| @author DBX <didier.beux@etat.ge.ch>      |
#| @since  lun. 10 oct. 2022 11:48:15        |
#+-------------------------------------------+
# shellcheck disable=SC2154
# Chemin du site dans le serveur.
#site_workspace="$1"
# Chemin de l'executable php dans le serveur.
#php_path="$2"
# Nom de la branche a deployer.
#git_branch="$3"
# Repository du projet dans Nexus.
#git_user="$4"
# Chemin jusqu'au repository dans Nexus.
#git_pwd="$5"
# Version to deploye
#version="$6"
# Url du projet dans Gitlab.
#url="$7"

# Appel de la librairie
source ../../scripts/utils_library.sh

function print_help() {
  echo "Il manque des parametres, rappel:"
  echo "tagging.sh <site_workspace> <php_path> <git_branch> <git_user> <git_pwd> <version>"
  echo
  echo "parameters :"
  echo "   - site_workspace : emplacement du site dans le serveur."
  echo "   - php_path       : emplacement de l'executable php dans le serveur."
  echo "   - git_branch     : nom de la branche déployée."
  echo "   - git_user       : utilisateur git actuel."
  echo "   - git_pwd        : mot de passe de l'utilisateur git actuel."
  echo "   - version        : version à déployer."
  echo "   - url            : url Gitlab du projet."
}

function validate() {
  if [[ "${site_workspace}" == "" ]] ||
    [[ "${php_path}" == "" ]] ||
    [[ "${git_branch}" == "" ]] ||
    [[ "${git_user}" == "" ]] ||
    [[ "${git_pwd}" == "" ]] ||
    [[ "${version}" == "" ]]; then
    print_help
    return 1
  fi
  site_path="${site_workspace}/branch/${git_branch}"
  return 0
}

function print_variables() {
  echo "site_workspace : ${site_workspace}"
  echo "php_path       : ${php_path}"
  echo "git_branch     : ${git_branch}"
  echo "git_user       : ${git_user}"
  echo "git_pwd        : ${git_pwd}"
  echo "version        : ${version}"
  echo "site_path      : ${site_path}"
  echo "git_url        : ${git_url}"
}

function test_1_toutes_les_variables_sont_renmplies() {
  print_message "TEST" "test_1_toutes_les_variables_sont_renmplies"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch="Ma git Branch de test"
  git_user="Test_USER"
  git_pwd="Test_PASSWORD"
  version="1.0.12"

  if validate; then
    echo "+--------+"
    echo "| Valide |"
    echo "+--------+"
    print_variables
  else
    echo "+------------+"
    echo "| Not valide |"
    echo "+------------+"
    print_variables
  fi
}

function test_2_une_des_variables_n_est_pas_remplie() {
  print_message "TEST" "test_2_une_des_variables_n_est_pas_remplie"
  site_workspace="Mon site Workspace de test"
  php_path=""
  git_branch="Ma git Branch de test"
  git_user="Test_USER"
  git_pwd="Test_PASSWORD"
  version="1.0.12"

  if validate; then
    echo "+--------+"
    echo "| Valide |"
    echo "+--------+"
    print_variables
  else
    echo "+------------+"
    echo "| Not valide |"
    echo "+------------+"
    print_variables
  fi
}

test_1_toutes_les_variables_sont_renmplies
test_2_une_des_variables_n_est_pas_remplie
