# Tests behat

NB : L'ensemble des commandes contenus dans ce README doivent être exécutées depuis les container php

## Configuration locale
- Dupliquer le fichier `tests/behat.local.yml.example` dans le même répertoire `tests` et le renommer en `behat.local.yml`
```
cp tests/behat.local.yml.example tests/behat.local.yml
```

- Créer le dossier `tests/failed` qui recevra les captures lors d'erreur de tests behat.
```
mkdir -p tests/failed
```

## Dossiers des tests
Les tests se trouvent dans le dossier `tests/features`.

## Exécution des tests en local

- Lancer les tests behat (depuis le répertoire racine du projet) :
```
php vendor/bin/behat --config tests/behat.yml
```

- Lancer un/des test(s) défini(s) par un tag :
```
php vendor/bin/behat --config tests/behat.yml --tags '@blog'
```

- Lancer un test via un mot dans "Feature" ou "Scenario" :
```
php vendor/bin/behat --config tests/behat.yml --name 'motClé'
```

- Lister les commandes behat disponible :

```
php vendor/bin/behat -dl --config tests/behat.yml
```

## Erreurs courantes

- Behat introuvable:
Si lors du lancement des tests vous avez l'erreur suivante :
```
Could not open input file: vendor/bin/behat
```
=> lancer le script `docker/init.sh` qui ajoutera le lien symbolique behat dans `vendor/bin`.

=> avec les nouveaux conteneurs build lancer la commande `composer install`qui ajoutera le lien symbolique behat dans `vendor/bin`.

- Le test affiche:
```
The provided host name is not valid for this server.
```
=> vérifier que `apache` est bien dans les `trusted_host_patterns` du fichier de settings.local.php

- Des erreurs apparaissent:
```
Form field with id|name|label|value|placeholder "Mot de passe" not found.
```
C'est que notre environnement n'a pas toutes les traductions.
Lancer alors depuis le container php:
```
drush locale:check
drush locale:update
```
