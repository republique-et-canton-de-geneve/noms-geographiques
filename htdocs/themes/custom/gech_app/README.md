# Thème - État de Genève - ge.ch App 

Le theme a été implémenté de manière à ce que Gulp fonctionne depuis le conteneur node de la pile de conteneur docker4drupal sur un poste de travail PTE.

Liens utiles:
- [Bootstrap 5.2](https://getbootstrap.com/docs/5.2)
- docker4drupal sur un poste PTE
- [docker4drupal](https://github.com/wodby/docker4drupal)

### Développement

Normalement, le conteneur `node` est lancé par Docker Desktop.
On se retrouve directement dans le répertoire du thème Drupal depuis le container.
On peut alors lancer le script d'initialisation de node en mode source.
```bash
$ . /var/www/html/docker/init.sh
```

Une fois Gulp installé, lancer la commande suivante:
```bash
$ gulp
```
Elle compile les fichiers CSS/JS et ensuite observe les fichiers pour compiler lors d'un changement.
Le rafraichissement du navigateur n'est pas automatique.
**C'est la commande à utiliser lors du développement.**

Terminer la tâche Gulp avec `Ctrl-c`.


Pour compiler seulement le CSS (sans `watch`):
```bash
$ gulp sass
```

Pour compiler seulement le JS (sans `watch`):
```bash
$ gulp js
```

## Configuration pour le développement du thème (no cache)

Ajouter dans le fichier `settings.local.php` :
```
$settings['extension_discovery_scan_tests'] = FALSE;
$settings['container_yamls'][] = DRUPAL_ROOT . '/../config/development.services.yml';
```

Et ajouter dans le fichier `development.services.yml` (présent dans le dossier config) :
```
parameters:
  http.response.debug_cacheability_headers: true
  twig.config:
    debug: true
    auto_reload: true
    cache: false
services:
  cache.backend.null:
    class: Drupal\Core\Cache\NullBackendFactory
```
Permet d'activer Twig Debug, de désactiver le cache Twig ainsi que la compression de fichiers.

## Clés pour le développement front

### Général
- "Est-ce j'ai meilleur temps de":
  - surcharger un template (pour donner les bonnes classes CSS existantes) ?
  - utiliser un hook preprocess (pour donner les bonnes classes CSS existantes, si une logique est nécessaire) ?
  - utiliser du SASS (car aucune classe CSS ne fait le travail) ?

### SASS
- "Est-ce que Bootstrap propose cet élément ?"
    - "Si oui, est-ce que j'ai besoin de surcharger une variable SASS (pour configurer l'élément à travers tout le projet) ?"

### Javascript
- Est-ce que mon JS est utilisé partout dans le site (=> buildé dans le scripts.min.js) ou 
  est-ce qu'il est utilisé qu'à certains endroits (=> créer une librairie et l'appeler dans le Twig ou le PHP).

## A savoir

La dépendance `@popperjs/core@2.11.6` de Bootstrap n'est pas disponible sur yarn (en date du 19.08.2022).
Elle n'est donc pas utilisée. Exemple: la goutte du header utilise le "Collapse" de Bootstrap.

---

Le dossier `gech` de sass ou de templates **ne doit pas être modifié**.
Pour tout nouvel ajout ou surchage, le faire dans le fichier adéquat des autres dossiers.

---

Lorsque Gulp tourne, il produit ce type d'avertissements:
```bash
Deprecation Warning: Using / for division outside of calc() is deprecated and will be removed in Dart Sass 2.0.0.
```
Il y a une issue sur Bootstrap sur ce point-là. On pourrait (encore) redescendre la version de Sass sinon.
Le Gulp tourne bien lorsque on voit ces lignes bleues:
```bash
[Browsersync] Reloading Browsers...
[Browsersync] Reloading Browsers... (buffered 16 events)
```


## Résolution de problèmes

Lancer le container `node` avec la commande:
```bash
$ docker-compose -p ngeo exec node sh
```

Si Gulp (ou autre) ne serait pas installé, entrer la ligne de commande suivante pour installer des dépendances manquantes:*
```bash
yarn install
```
