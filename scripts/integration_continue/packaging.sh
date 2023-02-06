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
site_workspace="$1"
# Chemin de l'executable php dans le serveur.
php_path="$2"
# Nom de la branche a deployer.
git_branch="$3"
# Repository du projet dans Nexus.
artifact_id="$4"
# Chemin jusqu'au repository dans Nexus.
artifact_group_id="$5"
# Version to deploye
version="$6"
# Do you want to keep archive ? true/false
keep_archive=$7

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

function init_building() {
  if [[ -d "${site_path}" ]]; then
    # shellcheck disable=SC2164
    cd "${site_path}"
    print_message "INFO" "Supprime tous les fichiers *.sql."
    find . -type f -name '*.sql' -exec rm {} \;
    print_message "INFO" "Supprime tous les fichiers *.md."
    find . -type f -name '*.md' -exec rm {} \;
    print_message "INFO" "Supprime tous les fichiers *.txt."
    find . -type f -name '*.txt' -exec rm {} \;
    print_message "INFO" "Supprime tous les répertoires .git."
    find . -type d -name ".git" -exec rm -rf {} \;
    print_message "INFO" "Supprime tous les répertoires tests."
    find . -type d -name "tests" -exec rm -rf {} \;
    print_message "INFO" "Supprime tous les répertoires ci."
    find . -type d -name "ci" -exec rm -rf {} \;
    print_message "INFO" "Supprime le contenu du répertoire de config."
    rm -rf config/saml/*
    rm config/settings.local.dev.example.php config/settings.local.example.php config/development.services.yml
    print_message "INFO" "Supprime le contenu du répertoire de scripts, on ne garde que utils_library.sh."
    rm -rf scripts/integration_continue/*
    rm scripts/jenkins.sh scripts/migration.sh scripts/A\ copier\ dans\ le\ serveur\ de\ DEV
    print_message "INFO" "Supprime le dossier images."
    rm -rf images
  fi
}

function create_package() {
  # shellcheck disable=SC2164
  if [ -z "${version}" ]; then
    print_message "INFO" "La version est vide, c'est le nom de la branche qui sera pris en compte."
    version=${git_branch}
  fi
  if [[ -d "${site_path}" ]]; then
    # shellcheck disable=SC2164
    cd "${site_path}"
  fi
  print_message "INFO" "création du fichier release.properties dans htdocs."
  echo "VERSION=${artifact_id}_v${version}" >htdocs/release.properties
  print_message "INFO" "Création de l'archive ${artifact_id}-${version}.tgz"
  tar -czvf "${site_workspace}/build/${artifact_id}-${version}.tgz" .
  if [[ -f "${site_workspace}/build/${artifact_id}-${version}.tgz" ]]; then
    print_message "INFO" "Archive ${artifact_id}-${version}.tgz créée."
  fi
}

function create_metadatas() {
  if [[ -d "${site_workspace}"/build ]]; then
    # shellcheck disable=SC2164
    cd "${site_workspace}"/build
  fi
  print_message "INFO" "Création du fichier maven-metadata.xml."
  touch maven-metadata.xml
  echo "<metadata>" >>maven-metadata.xml
  echo "  <groupId>${artifact_group_id}</groupId>" >>maven-metadata.xml
  echo "  <artifactId>${artifact_id}</artifactId>" >>maven-metadata.xml
  echo "  <version>${version}</version>" >>maven-metadata.xml
  echo "  <versioning>" >>maven-metadata.xml
  echo "    <latest>${version}</latest>" >>maven-metadata.xml
  echo "    <release>${version}</release>" >>maven-metadata.xml
  echo "    <versions>" >>maven-metadata.xml
  echo "      <version>${version}</version>" >>maven-metadata.xml
  echo "    </versions>" >>maven-metadata.xml
  echo "    <lastUpdated/>" >>maven-metadata.xml
  echo "  </versioning>" >>maven-metadata.xml
  echo "</metadata>" >>maven-metadata.xml
  if [[ -f maven-metadata.xml ]]; then
    print_message "INFO" "Fichier metadata  maven-metadata.xml créée."
  fi
}

function create_checksum_files() {
  if [[ -d "${site_workspace}"/build ]]; then
    # shellcheck disable=SC2164
    cd "${site_workspace}"/build
  fi
  print_message "INFO" "Création du fichier maven-metadata.xml.md5."
  md5sum maven-metadata.xml >maven-metadata.xml.md5
  if [[ -f maven-metadata.xml.md5 ]]; then
    print_message "INFO" "fichier checksum maven-metadata.xml.md5 créé."
  fi
  print_message "INFO" "Création du fichier ${artifact_id}-${version}.tgz.md5."
  md5sum ${artifact_id}-${version}.tgz >${artifact_id}-${version}.tgz.md5
  if [[ -f ${artifact_id}-${version}.tgz.md5 ]]; then
    print_message "INFO" "fichier ${artifact_id}-${version}.tgz.md5 créé."
  fi
}

# ${nexus_url} se trouve dans ${site_workspace}/scripts/utils_library.sh
function send_package_and_checksums_to_nexus() {
  if [[ -d "${site_workspace}"/build ]]; then
    # shellcheck disable=SC2164
    cd "${site_workspace}"/build
  fi
  print_message "INFO" "Envois du package dans Nexus."
  repo=${artifact_group_id//[\.]//}
  # shellcheck disable=SC2154
  url="${nexus_url}/${repo}/${artifact_id}"
  print_message "INFO" "Envois du package ${artifact_id}-${version}.tgz dans Nexus vers ${url}/${version}."
  curl -v --insecure -u PHP_DEPLOYER:Hyper2018 --upload-file ${artifact_id}-${version}.tgz ${url}/${version}/${artifact_id}-${version}.tgz
  print_message "INFO" "Envois du checksum ${artifact_id}-${version}.tgz.md5 dans Nexus vers ${url}/${version}."
  curl -v --insecure -u PHP_DEPLOYER:Hyper2018 --upload-file ${artifact_id}-${version}.tgz.md5 ${url}/${version}/${artifact_id}-${version}.tgz.md5
  print_message "INFO" "Envois du metadata maven-metadata.xml dans Nexus vers ${url}/${version}."
  curl -v --insecure -u PHP_DEPLOYER:Hyper2018 --upload-file maven-metadata.xml ${url}/maven-metadata.xml
  print_message "INFO" "Envois du checksum maven-metadata.xml.md5 dans Nexus vers ${url}/${version}."
  curl -v --insecure -u PHP_DEPLOYER:Hyper2018 --upload-file maven-metadata.xml.md5 ${url}/maven-metadata.xml.md5
}

function cleaning_build() {
  if [[ -d "${site_workspace}"/build ]]; then
    # shellcheck disable=SC2164
    cd "${site_workspace}"/build
    print_message "INFO" "Suppressiom des fichiers ${artifact_id}-${version}.tgz ${artifact_id}-${version}.tgz.md5 maven-metadata.xml et maven-metadata.xml.md5."
    rm ${artifact_id}-${version}.tgz ${artifact_id}-${version}.tgz.md5 maven-metadata.xml maven-metadata.xml.md5
    print_message "INFO" "Suppressiom de la branche ${git_branch} dans build."
    rm -rf "${git_branch}"
  fi
}

# main process
if validate; then
  source "${site_path}/scripts/utils_library.sh"
  print_message "INFO" "Nettoyage de la branche avant archivage."
  init_building
  print_message "INFO" "Creation du package."
  create_package
  print_message "INFO" "Creation des metadatas."
  create_metadatas
  print_message "INFO" "Calcule et creation des fichiers checksums."
  create_checksum_files
  print_message "INFO" "Envoi des fichiers à Nexus."
  send_package_and_checksums_to_nexus
  if ! ${keep_archive}; then
    print_message "INFO" "Suppression de la branche."
    cleaning_build
  fi
fi
