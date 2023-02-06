#!/usr/bin/env bash
#    .--.
#   |o_o |
#   |:_/ |
#  //   \ \
# (|     | )
#/'\_   _/`\
#\___)=(___/
#+-------------------------------------------+
#| Script appeler en premier par Jenkins(r)  |
#| @author DBX <didier.beux@etat.ge.ch>      |
#| @since  jeu. 11 août 2022 15:40:58        |
#+-------------------------------------------+

# Emplacement du site dans le serveur
site_workspace="$1"
# Nom de la branche a deployer
git_branch="$2"
# Nom de l'utilisateur git
git_user="$3"
# Passe technique de l'utilisateur
git_pwd="$4"
# Deploiement DEPLOYMENT
is_deployment=$5

# appelle des librairies utiles
source ${site_workspace}/scripts/utils_library.sh

if [[ "${is_deployment}" == "" ]] ||
  [[ "${is_deployment}" == "false" ]]; then
  type_deployment='PACKAGING'
else
  type_deployment='DEPLOYMENT'
fi

function print_help() {
  echo "Help"
  echo
  echo "Type jenkins <site_workspace> <git_branch> <git_user> <git_pwd> [<is_deployment>]"
  echo "parameters :"
  echo "   - site_workspace : emplacement du site dans le serveur."
  echo "   - git_branch     : nom de la branche a deployer."
  echo "   - git_user       : nom de l'utilisateur technique."
  echo "   - git_pwd        : mot de passe technique."
  echo "   - is_deployment    : Type de deploiement. Par defaut, la valeur est false"
}

function validate() {
  res=0
  message=''
  if [[ "${site_workspace}" == "" ]] ||
    [[ "${git_branch}" == "" ]] ||
    [[ "${git_user}" == "" ]] ||
    [[ "${git_pwd}" == "" ]]; then
    res=1
  fi
  return ${res}
}

function init_deployment() {
  print_message "INFO" "Test si le reperoire existe."
  if [[ -d ${deployment_path} ]]; then
    print_message "INFO" "Test si le Repertoire de deploiement appartient a jenkinsadmin."
    # Utilisateur prorietaire du repertoire de deploiement
    owner_deployment_path=$(stat --format '%U' "${deployment_path}")
    if [ "${owner_deployment_path}" != "jenkinsadmin" ]; then
      print_message "INFO" "S'il n'appartient pas a jenkinsadmin, on le fait appartenir."
      sudo /bin/chown -R jenkinsadmin ${deployment_path}
    fi
    print_message "INFO" "Test si le repertoire de la branche a deployer existe deja."
    if [[ -d "${deployment_path}/${git_branch}" ]]; then
      print_message "INFO" "Si elle existe on la detruit"
      # shellcheck disable=SC2115
      rm -rf "${deployment_path}/${git_branch}"
    fi
  else
    print_message "INFO" "Si le repertoire de deploiement n'existe pas on le cree."
    mkdir ${deployment_path}
  fi
}

# ${git_url} se trouve dans ${site_workspace}/scripts/utils_library.sh
function deploy_to_deployment_path() {
  print_message "INFO" "On cree le repertoire de la branche a deployer."
  mkdir "${deployment_path}/${git_branch}"
  print_message "INFO" "On recupere le projet depuis git dans le repertoire de deploiement."
  # shellcheck disable=SC2086
  # shellcheck disable=SC2154
  git clone -b "${git_branch}" \
    -c http.sslVerify=false \
    -c core.symlinks=true \
    https://${git_user}:${git_pwd}@"${git_url}" "${deployment_path}/${git_branch}"
}

# main process
if validate; then
  # Repertoire de deploiement du projet depuis git
  if [[ "${type_deployment}" == "DEPLOYMENT" ]]; then
    deployment_path="${site_workspace}/branch"
    print_message "INFO" "Deploiement DEPLOYMENT le repertoire de deploiement est ${deployment_path}"
  elif [[ "${type_deployment}" == "PACKAGING" ]]; then
    deployment_path="${site_workspace}/build"
    print_message "INFO" "Deploiement PACKAGING le repertoire de deploiement est ${deployment_path}"
  fi
  print_message "INFO" "Initialisation du deploiement."
  init_deployment
  print_message "INFO" "Deploiement."
  deploy_to_deployment_path
else
  print_message "ERREUR" "Deploiement impossible."
  print_help
  if [[ "${site_workspace}" == "" ]]; then
    message="${message}le parametre site_workspace est manquant\n"
  fi
  if [[ "${git_branch}" == "" ]]; then
    message="${message}le parametre git_branch est manquant\n"
  fi
  if [[ "${git_user}" == "" ]]; then
    message="${message}le parametre git_user est manquant\n"
  fi
  if [[ "${git_pwd}" == "" ]]; then
    message="${message}le parametre git_pwd est manquant\n"
  fi
  if [[ "${message}" != "" ]]; then
    echo -en "${message}"
  fi
fi
