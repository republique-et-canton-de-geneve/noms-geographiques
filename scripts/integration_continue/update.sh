#!/usr/bin/env bash
#    .--.
#   |o_o |
#   |:_/ |
#  //   \ \
# (|     | )
#/'\_   _/`\
#\___)=(___/
#+-------------------------------------------+
#| Script mise en place du site pour qu'il   |
#| soit exploitable depuis le serveur.       |
#| @author DBX <didier.beux@etat.ge.ch>      |
#| @since  jeu. 11 août 2022 14:41:26        |
#+-------------------------------------------+

# Chemin du site dans le serveur.
site_workspace="$1"
# Chemin de l'executable php dans le serveur.
php_path="$2"
# Nom de la branche a deployer.
git_branch="$3"
# Nom du projet a deployer.
project_name="$4"
# Deploiement snapshot.
is_deployment=$5
# Path jusqu'au site livré.
site_path=""

function print_help() {
  echo "Il manque des parametres, rappel:"
  echo "update.sh <site_workspace> <php_path> <git_branch> [is_deployment]"
  echo
  echo "parameters :"
  echo "   - site_workspace : emplacement du site dans le serveur."
  echo "   - php_path       : emplacement de l'executable php dans le serveur"
  echo "   - git_branch     : nom de la branche a deployer."
  echo "   - project_name   : nom du projet a deployer. !! Obsolète"
  echo "   - is_deployment    : Type de deploiement. Par defaut, la valeur est false"
}

function validate() {
  if [[ "${site_workspace}" == "" ]] ||
     [[ "${php_path}" == "" ]] ||
     [[ "${git_branch}" == "" ]] ; then
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
      site_path="${site_workspace}/releases/${git_branch}"
    fi
  fi
  return 0
}
function createSymbolicLinks() {
  print_message "INFO" "Creation des liens symboliques utiles au deploiement ..."
  print_message "INFO" "Creation du lien ${site_workspace}/htdocs sur ${site_path}/htdocs."
  ln -sfn "${site_path}/htdocs" ${site_workspace}/htdocs
  print_message "INFO" "Creation du lien ${site_path}/htdocs/media sur ${site_workspace}/media."
  ln -sfn ${site_workspace}/media "${site_path}/htdocs/media"
  print_message "INFO" "Creation du lien ${site_workspace}/vendor sur ${site_path}/vendor."
  ln -sfn "${site_path}/vendor" ${site_workspace}/vendor
  print_message "INFO" "Creation du lien ${site_workspace}/config/sync sur ${site_path}/config/sync."
  ln -sfn "${site_path}/config/sync" ${site_workspace}/config/sync
  print_message "INFO" "Creation du lien ${site_workspace}/config/saml sur ${site_path}/config/sync/saml/dev."
  ln -sfn "${site_path}/config/saml/dev" ${site_workspace}/config/saml
  if [[ -f ${site_workspace}/htdocs/sites/default/settings.local.php ]]; then
    print_message "INFO" "Suppression du fichier ${site_workspace}/htdocs/sites/default/settings.local.php existant."
    rm ${site_workspace}/htdocs/sites/default/settings.local.php
  fi
  print_message "INFO" "Creation du lien settings.local.php a partir de ${site_path}/config/settings.local.php dans ${site_workspace}/htdocs/sites/default/."
  ln -sfn "${site_path}/config/settings.local.dev.example.php" ${site_workspace}/htdocs/sites/default/settings.local.php
#  print_message "INFO" "Creation du lien symbolique ${project_name} dans htdocs"
#  # shellcheck disable=SC2164
#  cd "${site_workspace}/htdocs"
#  ln -s . ${project_name}
  print_message "INFO" "Supression des droits en écriture pour le groupe et les autres sur le répertoire et fichiers du sites/default et suivants."
  chmod -R go-w "${site_workspace}/htdocs/sites/default"
}

