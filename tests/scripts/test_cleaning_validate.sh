#!/bin/bash
# Chemin du site dans le serveur.
#site_workspace="$1"
# Nom de la branche a deployer.
#git_branch="$2"
# Chemin de l'executable php dans le serveur.
#php_path="$3"

function print_help(){
  echo "Il manque des parametres, rappel:"
  echo "cleaning.sh <site_workspace> <php_path> [<git_branch>]"
  echo
  echo "parameters :"
  echo "   - site_workspace : emplacement du site dans le serveur."
  echo "   - php_path       : emplacement de l'executable php dans le serveur."
  echo "   - git_branch     : nom de la branche déployée."
}

# Appel de la librairie
source ../../scripts/utils_library.sh

function print_help(){
  echo "Il manque des parametres, rappel:"
  echo "cleaning.sh <site_workspace> <php_path> [<git_branch>]"
  echo
  echo "parameters :"
  echo "   - site_workspace : emplacement du site dans le serveur."
  echo "   - php_path       : emplacement de l'executable php dans le serveur."
  echo "   - git_branch     : nom de la branche déployée."
}

function validate() {
  error=0
  if [[ "${site_workspace}" == "" ]] ||
     [[ "${php_path}" == "" ]]; then
     error=1
  elif [[ "${git_branch}" == "" ]]; then
    site_path=${site_workspace}
  else
    site_path=${site_workspace}/branch/${git_branch}
  fi
  if [[ ${error} -gt 0 ]]; then
     print_help
  fi

  return ${error}
}


function print_variables() {
  echo "site_workspace : ${site_workspace}"
  echo "php_path       : ${php_path}"
  echo "git_branch     : ${git_branch}"
  echo "site_path      : ${site_path}"
}


function test_1_tout_est_rempli(){
  print_message "TEST" "test_1_tout_est_rempli"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch="Ma git Branch de test"
  site_path=""

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

function test_2_rien_n_est_rempli(){
  print_message "TEST" "test_2_rien_n_est_rempli"
  site_workspace=""
  php_path=""
  git_branch=""
  site_path=""

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

function test_3_il_manque_la_branche(){
  print_message "TEST" "test_3_il_manque_la_branche"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch=""
  site_path=""

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

test_1_tout_est_rempli
test_2_rien_n_est_pas_rempli
test_3_il_manque_la_branche
