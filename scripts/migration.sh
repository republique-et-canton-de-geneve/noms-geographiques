#!/usr/bin/env bash
#    .--.
#   |o_o |
#   |:_/ |
#  //   \ \
# (|     | )
#/'\_   _/`\
#\___)=(___/
#+-------------------------------------------+
#| Script de migration des données D7 vers D9|
#| La base de donnée initiale est à la raci- |
#| ne du projet.                             |
#| @author DBX <didier.beux@etat.ge.ch>      |
#| @since  jeu. 11 août 2022 14:41:26        |
#+-------------------------------------------+

# Path du site dans le serveur.
site_workspace="$1"
# Le nom de la branche
git_branch="$2"
# Deploiement snapshot.
is_deployment=$3

# Chemin complet du site.
site_path=""
# Chemin des binaires dans vendor.
vendor_bin_path="${site_path}/vendor/bin"

# Chemin du dump de la base drupal initiale.
fresh_db_path=""
# Chemin vers le php.init personnalisé
php_init_path=""

# Mise à jour du PATH pour l'accès à la commande drush
export PATH="${vendor_bin_path}:${PATH}"


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
      site_path="${site_workspace}/releases/${git_branch}"
    fi
      fresh_db_path="${site_path}/drupal-fresh-install.dump"
      php_init_path="${site_path}/config/PHPRC"
  fi
  if [[ ${error} -eq 1 ]]; then
    print_message "WARNING" "${message}"
  fi
  return ${error}
}

function init_database(){
  print_message "INFO" "Suppression de la base actuelle"
  drush sql:drop -y
  print_message "INFO" "Récupération de la base vide initiale Drupal"
  drush sql:cli <${fresh_db_path} -y
  print_message "INFO" "Reconstruction du cache Drupal"
  drush cache:rebuild
}

function import_configuration(){
  print_message "INFO" "Mise à jour de la base"
  drush updatedb -y
  print_message "INFO" "Importation de la configuration"
  drush config:import -y
  drush config:import -y
  print_message "INFO" "Suppression du contenu du repertoire media"
  rm -rf "${site_path:?}/media/*"
}

function migrate_noms_geographique(){
  print_message "INFO" "Lancement de la migration du site Noms géographique D7 dans D9"
  drush migrate:import --group=ngeo_lieu_geneve --execute-dependencies
  drush migrate:import ngeo_page_node
  chmod -R a+r ${site_path}/media/
}

function maintenance_is_set(){
  is_set=$1
  if ${is_set}; then
    print_message "INFO" "Activation du mode maintenance."
    drush state:set system.maintenance_mode 1
    drush cache:rebuild
  elif ! ${is_set}; then
    print_message "INFO" "désactivation du mode maintenance."
    drush state:set system.maintenance_mode 0
    drush cache:rebuild
  fi
}

# main treatment
if validate; then
  # shellcheck disable=SC1090
  source "${site_path}/scripts/utils_library.sh"
  # Variable d'environnement utilisée par drush.
  export PHPRC="${php_init_path}"

  print_message "INFO" "Lancement de la migration."
  init_database
  maintenance_is_set true
  import_configuration
  migrate_noms_geographique
  maintenance_is_set false
  print_message "INFO" "Migration effectuée."
else
  print_message "WARNING" "Un problème est survenu."
  print_help
  exit 1
fi
