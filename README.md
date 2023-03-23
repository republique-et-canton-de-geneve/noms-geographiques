># NGEO
>Un répertoire collaboratif des noms de rues du canton de Genève.
>.

Analyse SonarCloud :

[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=republique-et-canton-de-geneve_noms-geographiques&metric=bugs)](https://sonarcloud.io/dashboard?id=republique-et-canton-de-geneve_noms-geographiques)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=republique-et-canton-de-geneve_noms-geographiques&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=republique-et-canton-de-geneve_noms-geographiques)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=republique-et-canton-de-geneve_noms-geographiques&metric=code_smells)](https://sonarcloud.io/dashboard?id=republique-et-canton-de-geneve_noms-geographiques)
[![Duplicated Lines (%)](https://sonarcloud.io/api/project_badges/measure?project=republique-et-canton-de-geneve_noms-geographiques&metric=duplicated_lines_density)](https://sonarcloud.io/dashboard?id=republique-et-canton-de-geneve_noms-geographiques)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=republique-et-canton-de-geneve_noms-geographiques&metric=coverage)](https://sonarcloud.io/dashboard?id=republique-et-canton-de-geneve_noms-geographiques)

Licence :

[![License: AGPL v3](https://img.shields.io/badge/License-AGPL%20v3-blue.svg)](https://www.gnu.org/licenses/why-affero-gpl.html)

![Page d'accueil](images/Page_d_accueil.png)

## Prérequis
Docker ou Docker desktop.

## Installation
Installation basée sur la ligne de commande PowerShell, à l'exception des commandes git faites via Git Bash.

### Récupérer le projet Git
Copier l'URL dans Github :

![Github clone project](images/clone_project.png)

Cloner le projet chez soi :
```
$ git clone https://github.com/republique-et-canton-de-geneve/noms-geographiques.git
```

### Préparer l'environnement local
Modifier le fichier `hosts` de la machine hôte (C:\Windows\System32\drivers\etc\hosts) :
```
127.0.0.1  noms-geographiques.local.ge.ch
127.0.0.1  cmsadmin-noms-geographiques.local.ge.ch
127.0.0.1  portainer.ngeo.local.ge.ch
127.0.0.1  pma.noms-geographiques.local.ge.ch
127.0.0.1  webgrind.ngeo.local.ge.ch
127.0.0.1  mailhog.noms-geographiques.local.ge.ch
127.0.0.1  idp.ngeo.local.ge.ch
```

Lancer le projet Docker :
```sh
cd 10677-ngeo/docker
docker-compose -p ngeo up -d
```

Se connecter au conteneur PHP :
```bash
cd 10677-ngeo/docker
docker exec -it ngeo_php /bin/sh
```
### Initialisation Drupal
Aller voir le site :
En Anonyme:
   http://noms-geographiques.local.ge.ch:8099/
Mode Connecté :
   http://cmsadmin-noms-geographiques.local.ge.ch:8099/
=> les comptes SAML locaux sont décrits dans le fichier `{project_root}/docker/comptes_locaux_saml.php`

## Composer
Exécuter les commandes composer souhaitées. Ex :
```bash
# pour lancer l'installation des 'vendor' qui ne seraient pas présents :
$ composer install

# pour ajouter un module drupal contrib ou autre composant (--dev si c'est un composant utile au développement, ex devel)
$ composer require drupal/mon-module

# avoir un statut sur les composants drupal
$ composer outdated 'drupal/*'

# faire une mise à jour => cf les bonnes pratiques de mise à jour (todo wiki ?)
$ composer update drupal/mon-module-a-mettre-a-jour
```

# Développement NGEO : bon à savoir

## Titres de page
Pour ne pas utiliser le bloc de titre proposé de base dans les différentes pages :
1. Aller sur : Structure > Mise en page des blocs.
2. Bloc "Titre de page": configurer.
3. Dépend du type de page, mais paramétrer ici pour le désactiver. Exemple : /search => aller sur Page.
4. Sauvegarder.


# Vérification du code

## PHPCS
PHP CodeSniffer sert à vérifier si les standards de code Drupal sont respectés.

1. Ajout de Coder via composer :
    ```
    composer update --prefer-source drupal/coder
    ```
2. Ajouter les dossiers pour les standards de code Drupal :
    ```
    phpcs --config-set installed_paths /var/www/html/vendor/drupal/coder/coder_sniffer,/var/www/html/vendor/sirbrillig/phpcs-variable-analysis,/var/www/html/vendor/slevomat/coding-standard
    ```
3. Tester si tout est bien configuré :
    ```
    php vendor/bin/phpcs -i
    ```
4. Tester le code (ici : dans le module 'ngeo_core') :
    ```
    php vendor/bin/phpcs --standard=Drupal,DrupalPractice htdocs/modules/custom/ngeo_core
    ```
5. Corriger le code automatiquement avec phpcbf (ici : dans le module 'ngeo_core') :
   ```
   php vendor/bin/phpcbf --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md htdocs/modules/custom/ngeo_core

## Contributeurs
| Avatar                                           | e-mail adress            |
|--------------------------------------------------|--------------------------|
| ![Didier.Beux](images/beux.didier.32x32.png)     | didier.beux@etat.ge.ch   |
| ![Auriana.Hug](images/hug.auriana.32x32.png)     | auriana.hug@etat.ge.ch   |
| ![Benjamin.Roy](images/roy.benjamin.32x32.png)   | benjamin.roy@etat.ge.ch  |
| ![Kévin.Notari](images/notari.kevin.32x32.png)   | kevin.notary@etat.ge.ch  |
| ![Zeqiri.Erblin](images/zeqiri.erblin.32x32.png) | erblin.zeqiri@etat.ge.ch |
