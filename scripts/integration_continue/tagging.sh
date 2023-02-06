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
#| @since  jeu. 11 août 2022 14:41:26        |
#+-------------------------------------------+
# Chemin du site dans le serveur.
site_workspace="$1"
# Chemin de l'executable php dans le serveur.
php_path="$2"
# Nom de la branche a deployer.
git_branch="$3"
# Repository du projet dans Nexus.
git_user="$4"
# Chemin jusqu'au repository dans Nexus.
git_pwd="$5"
# Version to deploye
version="$6"

# Chemin complete du site.
site_path=""

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
  site_path="${site_workspace}/build/${git_branch}"
  return 0
}

# ${git_url} se trouve dans ${site_workspace}/scripts/utils_library.sh
function tag_branch() {
  print_message "INFO" "Changement de répertoire dans ${site_path}"
  # shellcheck disable=SC2164
  cd "${site_path}"
  print_message "INFO" "Assignation de l'utilisateur ${git_user} dans git"
  git config user.name "${git_user}"
  print_message "INFO" "Mise à jour de la config dans git"
  git push https://${git_user}:${git_pwd}@${git_url} --all
  print_message "INFO" "Creation du tag ${version} dans git avec le message jenkins packaging ${version}"
  git tag -a ${version} -m "jenkins packaging ${version}"
  print_message "INFO" "Push du tag ${version} dans l'origin"
  git push origin ${version}
}

# main process
if validate; then
  # shellcheck disable=SC1090
  source "${site_path}/scripts/utils_library.sh"
  print_message "INFO" "On tag la branche ..."
  tag_branch
fi