function createSamlSystem() {
  if [[ -d ${site_workspace}/vendor/simplesamlphp/simplesamlphp/www ]]; then
    print_message "INFO" "Creation du lien symbolique ${site_workspace}/htdocs/saml a partir de ${site_workspace}/vendor/simplesamlphp/simplesamlphp/www."
    ln -sfn ${site_workspace}/vendor/simplesamlphp/simplesamlphp/www ${site_workspace}/htdocs/saml
  fi
  if [[ -d ${site_workspace}/vendor/simplesamlphp/simplesamlphp/metadata ]]; then
    if [[ -f ${site_workspace}/vendor/simplesamlphp/simplesamlphp/metadata/saml20-idp-remote.php ]]; then
      print_message "INFO" "Suppression du fichier ${site_workspace}/vendor/simplesamlphp/simplesamlphp/metadata/saml20-idp-remote.php existant."
      rm ${site_workspace}/vendor/simplesamlphp/simplesamlphp/metadata/saml20-idp-remote.php
    fi
  fi
}

function startCron() {
  # mise en place du script pour le cron
  chmod +x "${site_path}/scripts/10677run-cron.sh"
  if [[ ! -L ${site_workspace} ]]; then
    ln -sfn "${site_path}" ${site_workspace}
  fi
}

function setAssetsIntoVendor() {
  print_message "INFO" "Creation du repertoire bin dans ${site_workspace}/htdocs/vendor"
  mkdir -p ${site_workspace}/vendor/bin
  print_message "INFO" "Dans le repretoire ${site_workspace}/vendor/bin"
  # shellcheck disable=SC2164
  cd "${site_workspace}"/vendor/bin
  print_message "INFO" "Creation du lien drush depuis ../drush/drush/drush"
  ln -sf ../drush/drush/drush drush
  if ${is_deployment}; then
    print_message "INFO" "Creation du lien psysh depuis ../psy/psysh/bin/psysh"
    ln -sf ../psy/psysh/bin/psysh psysh
    print_message "INFO" "Creation du lien robo depuis ../consolidation/robo/robo"
    ln -sf ../consolidation/robo/robo robo
    print_message "INFO" "Application des droits d'execution dans tout le repetoire ${site_workspace}/vendor/bin"
    chmod u+x ${site_workspace}/vendor/drush/drush/drush \
      ${site_workspace}/vendor/psy/psysh/bin/psysh \
      ${site_workspace}/vendor/consolidation/robo/robo
  else
    chmod u+x ${site_workspace}/vendor/drush/drush/drush
  fi
}

function drushExecutions() {
  print_message "INFO" "Exportation de l'environnement necessaire."
  export PATH="${php_path}:${site_workspace}/vendor/bin:${PATH}"
  export PHPRC=${site_path}/config/PHPRC
  print_message "INFO" "Nettoyage du cache php"
  php -r 'opcache_reset();'
  print_message "INFO" "Activation du mode de maintenance drupal"
  drush state:set system.maintenance_mode 1 --input-format=integer
  print_message "INFO" "Nettoyage du cache drupal"
  drush cr

  drush updatedb -y

  print_message "INFO" "Importation de la configuration dans drupal"
  drush cim --source=${site_workspace}/config/sync/ -y
  drush cim --source=${site_workspace}/config/sync/ -y

  print_message "INFO" "Mise a jour de la version courante dans l'indicateur d'environnement drupal"
  drush sset environment_indicator.current_release "j-${git_branch}"

  # On met a jour les locales avec drush
  print_message "INFO" "Mise a jour de la gestion des langues dans drupal"
  drush locale-check
  drush locale-update

  print_message "INFO" "Nettoyage du cache"
  drush cr
  print_message "INFO" "Desactivation du mode de maintenance drupal"
  drush state:set system.maintenance_mode 0 --input-format=integer
}

# main process
if validate; then
  print_message "INFO" "Appel de la librairie ${site_path}/scripts/utils_library.sh."
  source "${site_path}/scripts/utils_library.sh"
  if ${is_deployment}; then
    print_message "INFO" "Creation des liens symboliques ..."
    createSymbolicLinks
  fi
  print_message "INFO" "Creation des assets dans vendor ..."
  setAssetsIntoVendor
  print_message "INFO" "Mise en place du systeme SAML ..."
  createSamlSystem
  print_message "INFO" "Execution de drush ..."
  drushExecutions
fi
