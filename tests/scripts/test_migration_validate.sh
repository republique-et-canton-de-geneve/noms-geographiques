#!/bin/bash
# Path du site dans le serveur.
#site_workspace="$1"
# Le nom de la branche
#git_branch="$2"
# Deploiement snapshot.
#is_deployment=$3
# Chemin complet du site.
#site_path=""
# Chemin des binaires dans vendor.
#vendor_bin_path="${site_path}/vendor/bin"
# Chemin du dump de la base drupal initiale.
#fresh_db_path=""
# Chemin vers le php.init personnalisé
#php_init_path=""

# Appel de la librairie
source ../../scripts/utils_library.sh

function print_help(){
  echo "Il manque des parametres, rappel:"
  echo "migration.sh <site_workspace> <php_path> [<git_branch>] <is_deployment>"
  echo
  echo "parameters :"
  echo "   - site_workspace : emplacement du site dans le serveur."
  echo "   - php_path       : emplacement de l'executable php dans le serveur."
  echo "   - git_branch     : nom de la branche déployée."
  echo "   - is_deployment  : Type de deploiement. Par defaut, la valeur est false"
}

function validate() {
  error=0
  message=

  if [[ "${site_workspace}" == "" ]]; then
    message="Il manque le paramêtre site_workspace"
    error=1
  fi
  if [[ ${error} -eq 0 ]]; then
    if [[ "${is_deployment}" == "" ]] &&
       [[ "${git_branch}" == "true" ]] || [[ "${git_branch}" == "false" ]]; then
      site_path="${site_workspace}"
    elif ${is_deployment}; then
      site_path="${site_workspace}/branch/${git_branch}"
    else
      site_path="${site_workspace}/release/${git_branch}"
    fi
      fresh_db_path="${site_path}/drupal-fresh-install.dump"
      php_init_path="${site_path}/config/PHPRC"
  fi
  if [[ ${error} -eq 1 ]]; then
    print_message "WARNING" "${message}"
  fi
  return ${error}
}

function print_variables() {
  echo "site_workspace : ${site_workspace}"
  echo "php_path       : ${php_path}"
  echo "git_branch     : ${git_branch}"
  echo "is_deployment  : ${is_deployment}"
  echo "site_path      : ${site_path}"
  echo "fresh_db_path  : ${fresh_db_path}"
  echo "php_init_path  : ${php_init_path}"
}


function test_1_tout_est_rempli_is_deployement_true(){
  print_message "TEST" "test_1_tout_est_rempli_is_deployement_true"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch="Ma git Branch de test"
  is_deployment=true
  site_path=""
  fresh_db_path=""
  php_init_path=""

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
function test_2_tout_est_rempli_is_deployement_false(){
  print_message "TEST" "test_2_tout_est_rempli_is_deployement_false"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch="Ma git Branch de test"
  is_deployment=false
  site_path=""
  fresh_db_path=""
  php_init_path=""

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

function test_3_rien_n_est_rempli(){
  print_message "TEST" "test_3_rien_n_est_rempli"
  site_workspace=""
  php_path=""
  git_branch=""
  is_deployment=""
  site_path=""
  fresh_db_path=""
  php_init_path=""

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

function test_4_il_manque_la_branche_is_deployent_true(){
  print_message "TEST" "test_4_il_manque_la_branche_is_deployent_true"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch="true"
  is_deployment=""
  site_path=""
  fresh_db_path=""
  php_init_path=""

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
function test_5_il_manque_la_branche_is_deployent_false(){
  print_message "TEST" "test_5_il_manque_la_branche_is_deployent_false"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch=false
  is_deployment=""
  site_path=""
  fresh_db_path=""
  php_init_path=""

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

test_1_tout_est_rempli_is_deployement_true
test_2_tout_est_rempli_is_deployement_false
test_3_rien_n_est_rempli
test_4_il_manque_la_branche_is_deployent_true
test_5_il_manque_la_branche_is_deployent_false
