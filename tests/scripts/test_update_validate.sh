#!/bin/bash

# Chemin du site dans le serveur.
#site_workspace="$1"
# Chemin de l'executable php dans le serveur.
#php_path="$2"
# Nom de la branche a deployer.
#git_branch="$3"
# Nom du projet a deployer.
#projegit_branchct_name="$4"
# Deploiement snapshot.
#is_deployment=$5
# Path jusqu'au site livré.
# site_path=""

# Appel de la librairie
source ../../scripts/utils_library.sh

function validate() {
if [[ "${site_workspace}" == "" ]] ||
    [[ "${php_path}" == "" ]] ||
    [[ "${git_branch}" == "" ]] ||
    [[ "${project_name}" == "" ]] ; then
    echo "Il manque des parametres, rappel:"
    echo "update.sh <site_workspace> <php_path> <git_branch> <is_deployment>"
    echo false
    return 1
  fi
  if [[ "${is_deployment}" == "" ]] && [[ "${project_name}" != "" ]]; then
    is_deployment=${project_name}
    project_name="${git_branch}"
    git_branch=""
    site_path="${site_workspace}"
    is_deployment=false
  else
    if ${is_deployment}; then
      site_path="${site_workspace}/branch/${git_branch}"
    else
      site_path="${site_workspace}/release/${git_branch}"
    fi
  fi
  return 0
}
function print_variables() {
  echo "site_workspace : ${site_workspace}"
  echo "php_path       : ${php_path}"
  echo "git_branch     : ${git_branch}"
  echo "project_name   : ${project_name}"
  echo "is_deployment  : ${is_deployment}"
  echo "site_path  : ${site_path}"
}


function test_1_toutes_les_variables_sont_renmplies(){
  print_message "TEST" "test_1_toutes_les_variables_sont_renmplies"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch="Ma git Branch de test"
  project_name="Mon project name de test"
  is_deployment=true
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

function test_2_aucune_variable_n_est_remplie(){
  print_message "TEST" "test_2_aucune_variable_n_est_remplie"
  site_workspace=""
  php_path=""
  git_branch=""
  project_name=""
  is_deployment=""
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


function test_3_toutes_sont_remplies_sauf_branch(){
  print_message "TEST" "test_3_toutes_sont_remplies_sauf_branch"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch="Mon project name de test"
  project_name=true
  is_deployment=
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

function test_4_toutes_sont_remplies_et_is_deployment_false(){
  print_message "TEST" "test_4_toutes_sont_remplies_et_is_deployment_false"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch="Ma git Branch de test"
  project_name="Mon project name de test"
  is_deployment=false
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

function test_5_toutes_sauf_branch_et_is_deployment_false(){
  print_message "TEST" "test_5_toutes_sauf_branch_et_is_deployment_false"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch="Mon project name de test"
  project_name=false
  is_deployment=
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

function test_6_Seuls_site_workspace_et_php_path_sont_remplis(){
  print_message "TEST" "test_5_toutes_sauf_branch_et_is_deployment_false"
  site_workspace="Mon site Workspace de test"
  php_path="Mon PHP Path de test"
  git_branch=""
  project_name=""
  is_deployment=""
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


test_1_toutes_les_variables_sont_renmplies
test_2_aucune_variable_n_est_remplie
test_3_toutes_sont_remplies_sauf_branch
test_4_toutes_sont_remplies_et_is_deployment_false
test_5_toutes_sauf_branch_et_is_deployment_false
test_6_Seuls_site_workspace_et_php_path_sont_remplis
