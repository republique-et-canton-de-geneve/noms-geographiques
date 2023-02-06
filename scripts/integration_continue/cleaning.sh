#!/usr/bin/env bash
#    .--.
#   |o_o |
#   |:_/ |
#  //   \ \
# (|     | )
#/'\_   _/`\
#\___)=(___/
#+-------------------------------------------+
#| Script de nettoyage du site               |
#| @author DBX <didier.beux@etat.ge.ch>      |
#| @since  jeu. 11 août 2022 14:41:26        |
#+-------------------------------------------+
# Chemin du site dans le serveur.
site_workspace="$1"
# Chemin de l'executable php dans le serveur.
php_path="$2"
# Nom de la branche a deployer.
git_branch="$3"
# Path jusqu'au site livré.
site_path=""

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



function updateUserJenkinsAdminOnProject() {
  print_message "INFO" "Applique jenkinsadmin comme owner des liens symboliques s'ils existent."
  if [[ -L ${site_workspace}/branch ]]; then
    print_message "INFO" "Applique jenkinsadmin sur le lien dymamique ${site_workspace}/branch."
    sudo /bin/chown -R jenkinsadmin ${site_workspace}/branch
  fi
  if [[ -L ${site_workspace}/htdocs ]]; then
    print_message "INFO" "Applique jenkinsadmin sur le lien dymamique ${site_workspace}/htdocs."
    sudo /bin/chown -R jenkinsadmin ${site_workspace}/htdocs
  fi
  if [[ -L ${site_workspace}/tests ]]; then
    print_message "INFO" "Applique jenkinsadmin sur le lien dymamique ${site_workspace}/tests."
    sudo /bin/chown -R jenkinsadmin ${site_workspace}/tests
  fi
  if [[ -L ${site_workspace}/vendor ]]; then
    print_message "INFO" "Applique jenkinsadmin sur le lien dymamique ${site_workspace}/vendor."
    sudo /bin/chown -R jenkinsadmin ${site_workspace}/vendor
  fi
  if [[ -L ${site_workspace}/private-files ]]; then
    print_message "INFO" "Applique jenkinsadmin sur le lien dymamique ${site_workspace}/private-files."
    sudo /bin/chown -R jenkinsadmin ${site_workspace}/private-files
  fi
  if [[ -L ${site_workspace}/tmp ]]; then
   print_message "INFO" "Applique jenkinsadmin sur le lien dymamique ${site_workspace}/tmp."
   sudo /bin/chown -R jenkinsadmin ${site_workspace}/tmp
  fi
  if [[ -L ${site_workspace}/sync ]]; then
    print_message "INFO" "Applique jenkinsadmin sur le lien dymamique ${site_workspace}/config/sync."
    sudo /bin/chown -R jenkinsadmin ${site_workspace}/config/sync
  fi
}

function removeAllPreviousDeployment() {
  print_message "INFO" "Suppression de tous les liens symboliques s'ils existent."
  if [[ -L ${site_workspace}/htdocs ]]; then
    print_message "INFO" "Suppression du lien dymamique ${site_workspace}/htdocs."
    rm ${site_workspace}/htdocs
  fi

  if [[ -L ${site_workspace}/tests ]]; then
    print_message "INFO" "Suppression du lien dymamique ${site_workspace}/tests."
    rm ${site_workspace}/tests
  fi

  if [[ -L ${site_workspace}/vendor ]]; then
    print_message "INFO" "Suppression du lien dymamique ${site_workspace}/vendor."
    rm ${site_workspace}/vendor
  fi

  if [[ -L ${site_workspace}/tmp ]]; then
    print_message "INFO" "Suppression du lien dymamique ${site_workspace}/tmp."
    rm ${site_workspace}/tmp
  fi

  if [[ -L ${site_workspace}/config/sync ]]; then
    print_message "INFO" "Suppression du lien dymamique ${site_workspace}/sync."
    rm ${site_workspace}/config/sync
  fi

  if [[ -L ${site_workspace}/scripts ]]; then
    print_message "INFO" "Suppression du lien dymamique ${site_workspace}/scripts."
    rm ${site_workspace}/scripts
  fi

}

# main process
if validate; then
   print_message "INFO" "Appel de la librairie ${site_path}/scripts/utils_library.sh."
   source "${site_path}/scripts/utils_library.sh"
   print_message "INFO" "Donne les permissions à jenkinsadmin."
   updateUserJenkinsAdminOnProject
   print_message "INFO" "Supprime le dernier déploiement."
   removeAllPreviousDeployment
fi