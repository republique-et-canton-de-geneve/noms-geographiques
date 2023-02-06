#!/usr/bin/env bash
#    .--.
#   |o_o |
#   |:_/ |
#  //   \ \
# (|     | )
#/'\_   _/`\
#\___)=(___/
#+-------------------------------------------+
#| Script package le projet et le pousse     |
#| dans Nexus                                |
#| @author DBX <didier.beux@etat.ge.ch>      |
#| @since  lun. 10 oct. 2022 12:47:13        |
#+-------------------------------------------+
# Chemin du site dans le serveur.
#site_workspace="$1"
# Chemin de l'executable php dans le serveur.
#php_path="$2"
# Nom de la branche a deployer.
#git_branch="$3"
# Repository du projet dans Nexus.
#artifact_id="$4"
# Chemin jusqu'au repository dans Nexus.
#artifact_group_id="$5"
# Version to deploye
#version="$6"
# Do you want to keep archive ? true/false
# keep_archive=$7

# Appel de la librairie
source ../../scripts/utils_library.sh

# Path jusqu'au site livré.
site_path=""

function print_help() {
  echo "Il manque des parametres, rappel:"
  echo "packaging.sh <site_workspace> <php_path> <git_branch> <artifact_id> <artifact_group_id> <version>"
  echo
  echo "parameters :"
  echo "   - site_workspace    : emplacement du site dans le serveur."
  echo "   - php_path          : emplacement de l'executable php dans le serveur."
  echo "   - git_branch        : nom de la branche déployée."
  echo "   - artifact_id       : répertoire du projet dans Nexus."
  echo "   - artifact_group_id : Chemin du domaine qui contient le projet  dans Nexus."
  echo "   - version           : version à déployer."
  echo "   - keep_archive      : Would you like to keep archive ? true|false"
}

function validate() {
  if [[ "${site_workspace}" == "" ]] ||
    [[ "${php_path}" == "" ]] ||
    [[ "${git_branch}" == "" ]] ||
    [[ "${artifact_id}" == "" ]] ||
    [[ "${artifact_group_id}" == "" ]] ||
    [[ "${version}" == "" ]]; then
    print_help
    return 1
  fi
  if [[ "${keep_archive}" == "" ]]; then
    keep_archive=false
  fi
  site_path="${site_workspace}/build/${git_branch}"
  return 0
}

function print_variables() {
  echo "site_workspace    : ${site_workspace}"
  echo "php_path          : ${php_path}"
  echo "git_branch        : ${git_branch}"
  echo "artifact_id       : ${artifact_id}"
  echo "artifact_group_id : ${artifact_group_id}"
  echo "version           : ${version}"
  echo "site_path         : ${site_path}"
  # shellcheck disable=SC2154
  echo "nexus_url         : ${nexus_url}"
  echo "keep_archive      : ${keep_archive}"
}

function test_1_toutes_les_variables_sont_renmplies() {
  print_message "TEST" "test_1_toutes_les_variables_sont_renmplies"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch="Ma git Branch de test"
  artifact_id="1234_test"
  artifact_group_id="ch.ge.mon_domaine"
  version="1.0.12"
  keep_archive=true

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
  php_path="Mon PHP Path de test"
  git_branch="Ma git Branch de test"
  artifact_id="1234_test"
  artifact_group_id="ch.ge.mon_domaine"
  version=""

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

function test_3_if_not_keep_archive() {
  print_message "TEST" "test_3_if_not_keep_archive"
  keep_archive=false
  if ! ${keep_archive}; then
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

function test_4_if_keep_archive() {
  print_message "TEST" "test_4_if_keep_archive"
  keep_archive=true
  if ${keep_archive}; then
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
test_3_if_not_keep_archive
test_4_if_keep_archive
